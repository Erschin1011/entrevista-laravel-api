param(
    [switch] $SkipMigrations
)

$ErrorActionPreference = 'Stop'
$ProjectRoot = Split-Path -Parent $PSScriptRoot
Set-Location -LiteralPath $ProjectRoot

function Require-Command([string] $Name, [string] $InstallHint) {
    if (-not (Get-Command $Name -ErrorAction SilentlyContinue)) {
        throw "$Name no está en PATH. $InstallHint"
    }
}

Require-Command 'php' 'Instala PHP 8.2+ (por ejemplo con XAMPP o PHP oficial) y reinicia PowerShell.'
Require-Command 'composer' 'Instala Composer y reinicia PowerShell.'

$PhpVersionOutput = (& php -v | Select-Object -First 1) -join ''
if ($PhpVersionOutput -notmatch 'PHP\s+(\d+)\.(\d+)\.(\d+)') {
    throw "No se pudo leer la versión de PHP desde: $PhpVersionOutput"
}
$PhpMajor = [int]$Matches[1]
$PhpMinor = [int]$Matches[2]
$PhpVersion = "$PhpMajor.$PhpMinor.$($Matches[3])"
if ($PhpMajor -lt 8 -or ($PhpMajor -eq 8 -and $PhpMinor -lt 2)) {
    throw "Se requiere PHP 8.2 o superior. Se detectó PHP $PhpVersion."
}

$PhpModules = @(& php -m)
if ($PhpModules -notcontains 'pdo_pgsql') {
    throw "Falta pdo_pgsql. Edita el php.ini que indica 'php --ini', habilita extension=pdo_pgsql y reinicia PowerShell."
}

Write-Host '==> Instalando dependencias PHP desde composer.lock' -ForegroundColor Cyan
composer install
if ($LASTEXITCODE -ne 0) { throw 'composer install falló.' }

if (-not (Test-Path -LiteralPath '.env')) {
    Copy-Item -LiteralPath '.env.example' -Destination '.env'
    Write-Host 'Se creó .env a partir de .env.example. Revisa DB_USERNAME y DB_PASSWORD.' -ForegroundColor Yellow
}

$Environment = Get-Content -LiteralPath '.env' -Raw
if ($Environment -notmatch '(?m)^APP_KEY=base64:.+') {
    Write-Host '==> Generando APP_KEY' -ForegroundColor Cyan
    php artisan key:generate
    if ($LASTEXITCODE -ne 0) { throw 'No se pudo generar APP_KEY.' }
}

if (-not $SkipMigrations) {
    $DatabaseHost = '127.0.0.1'
    $DatabasePort = 5432
    if ($Environment -match '(?m)^DB_HOST=(.+)$') { $DatabaseHost = $Matches[1].Trim() }
    if ($Environment -match '(?m)^DB_PORT=(\d+)$') { $DatabasePort = [int]$Matches[1] }

    Write-Host "==> Comprobando PostgreSQL en ${DatabaseHost}:$DatabasePort" -ForegroundColor Cyan
    if (-not (Test-NetConnection -ComputerName $DatabaseHost -Port $DatabasePort -InformationLevel Quiet)) {
        throw "No responde PostgreSQL en ${DatabaseHost}:$DatabasePort. Instala/inicia el servicio y crea la base/usuario (consulta README.md), o vuelve a ejecutar con -SkipMigrations."
    }

    Write-Host '==> Aplicando migraciones' -ForegroundColor Cyan
    php artisan migrate --no-interaction
    if ($LASTEXITCODE -ne 0) { throw 'Las migraciones fallaron; verifica credenciales y permisos de PostgreSQL en .env.' }
}

Write-Host ''
Write-Host 'Listo. Inicia Laravel con: php artisan serve' -ForegroundColor Green
Write-Host 'Abre: http://127.0.0.1:8000' -ForegroundColor Green
