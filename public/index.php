<?php
define('APP_ROOT', dirname(__DIR__) . '/app');

require APP_ROOT . '/core/database.php';
require APP_ROOT . '/core/Controller.php';
require APP_ROOT . '/core/App.php';

new App;
