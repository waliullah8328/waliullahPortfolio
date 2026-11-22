<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ExperienceTest extends TestCase
{
    use RefreshDatabase;

    private function entry(string $start, ?string $end, bool $current = false): Experience
    {
        return new Experience(['start_date' => $start, 'end_date' => $end, 'is_current' => $current]);
    }

    public function test_total_months_counts_overlaps_once_and_skips_gaps(): void
    {
        $this->assertSame(17, Experience::totalMonths([$this->entry('2024-12-01', '2026-04-30')]));
        $this->assertSame(19, Experience::totalMonths([$this->entry('2024-12-01', '2026-04-30'), $this->entry('2026-03-01', '2026-06-30')]));
        $this->assertSame(18, Experience::totalMonths([$this->entry('2022-01-01', '2022-12-31'), $this->entry('2024-01-01', '2024-06-30')]));
        $this->assertSame('2 yrs 1 mo', Experience::humanMonths(25));
    }

    public function test_home_shows_total_experience_work_type_and_address(): void
    {
        Carbon::setTestNow('2026-11-15');

        Experience::create(['type' => 'work', 'title' => 'Senior Flutter Developer', 'organization' => 'Biddabari',
            'location' => 'On Site', 'address' => 'Farmgate, Dhaka 1215', 'start_date' => '2026-05-01', 'is_current' => true]);
        Experience::create(['type' => 'work', 'title' => 'Flutter Developer', 'organization' => 'Betopia Group',
            'location' => 'On Site', 'start_date' => '2024-12-01', 'end_date' => '2026-04-30']);

        $this->get(route('home'))->assertOk()
            ->assertSee('2 yrs')            // Dec 2024 - Nov 2026 = 24 months
            ->assertSee('Farmgate, Dhaka 1215')
            ->assertSee('class="xp-mode">On Site', false);
    }

    public function test_admin_can_save_an_address(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('admin.experiences.store'), [
            'type' => 'work', 'title' => 'Dev', 'organization' => 'Acme', 'location' => 'Remote',
            'address' => '12 Road, Gulshan, Dhaka', 'start_date' => '2025-01-01', 'is_current' => '1',
        ])->assertSessionHasNoErrors();

        $this->assertSame('12 Road, Gulshan, Dhaka', Experience::sole()->address);
    }
}
