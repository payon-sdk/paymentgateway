<?php

namespace Payon\PaymentGateway;

use Payon\PaymentGateway\PayonEncrypto;

class PayonHelper
{
    private $secret_key;
    private $app_id;
    private $url;
    private $http_auth;
    private $http_auth_pass;
    public function __construct(
        string $secret_key, 
        string $app_id, 
        string $url, 
        string $http_auth, 
        string $http_auth_pass)
    {
        $this->secret_key = $secret_key;
        $this->app_id = $app_id;
        $this->url = $url;
        $this->http_auth = $http_auth;
        $this->http_auth_pass = $http_auth_pass;
        $url_base = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $this->ref_code = 'MCAPI-LV-'. $url_base;
    }

    /**
     * Thanh toán ngay
     * @param $param
     * @return mixed
     */
    function CreateOrderPaynow($param)
    {
        $data = $param;
        $data = json_encode($data);
        $crypto = new PayonEncrypto($this->secret_key);
        $data = $crypto->Encrypt($data);
        $checksum = md5($this->app_id . $data . $this->secret_key);
        $bodyPost = array(
            'app_id' => $this->app_id,
            'data' => $data,
            'checksum' => $checksum,
            'ref_code' => $this->ref_code
        );
        $result = $this->call($bodyPost, "createOrderPaynow", $this->url, $this->http_auth, $this->http_auth_pass);
        return $result;
    }

    /**
     * Kiểm tra giao dịch
     * @param $input: merchant_request_id
     * @return mixed
     */
    function CheckPayment($input)
    {
        $data = array(
            'merchant_request_id' => $input,
        );
        $data = json_encode($data);
        $crypto = new PayonEncrypto($this->secret_key);
        $data = $crypto->Encrypt($data);
        $checksum = md5($this->app_id . $data . $this->secret_key);
        $bodyPost = array(
            'app_id' => $this->app_id,
            'data' => $data,
            'checksum' => $checksum,
            'ref_code' => $this->ref_code
        );
        $result = $this->call($bodyPost, "checkPayment", $this->url, $this->http_auth, $this->http_auth_pass);
        return $result;
    }

    /**
     * Lấy danh sách ngân hàng hỗ trợ trả góp
     * @return mixed
     */
    function GetBankInstallment()
    {
        $data = array();
        $data = json_encode($data);
        $crypto = new PayonEncrypto($this->secret_key);
        $data = $crypto->Encrypt($data);
        $checksum = md5($this->app_id . $data . $this->secret_key);
        $bodyPost = array(
            'app_id' => $this->app_id,
            'data' => $data,
            'checksum' => $checksum,
            'ref_code' => $this->ref_code
        );
        $result = $this->call($bodyPost, "getBankInstallmentV2", $this->url, $this->http_auth, $this->http_auth_pass);
        return $result;
    }

    /**
     * Thông tin phí trả góp
     * @param $data
     * @return mixed
     */
    function getFee($data)
    {
        $data = json_encode($data);
        $crypto = new PayonEncrypto($this->secret_key);
        $data = $crypto->Encrypt($data);
        $checksum = md5($this->app_id . $data . $this->secret_key);
        $bodyPost = array(
            'app_id' => $this->app_id,
            'data' => $data,
            'checksum' => $checksum,
            'ref_code' => $this->ref_code
        );
        $result = $this->call($bodyPost, "getFeeInstallmentv2", $this->url, $this->http_auth, $this->http_auth_pass);
        return $result;
    }

    /**
     * Tạo yêu cầu thanh toán trả góp
     * @param $data
     * @return mixed
     */
    function createOrderInstallment($data)
    {
        $data = json_encode($data);
        $crypto = new PayonEncrypto($this->secret_key);
        $data = $crypto->Encrypt($data);
        $checksum = md5($this->app_id . $data . $this->secret_key);
        $bodyPost = array(
            'app_id' => $this->app_id,
            'data' => $data,
            'checksum' => $checksum,
            'ref_code' => $this->ref_code
        );
        $result = $this->call($bodyPost, "createOrderInstallment", $this->url, $this->http_auth, $this->http_auth_pass);
        return $result;
    }

    /**
     * Curl
     * @param $params
     * @param $fnc
     * @return mixed
     */
    function Call($params, $fnc, $url, $http_auth, $http_auth_pass)
    {
        if(substr( $url,-1) != '/'){
            $url = $url.'/';
        }
        $url = $url.$fnc;
        $agent = $_SERVER["HTTP_USER_AGENT"];
        if(empty($agent))
        {
            $agent = 'not user agent';
        }
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_USERAGENT, $agent);
        curl_setopt($curl, CURLOPT_USERPWD, $http_auth . ':' . $http_auth_pass);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
                'Content-Type: application/json'
            )
        );
        $response = curl_exec($curl);
        $resultStatus = curl_getinfo($curl);

        if($resultStatus['http_code'] == 200 && isset($response) )
        {
            $response = json_decode($response, true);
            if ($response['error_code'] == "00") {
                return $response;
            } else {
                return false;
            }
        } else{
            return false;
        }
    }
}
