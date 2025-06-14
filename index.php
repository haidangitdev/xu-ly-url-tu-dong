<?php
// Load class Route
require_once 'routes.php';
// Khởi tạo đối tượng route
$route = new Route();
// Đăng ký route dạng /{slug}/{id}
require_once 'web.php';
// Lấy method và path hiện tại từ request
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Lấy tên thư mục chứa project (tự động)
$scriptName = dirname($_SERVER['SCRIPT_NAME']); // => "/url-custom"
// Loại bỏ phần base folder ra khỏi URI
$path = str_replace($scriptName, '', $path);
// Gửi cho router xử lý
$route->dispatch($method, $path);
