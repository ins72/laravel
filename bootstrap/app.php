<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

// QR Code constants - check if already defined
if (!defined('QR_CACHEABLE')) define('QR_CACHEABLE', false);
if (!defined('QR_CACHE_DIR')) define('QR_CACHE_DIR', false);
if (!defined('QR_LOG_DIR')) define('QR_LOG_DIR', false);
if (!defined('QR_FIND_BEST_MASK')) define('QR_FIND_BEST_MASK', false);
if (!defined('QR_FIND_FROM_RANDOM')) define('QR_FIND_FROM_RANDOM', 2);
if (!defined('QR_DEFAULT_MASK')) define('QR_DEFAULT_MASK', 2);
if (!defined('QR_PNG_MAXIMUM_SIZE')) define('QR_PNG_MAXIMUM_SIZE', 1024);

// Encoding modes
if (!defined('QR_MODE_NUL')) define('QR_MODE_NUL', -1);
if (!defined('QR_MODE_NUM')) define('QR_MODE_NUM', 0);
if (!defined('QR_MODE_AN')) define('QR_MODE_AN', 1);
if (!defined('QR_MODE_8')) define('QR_MODE_8', 2);
if (!defined('QR_MODE_KANJI')) define('QR_MODE_KANJI', 3);
if (!defined('QR_MODE_STRUCTURE')) define('QR_MODE_STRUCTURE', 4);

// Levels of error correction.
if (!defined('QR_ECLEVEL_L')) define('QR_ECLEVEL_L', 0);
if (!defined('QR_ECLEVEL_M')) define('QR_ECLEVEL_M', 1);
if (!defined('QR_ECLEVEL_Q')) define('QR_ECLEVEL_Q', 2);
if (!defined('QR_ECLEVEL_H')) define('QR_ECLEVEL_H', 3);

// Supported output formats
if (!defined('QR_FORMAT_TEXT')) define('QR_FORMAT_TEXT', 0);
if (!defined('QR_FORMAT_PNG')) define('QR_FORMAT_PNG', 1);

if (!defined('QRSPEC_VERSION_MAX')) define('QRSPEC_VERSION_MAX', 40);
if (!defined('QRSPEC_WIDTH_MAX')) define('QRSPEC_WIDTH_MAX', 177);

if (!defined('QRCAP_WIDTH')) define('QRCAP_WIDTH', 0);
if (!defined('QRCAP_WORDS')) define('QRCAP_WORDS', 1);
if (!defined('QRCAP_REMINDER')) define('QRCAP_REMINDER', 2);
if (!defined('QRCAP_EC')) define('QRCAP_EC', 3);

if (!defined('QR_IMAGE')) define('QR_IMAGE', true);

if (!defined('STRUCTURE_HEADER_BITS')) define('STRUCTURE_HEADER_BITS', 20);
if (!defined('MAX_STRUCTURED_SYMBOLS')) define('MAX_STRUCTURED_SYMBOLS', 16);

if (!defined('N1')) define('N1', 3);
if (!defined('N2')) define('N2', 3);
if (!defined('N3')) define('N3', 40);
if (!defined('N4')) define('N4', 10);

if (!defined('QR_VECT')) define('QR_VECT', true);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
