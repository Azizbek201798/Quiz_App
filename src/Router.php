<?php
namespace App;
class Router {
    public $currentRoute;
    public function __construct () {
        $this->currentRoute = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public static function getRoute () {
        return (new static())->currentRoute;
}

    public static function getResource ($route): false|string
    {
        $resourceIndex = mb_stripos($route, '{id}');
        if (!$resourceIndex){
            return false;
        }
        $resourceValue = substr(self::getRoute(), $resourceIndex);
        if($limit = mb_stripos($resourceValue, '/')){
            return substr($resourceValue, 0, $limit);
        }
        return $resourceValue ?: false;
    }
    public static function get ($route, $callback): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            self::extracted($route, $callback);
        }
    }
    public static function post ($route, $callback): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            self::extracted($route, $callback);
        }
    }
    public static function putApi ($route, $callback): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            self::extracted($route, $callback);
        }
    }

    public static function put ($route, $callback): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['_method']) && strtoupper($_POST['_method']) === 'PUT') {
            self::extracted($route, $callback);
        }
    }
    public static function delete ($route, $callback): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            self::extracted($route, $callback);
        }
    }
    public static function extracted($route, $callback): void
    {
        $resourceValue = self::getResource($route);
        if ($resourceValue) {
            $resourceRoute = str_replace('{id}', $resourceValue, $route);
            if ($resourceRoute == self::getResource()) {
                $callback($resourceValue);
                exit();
            }
        }
        if ($route == self::getRoute()) {
            $callback();
            exit();
        }
    }
    public function isApiCall(): bool{
        return mb_stripos($_SERVER['REQUEST_URI'], '/api') === 0;
    }
    public function isTelegram(): bool{
        return mb_stripos($_SERVER['REQUEST_URI'], '/telegram') === 0;
    }
}