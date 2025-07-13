<?php
declare(strict_types=1);

/* self‑define once — works from CLI & web */
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

/* ultra‑light PSR‑0 autoloader */
spl_autoload_register(function ($cls) {
    foreach ([
        APP_ROOT . "/controllers/$cls.php",
        APP_ROOT . "/models/$cls.php",
        APP_ROOT . "/core/$cls.php",
    ] as $f) if (is_file($f)) { require $f; return; }
});

class App
{
    public function __construct()
    {
        $parts = explode('/', trim($_GET['url'] ?? 'movies/search', '/'));
        [$c,$m] = [$parts[0] ?? 'movies', $parts[1] ?? 'search'];

        $ctrl = ucfirst($c);
        if (!class_exists($ctrl)) abort(404,'ctrl');
        if (!method_exists($ctrl,$m)) abort(404,'action');

        call_user_func_array([new $ctrl, $m], array_slice($parts,2));
    }
}

function abort(int $code,string $msg): never
{
    http_response_code($code); exit("$code • $msg");
}
