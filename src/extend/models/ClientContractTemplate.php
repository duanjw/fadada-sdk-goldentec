<?php

namespace duanjw\fadada_sdk_goldentec\extend\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\Connection;

/**
 * This is the model class for table "{{%sell_client_contract_template}}".
 *
 * @property int $id
 * @property string $template_name 模板名称
 * @property string $template_id 模板编号
 * @property string $template_params 模板参数
 * @property string $key 关键字
 * @property string $pdf_url PDF 链接
 * @property int $type 模板类型
 * @property int $upload_status 上传状态0失败1成功
 * @property int $upload_time 上传时间
 * @property string $upload_msg 上传返回信息
 * @property string $operator 操作人ID
 * @property int $is_deleted 删除0否1是
 * @property int $province 适用省0全国
 * @property int $city 适用市0全省
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class ClientContractTemplate extends \yii\db\ActiveRecord
{
    CONST TYPE_BASIC_SERVICE = 1;

    public static $type_config = [
        self::TYPE_BASIC_SERVICE => '商户基础服务',
    ];

    CONST UPLOAD_STATUS_SUCCESS = 1;
    CONST UPLOAD_STATUS_FAIL = 0;
    CONST TEMPLATE_TYPE_MERCHANT = 1;
    CONST TEMPLATE_TYPE_PROVIDER = 2;

    public static $upload_status_config = [
        self::UPLOAD_STATUS_FAIL => '失败',
        self::UPLOAD_STATUS_SUCCESS => '成功',
    ];

    public static $all_region_codes = [
        '440300', //深圳
        '460000', //海南省
        '460100', //海口市
        '460200', //三亚市
        '460300', //三沙市
        '460400', //儋州市
        '460500', //洋浦经济开发区
        '469000', //省直辖县级行政区划
    ];

    //海南省的
    public static $region_codes = [
        '460000', //海南省
        '460100', //海口市
        '460200', //三亚市
        '460300', //三沙市
        '460400', //儋州市
        '460500', //洋浦经济开发区
        '469000', //省直辖县级行政区划
    ];

    CONST IS_DELETED = 1;
    CONST IS_DELETED_NO = 0;

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%sell_client_contract_template}}';
    }

    /**
     * @return Connection the database connection used by this AR class.
     * @throws InvalidConfigException
     */
    public static function getDb(): Connection
    {
        return Yii::$app->get('db_merchant');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['template_name', 'template_id', 'pdf_url', 'type', 'upload_time'], 'safe'],
            [['type', 'upload_status', 'upload_time', 'is_deleted', 'province', 'city', 'created_at', 'updated_at'], 'integer'],
            [['template_name', 'template_id', 'template_params', 'key', 'pdf_url', 'upload_msg'], 'string', 'max' => 255],
            [['operator'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'template_name' => '模板名称',
            'template_id' => '模板ID',
            'template_params' => '模板参数',
            'key' => '关键字',
            'pdf_url' => 'Pdf 链接',
            'type' => '合同类型',
            'upload_status' => 'Upload Status',
            'upload_time' => 'Upload Time',
            'upload_msg' => 'Upload Msg',
            'operator' => '操作人',
            'is_deleted' => 'Is Deleted',
            'province' => '适用省',
            'city' => '适用市',
            'created_at' => '创建时间',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 查询商户对应区域最新的上传成功合同,取不到区域取一条最新的
     * 选择深圳：后台返回深圳合同 440300
     * 选择海南：后台返回海南合同 460000,
     * 选择除这两个城市以外的城市：后台返回全国合同（目前默认返回全国，后期根据各地区税局要求而定）
     * @param string $client_region 商户所在区域
     * @return mixed
     */
    public static function getLastSuccess($client_region)
    {
        //判断长度
        $leng = strlen($client_region);
        if ($leng > 1){
            $province_and_city_code = substr($client_region, 0,4);
            if (!$province_and_city_code) {
                return false;
            }
            //管理后台模板数据只有省市这里截取补两位0
            $province_and_city_code =  $province_and_city_code . '00';
            if (!in_array($province_and_city_code, self::$all_region_codes)) {
                $province_and_city_code = '8';
            }
            //海南省的全部市,转成海南省合同
            if (in_array($province_and_city_code, self::$region_codes)){
                $province_and_city_code = '460000';
            }
        }else{
            //空就是全国8
            $province_and_city_code = '8';
        }

        $contractTemplates = self::find()
            ->where(['upload_status' => self::UPLOAD_STATUS_SUCCESS])
            ->andWhere(['type' => self::TEMPLATE_TYPE_MERCHANT])
            ->andWhere(['is_deleted' => self::IS_DELETED_NO])
            ->orderBy(['updated_at' => SORT_DESC])
            ->all();

        $contractTemplateTemp = '';
        if ($contractTemplates) {
            foreach ($contractTemplates as $key => $contractTemplate) {
                $areas_arr = explode(',', $contractTemplate->areas);
                //8代表全国
                if (in_array($province_and_city_code, $areas_arr)) {
                    $contractTemplateTemp = $contractTemplate;
                    break;
                }
            }
        }

        if ($contractTemplateTemp) {
            return $contractTemplateTemp;
        }

        // 没有默认返回一条最新的
        $contractTemplate = self::find()
            ->Where(['upload_status' => self::UPLOAD_STATUS_SUCCESS])
            ->andWhere(['type' => self::TEMPLATE_TYPE_MERCHANT])
            ->andWhere(['is_deleted' => self::IS_DELETED_NO])
            ->orderBy(['updated_at' => SORT_DESC])
            ->one();

        return $contractTemplate;
    }
}