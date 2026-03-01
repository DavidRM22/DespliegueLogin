<?php
require_once __DIR__ . '/../config.php';

$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'login';

$controllerName = ucfirst($controller) . 'Controller';
$controllerFile = CONTROLLER_PATH . '/' . $controllerName . '.php';

if (!file_exists($controllerFile)) {
    http_response_code(404);
    die('Controlador no encontrado');
}

require_once $controllerFile;
$controllerObject = new $controllerName();

if (!method_exists($controllerObject, $action)) {
    http_response_code(404);
    die('Acción no encontrada');
}

$controllerObject->$action();
