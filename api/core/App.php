<?php

require_once __DIR__ . '/../controllers/StudentController.php';

class App {
    protected $controller = 'StudentController';
    protected $method = 'index';
    protected $params = [];
    private $routes = [];

    public function __construct() {
        $this->defineRoutes();
        $this->handleRequest();
    }

    private function defineRoutes() {
        $this->routes = [
            'GET' => [
                'student' => [StudentController::class, 'getAllStudents'],
                'student/{stud_id}' => [StudentController::class, 'getStudent'],
                'student/final-grades' => [StudentController::class, 'getAllFinalGrades'],
                'student/{stud_id}/final-grade' => [StudentController::class, 'getFinalGradeById'],
            ],
            'POST' => [
                'student' => [StudentController::class, 'addStudent'],
            ],
            'PUT' => [
                'student/{stud_id}' => [StudentController::class, 'updateStudent'],
            ],
            'DELETE' => [
                'student/{stud_id}' => [StudentController::class, 'deleteStudent'],
            ],
        ];
    }

    private function handleRequest() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $this->getProcessedUri();

        error_log("Request Method: " . $requestMethod);
        error_log("Processed URI: " . $requestUri);

        if (!isset($this->routes[$requestMethod])) {
            $this->sendNotFound();
            return;
        }

        foreach ($this->routes[$requestMethod] as $route => $handler) {
            $pattern = $this->convertToRegex($route);
            error_log("Checking Route: " . $route . " with pattern: " . $pattern);

            if (preg_match($pattern, $requestUri, $matches)) {
                error_log("Match found: " . json_encode($matches));

                $matches = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                if ($requestMethod === 'POST' || $requestMethod === 'PUT') {
                    $requestData = $this->getRequestData();
                    $this->dispatch($handler, array_merge([$requestData], array_values($matches)));
                } else {
                    $this->dispatch($handler, array_values($matches));
                }
                return;
            }
        }

        $this->sendNotFound();
    }

    private function getProcessedUri(): string {
        $requestUri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        $scriptName = dirname($_SERVER["SCRIPT_NAME"]);
        return trim(str_replace($scriptName, "", $requestUri), "/");
    }

    private function convertToRegex(string $route): string {
        error_log("Converting Route: " . $route);
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<\1>\d+)', $route); 
        $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';
        error_log("Generated Regex: " . $pattern);
        return $pattern;
    }

    private function getRequestData() {
        $data = json_decode(file_get_contents('php://input'), true);
        return is_array($data) ? $data : []; 
    }

    private function dispatch(array $handler, array $params) {
        [$controllerClass, $method] = $handler;
        $controller = new $controllerClass();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
            $requestData = array_shift($params);
            call_user_func_array([$controller, $method], array_merge([$requestData], $params));
        } else {
            call_user_func_array([$controller, $method], $params);
        }
    }

    private function sendNotFound() {
        header("HTTP/1.0 404 Not Found");
        echo json_encode(["message" => "Route not found"]);
    }
}

?>