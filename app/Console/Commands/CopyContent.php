<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Copies all portfolio content from the local database into another database
 * (for example the Neon Postgres database Vercel gives you), after migrating it.
 *
 *   php artisan portfolio:copy-to "postgresql://user:pass@host/db?sslmode=require"
 */
class CopyContent extends Command
{
    protected $signature = 'portfolio:copy-to
        {url : Target database URL (postgresql://..., mysql://... or sqlite:///path/to/file.sqlite)}
        {--from= : Source connection name (default: the app default connection)}
        {--force : Overwrite target tables without asking}';

    protected $description = 'Migrate a target database and copy all portfolio content into it';

    /** Runtime/framework tables that should not be copied. */
    private const SKIP = ['migrations', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs', 'password_reset_tokens'];

    public function handle(): int
    {
        $source = $this->option('from') ?: config('database.default');
        config(['database.connections.copy_target' => $this->targetConfig($this->argument('url'))]);
        $target = DB::connection('copy_target');

        try {
            $target->getPdo();
        } catch (\Throwable $e) {
            $this->error('Could not connect to the target database: '.$e->getMessage());

            return self::FAILURE;
        }
        $this->info("Connected to target ({$target->getDriverName()}).");

        $this->call('migrate', ['--database' => 'copy_target', '--force' => true]);

        $tables = collect(Schema::connection($source)->getTables())->pluck('name')
            ->reject(fn ($t) => in_array($t, self::SKIP, true) || str_starts_with($t, 'sqlite_'))
            ->filter(fn ($t) => Schema::connection('copy_target')->hasTable($t))
            ->sortBy(fn ($t) => $t === 'users' ? 0 : 1) // users first
            ->values();

        $nonEmpty = $tables->filter(fn ($t) => $target->table($t)->exists());
        if ($nonEmpty->isNotEmpty() && ! $this->option('force')
            && ! $this->confirm('Target already has data in: '.$nonEmpty->join(', ').'. Replace it?', false)) {
            $this->warn('Nothing copied.');

            return self::FAILURE;
        }

        $target->transaction(function () use ($tables, $source, $target) {
            foreach ($tables as $table) {
                $types = collect(Schema::connection('copy_target')->getColumns($table))
                    ->mapWithKeys(fn ($c) => [$c['name'] => strtolower($c['type_name'])]);

                $rows = DB::connection($source)->table($table)->get()
                    ->map(fn ($row) => $this->convert((array) $row, $types->all()));

                $target->table($table)->delete();
                foreach ($rows->chunk(200) as $chunk) {
                    $target->table($table)->insert($chunk->values()->all());
                }

                if ($target->getDriverName() === 'pgsql' && $types->has('id') && str_starts_with($types['id'], 'int')) {
                    $target->statement(
                        "SELECT setval(pg_get_serial_sequence(?, 'id'), COALESCE((SELECT MAX(id) FROM \"{$table}\"), 0) + 1, false)",
                        [$table]
                    );
                }
                $this->line(sprintf('  %-22s %d rows', $table, $rows->count()));
            }
        });

        $this->warnAboutLocalFiles($source);
        $this->info('Done. Your content is now in the target database.');

        return self::SUCCESS;
    }

    /** Keep only columns the target has, and fix types SQLite stores loosely (booleans as 0/1). */
    private function convert(array $row, array $types): array
    {
        $out = [];
        foreach ($row as $column => $value) {
            if (! array_key_exists($column, $types)) {
                continue;
            }
            if ($value !== null && in_array($types[$column], ['bool', 'boolean'], true)) {
                $value = (bool) $value;
            }
            $out[$column] = $value;
        }

        return $out;
    }

    private function targetConfig(string $url): array
    {
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        if ($scheme === 'sqlite') {
            return ['driver' => 'sqlite', 'database' => preg_replace('~^sqlite://~i', '', $url), 'prefix' => '', 'foreign_key_constraints' => true];
        }

        $driver = match ($scheme) {
            'postgres', 'postgresql', 'pgsql' => 'pgsql',
            'mysql', 'mariadb' => $scheme,
            default => throw new \InvalidArgumentException("Unsupported database URL scheme: {$scheme}"),
        };

        return array_merge(config("database.connections.{$driver}"), ['url' => $url]);
    }

    /** Files in public/uploads won't exist on Vercel; list the fields that still point to them. */
    private function warnAboutLocalFiles(string $source): void
    {
        $local = fn ($q, $col) => $q->where($col, 'like', 'uploads/%');
        $found = DB::connection($source)->table('settings')->where('value', 'like', 'uploads/%')->pluck('key')
            ->map(fn ($k) => "Settings → {$k}");

        foreach ([['projects', 'image'], ['projects', 'screen_image'], ['certificates', 'image'], ['posts', 'cover_image'], ['testimonials', 'avatar']] as [$table, $col]) {
            if (Schema::connection($source)->hasColumn($table, $col)) {
                $found = $found->merge($local(DB::connection($source)->table($table), $col)->count()
                    ? ["{$table} ({$col})"] : []);
            }
        }

        if ($found->isNotEmpty()) {
            $this->warn('These still use uploaded files, which will not exist on Vercel. Replace them with links in the admin:');
            $found->each(fn ($f) => $this->line("  - {$f}"));
        }
    }
}
