<?php
namespace fadada_sdk_goldentec\extend;
use Yii;

class BaseLog
{
    /**
     * YII Log category
     */
    const LOG_CATEGORY = "server";

    public static function info($msg){
        Yii::info($msg,self::LOG_CATEGORY);
    }

    public static function error($msg){
        Yii::error($msg,self::LOG_CATEGORY);
    }

    public static function warning($msg){
        Yii::warning($msg,self::LOG_CATEGORY);
    }
}