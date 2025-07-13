<?php
/* app/core/Controller.php */
class Controller
{
    protected function model(string $name)
    {
        require_once APP_ROOT . "/models/{$name}.php";
        return new $name();
    }

    protected function view(string $view, array $data = []): void
    {
        extract($data);
        require APP_ROOT . "/views/{$view}.php";
    }
}
