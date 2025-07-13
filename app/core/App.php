<?php
/* app/core/App.php */
class App
{
    public function __construct()
    {
        $url = trim($_GET['url'] ?? 'movies/search', '/');
        [$ctrlName, $method, $param] = array_pad(explode('/', $url, 3), 3, null);

        $ctrlClass = ucfirst($ctrlName);
        require_once APP_ROOT . "/controllers/{$ctrlClass}.php";
        $controller = new $ctrlClass;

        $method = $method ?: 'index';
        call_user_func_array([$controller, $method], $param ? [$param] : []);
    }
}
