<?php
/**
 * Created by PhpStorm.
 * User: wtw
 * Date: 7/31/2020
 * Time: 4:24 PM
 */
date_default_timezone_set('Asia/Kuala_Lumpur');
$roles = [
    "admin" => 1,
    "customer" => 2,
    "branch" => 3,
    "cashier" => 4,
    "waiter" => 5,
];

return [
    "admin" => "administrator",
    "roles" => $roles,
    "upload_path" => base_path() . '/public/uploads',
    "date_time" => date('Y-m-d H:i:s'),
    "page_limit" => 5,
    "from_email" => "test3waytoweb@gmail.com",
    "currency" => "RM",
    "default_user" => "backend/dist/img/user.png",
    "default_product" => "frontend/images/No_image_available.jpg",
];
