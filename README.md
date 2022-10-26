# Giới thiệu thông tin
- Package sẽ hỗ trợ gọi các API trong tài liệu: https://docs.nextpay.vn/
    + Thanh toán ngay
    + Lấy danh sách ngân hàng hỗ trợ trả góp
    + Thông tin phí trả góp
    + Tạo yêu cầu thanh toán trả góp
    + Kiểm tra giao dịch
# Cài đặt và loading
Cài bằng composer
```sh
composer require devteam/payon
```
Include vào file PHP
```php
<?php
use Payon\PaymentGateway\PayonHelper;
//or require
require 'path/to/paymentgateway/src/PayonHelper.php';
```
# Code mẫu
- Các thanh số truyền vào hàm PayonHelper
    + $secret_key: MC_SECRET_KEY - Khóa để thực hiện mã hóa tham số data trong các hàm nghiệp vụ
    + $app_id: APP_ID - ID ứng dụng để định danh ứng dụng tích hợp
    + $url: URL_API - Đường dẫn API
    + $http_auth: MC_AUTH_USER - Tên Auth basic
    + $http_auth_pass: MC_AUTH_PASS - Mật khẩu Http Auth basic
```php
<?php

use Payon\PaymentGateway\PayonHelper;

$payon = new PayonHelper( $secret_key, $app_id, $url, $http_auth, $http_auth_pass);
$data = [
    "merchant_id" => $mc_id, //MC_ID - ID Merchant để định danh khách hàng trên PayOn
    "merchant_request_id" => $merchant_request_id,
    "amount" => (int)$order->get_total(),
    'bank_code' => $bank_code,
    'cycle' => (int) $cycle,
    'card_type' => $card_type,
    'userfee' => (int)(($user_fee == 'yes') ? 1 : 2),
    "description" => 'Thanh toán đơn hàng trả góp #' . $order_id,
    "url_redirect" => $url_redirect,
    "url_notify" => $url_notify,
    "url_cancel" => $url_cancel,
    "customer_fullname" => $customer_fullname,
    "customer_email" => $customer_email,
    "customer_mobile" => $customer_mobile,
];
return $payon->createOrderInstallment($data); //Tạo thanh toán trả góp
```
