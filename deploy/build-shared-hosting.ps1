<#
Builds an upload-ready zip for free PHP shared hosting (no SSH, no Composer on the server),
for example InfinityFree. The zip goes straight into the host's "htdocs" folder.

Usage (from the project folder):
    .\deploy\build-shared-hosting.ps1 -Url https://yoursite.infinityfreeapp.com -Php 8.3

  -Url       Your live site address (exactly as visitors will type it).
  -Php       The PHP version your host runs (8.3, 8.4, 8.5). Packages are resolved to match it.
  -Composer  Path to composer.phar (downloaded automatically if missing).
  -CodeOnly  For UPDATES after the site is live: leaves out .env, the database and uploads,
             so the content you edit on the live admin panel is never overwritten.

Output: deploy\dist\portfolio-upload.zip
#>
param(
    [Parameter(Mandatory)] [string] $Url,
    [string] $Php = '8.3',
    [string] $Composer,
    [switch] $CodeOnly
)
$ErrorActionPreference = 'Stop'
$root  = Split-Path $PSScriptRoot -Parent
$stage = Join-Path $env:TEMP 'portfolio-stage'
$dist  = Join-Path $PSScriptRoot 'dist'
$zip   = Join-Path $dist 'portfolio-upload.zip'
$Url   = $Url.TrimEnd('/')

function Invoke-Step($msg, [scriptblock] $block) {
    Write-Host "==> $msg" -ForegroundColor Cyan
    # Native tools write warnings to stderr; judge success by exit code instead.
    $ErrorActionPreference = 'Continue'
    $global:LASTEXITCODE = 0
    & $block
    if ($LASTEXITCODE -and $LASTEXITCODE -ne 0) { throw "Step failed: $msg (exit $LASTEXITCODE)" }
}

if (-not $Composer) {
    $Composer = Join-Path $env:TEMP 'composer.phar'
    if (-not (Test-Path $Composer)) {
        Write-Host '==> Downloading composer.phar' -ForegroundColor Cyan
        Invoke-WebRequest https://getcomposer.org/download/latest-stable/composer.phar -OutFile $Composer -UseBasicParsing
    }
}

Push-Location $root
try {
    Invoke-Step 'Building CSS/JS (npm run build)' { npm run build }

    Invoke-Step 'Copying project to a clean staging folder' {
        if (Test-Path $stage) { Remove-Item $stage -Recurse -Force }
        $xd = @('node_modules', 'vendor', '.git', '.idea', '.vscode', '.claude', (Join-Path $root 'deploy\dist'),
                (Join-Path $root 'storage\logs'), (Join-Path $root 'storage\framework'), (Join-Path $root 'bootstrap\cache'))
        $xf = @('.env', '.env.backup', 'hot', '*.log', '.phpunit.result.cache')
        if ($CodeOnly) { $xd += (Join-Path $root 'public\uploads'); $xf += 'database.sqlite' }
        robocopy $root $stage /E /NFL /NDL /NJH /NJS /NP /XD @xd /XF @xf | Out-Null
        if ($LASTEXITCODE -ge 8) { throw "robocopy failed ($LASTEXITCODE)" }
        $global:LASTEXITCODE = 0
        foreach ($d in 'storage\framework\cache\data', 'storage\framework\sessions', 'storage\framework\views',
                       'storage\logs', 'bootstrap\cache', 'public\uploads') {
            New-Item -ItemType Directory -Force (Join-Path $stage $d) | Out-Null
        }
    }

    Invoke-Step 'Writing production .env' {
        $key = (php artisan key:generate --show).Trim()
        $envText = Get-Content (Join-Path $PSScriptRoot '.env.production') -Raw
        $envText = $envText -replace '(?m)^APP_KEY=.*$', "APP_KEY=$key" -replace '(?m)^APP_URL=.*$', "APP_URL=$Url"
        if ($Url.StartsWith('https://')) { $envText += "SESSION_SECURE_COOKIE=true`n" }
        [IO.File]::WriteAllText((Join-Path $stage '.env'), $envText.Replace("`r`n", "`n"))
    }

    Push-Location $stage
    try {
        Invoke-Step "Installing production packages for PHP $Php" {
            php $Composer config platform.php "$Php.0" --no-interaction
            php $Composer update --no-dev --with-all-dependencies --optimize-autoloader --no-interaction --no-progress --no-scripts
            php $Composer dump-autoload --no-dev --optimize --no-interaction
            php artisan package:discover --ansi
        }
        Invoke-Step 'Checking the app boots' { php artisan about --only=environment | Out-Null }
    } finally { Pop-Location }

    if ($CodeOnly) {
        # The live server keeps its own .env (same APP_KEY), database and uploads.
        Remove-Item (Join-Path $stage '.env') -Force
        Remove-Item (Join-Path $stage 'public\uploads') -Recurse -Force
    }

    Invoke-Step 'Adding root .htaccess (sends every request into public/)' {
        $ht = @'
# Laravel lives in this folder; the real web root is public/.
# Everything else (.env, database, vendor, ...) is never served directly.
Options -Indexes
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
<FilesMatch "^\.|\.(sqlite|env|log|sh|ps1)$">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Deny from all
    </IfModule>
</FilesMatch>
'@
        [IO.File]::WriteAllText((Join-Path $stage '.htaccess'), $ht.Replace("`r`n", "`n"))
        # Block direct access to stray PHP files in the uploads folder.
        if (Test-Path (Join-Path $stage 'public\uploads')) { [IO.File]::WriteAllText((Join-Path $stage 'public\uploads\.htaccess'),
            "<FilesMatch `"\.(php|phtml|phar)`$`">`n    Require all denied`n</FilesMatch>`n") }
        Remove-Item (Join-Path $stage 'deploy') -Recurse -Force
    }

    Invoke-Step 'Creating zip' {
        New-Item -ItemType Directory -Force $dist | Out-Null
        if (Test-Path $zip) { Remove-Item $zip -Force }
        & "$env:SystemRoot\System32\tar.exe" -a -c -f $zip -C $stage .
    }

    $mb = [math]::Round((Get-Item $zip).Length / 1MB, 1)
    $files = (Get-ChildItem $stage -Recurse -File -Force).Count
    Write-Host "`nDone: $zip ($mb MB, $files files)" -ForegroundColor Green
    Write-Host 'Upload it to htdocs and extract it there (see deploy\HOSTING.md).'
} finally { Pop-Location }
