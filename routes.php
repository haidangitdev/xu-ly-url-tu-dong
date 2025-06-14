<?php
// Class xử lý định tuyến URL
class Route
{
    private $routes = [];

    // Thêm route mới
    public function add($method, $path, $callback)
    {
        // Tìm các tham số dạng {param} trong đường dẫn
        preg_match_all('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', $path, $paramMatches);
        $paramNames = $paramMatches[1];

        // Biến {param} thành regex để bắt URL tương ứng
        $regex = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([^/]+)', $path);
        $regex = "#^" . $regex . "$#";

        // Lưu route vào mảng routes
        $this->routes[] = [
            'method' => strtoupper($method),
            'regex' => $regex,
            'params' => $paramNames,
            'callback' => $callback,
        ];
    }

    // Các hàm hỗ trợ add route theo HTTP method
    public function get($path, $callback)
    {
        $this->add('GET', $path, $callback);
    }
    public function post($path, $callback)
    {
        $this->add('POST', $path, $callback);
    }
    public function put($path, $callback)
    {
        $this->add('PUT', $path, $callback);
    }
    public function delete($path, $callback)
    {
        $this->add('DELETE', $path, $callback);
    }

    // Xử lý request hiện tại dựa trên method và path
    public function dispatch($method, $path)
    {
        foreach ($this->routes as $route) {
            // Kiểm tra method có khớp không, và path có hợp lệ không
            if ($route['method'] === strtoupper($method) && preg_match($route['regex'], $path, $matches)) {
                array_shift($matches); // Bỏ phần khớp toàn bộ

                $callback = $route['callback'];

                // Nếu callback là mảng ['Controller', 'method']
                if (is_array($callback)) {
                    $controller = $callback[0];
                    $methodName = $callback[1];

                    // Nạp file controller
                    require_once "controllers/{$controller}.php";
                    $controllerInstance = new $controller();

                    // Gọi method với tham số lấy từ URL
                    return call_user_func_array([$controllerInstance, $methodName], $matches);
                }

                // Nếu callback là hàm (closure)
                return call_user_func_array($callback, $matches);
            }
        }

        // Nếu không tìm thấy route, trả về lỗi 404
        http_response_code(404);
        require __DIR__ . '/404.php';
        exit;
    }
}
