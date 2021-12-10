<?php


namespace duanjw\fadada_sdk_goldentec\extend;

class Client
{

    private $appId;
    private $appKey;
    public $apiUrl;
    private $timeout = 60;
    private $rollbackAppKey;

    public function __construct(string $appId, string $appKey, string $apiUrl, string $rollbackAppKey)
    {
        date_default_timezone_set('PRC');
        $this->appId = $appId;
        $this->appKey = $appKey;
        $this->apiUrl = $apiUrl;
        $this->rollbackAppKey = $rollbackAppKey;
    }

    public function getRollbackAppKey(): string
    {
        return $this->rollbackAppKey;
    }

    public function getRequestUrl(string $path, array $bizContent = array()): string
    {
        $body = array();

        $body['appId'] = $this->appId;
        $body['signType'] = "SHA256";
        $body['timestamp'] = date("Y-m-d H:i:s");
        $body['bizContent'] = base64_encode(urlencode(json_encode($bizContent)));
        // 签名
        $sign = ClientUtils::mkSign($body, $this->appKey);
        $body['sign'] = $sign;
        return $this->apiUrl.$path."?".http_build_query($body);
    }

    /**
     * @param string $path URI
     * @param array $bizContent 具体接口参数（非公共参数）
     * @return mixed
     */
    public function request(string $path, array $bizContent = array(), array $files = array())
    {
        $body = array();
        $headers = array();
        $body['appId'] = $this->appId;
        $body['signType'] = "SHA256";
        $body['timestamp'] = date("Y-m-d H:i:s");
        $body['bizContent'] = base64_encode(urlencode(json_encode($bizContent)));
        // 签名
        $sign = ClientUtils::mkSign($body, $this->appKey);
        $body['sign'] = $sign;
        if (empty($files)) {
            $headers['Content-type'] = "application/json;charset=utf-8";
        }
        $postHeader = $this->toPost($headers);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl.$path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $postHeader);
        if (!empty($files)){
            foreach($files as $k => $v){
                $body[$k] = new \CURLFile(realpath($v));
            }
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    /**
     * 生成请求头
     * @param array $params 请求头数组
     * @return array k:v 的数组
     */
    private function toPost(array $params = array()): array
    {
        $result = array();
        foreach($params as $k => $v){
            $result[] = $k.": ".$v;
        }
        return $result;
    }
}