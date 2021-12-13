<?php


namespace duanjw\fadada\extend;

use duanjw\fadada\api\FddApi;
use duanjw\fadada\extend\models\Areas;
use Yii;

class ClientUtils
{

    public static function getInstance(): FddApi
    {
        return  new FddApi(new Client(
            Yii::$app->params['contract_interface_app_id'],
            Yii::$app->params['contract_interface_app_key'],
            Yii::$app->params['contract_interface_api_url'],
            Yii::$app->params['contract_interface_rollback_app_key']
        ));
    }

    public static function getAreas($region_code, $glue = ''){
        if($region_code = trim($region_code )){
            $region_code = str_pad($region_code, 6, "0", STR_PAD_RIGHT);
        }
        $areas = Areas::getOriginalByAreaId($region_code);
        $areasAll = [];
        if ($areas['province_name'] ?? false){
            $areasAll[] = $areas['province_name'];
        }
        if ($areas['city_name'] ?? false){
            $areasAll[] = $areas['city_name'];
        }
        if ($areas['area_name'] ?? false){
            $areasAll[] = $areas['area_name'];
            $result['area_region_code'] = $areas['area_id'] ?? '';  //区地区编码
        }
        if ($areas['street_name'] ?? false){
            $areasAll[] = $areas['street_name'];
        }
        if($areasAll){
            $areasAll = implode($glue, $areasAll);
            $result['areas'] = $areasAll;
        }
        return $result['areas']??"";
    }

    /**
     * 签名
     * @param array $body
     * @param string $key
     * @return string
     */
    public static function mkSign(array $body, string $key) : string
    {
        $signType = $body["signType"] ?? 'SHA256';

        //得到排序后的字符串
        $keys = array_keys($body);
        array_multisort($keys, SORT_ASC, SORT_STRING);
        $sortParam = self::arrayParamToStr($body, $keys);
        //计算签名,sign = Base64(SHA256(SHA256(待签名字符串) + appKey));
        $signText = strtoupper(hash($signType, $sortParam));
        $signText = strtoupper(hash($signType, $signText.$key));
        return base64_encode($signText);
    }

    /**
     * 拼接签名所需要的参数
     * @param array $array
     * @param array $keys
     * @return string
     */
    public static function arrayParamToStr(array $array, array $keys): string
    {
        $Str = "";
        foreach($keys as $v){
            $Str .= $v."=".$array[$v]."&";
        }
        return trim($Str,"&");
    }


    /**
     * 生成合同交易随机字符串
     * @return string
     */
    public static function generateTransactionId() : string
    {
        return time() . rand(10000000, 99999999);
    }

    /**
     * 根据字符集生成随机字符串
     * @param int $length 字符串长度
     * @param int $type 0:纯数字, 1:数字与大小写字母 其他:数字和大写字母
     * @return string
     */
    public static function generatePassword(int $length = 6, int $type = 0) : string
    {
        if ($type == 1) {
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        } elseif ($type == 0) {
            $chars = '0123456789';
        } else {
            $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }

    /**
     * 返回格式
     * @param string $msg 返回消息
     * @param string $code 返回状态码
     * @param mixed $data 返回数据
     * @return array
     *
     * @author xiaobobo
     */
    public static function returnArray(string $msg = '系统错误', string $code = '1', $data = []) : array
    {
        return ['code' => $code, 'msg' => $msg, 'data' => $data];
    }
}