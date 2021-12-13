<?php

namespace duanjw\fadada\extend\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\Connection;

/**
 * This is the model class for table "{{%sell_client_contract}}".
 *
 * @property int $id
 * @property string $contract_id 合同编号
 * @property string $template_id 模板编号
 * @property string $title 合同名称
 * @property int $client_id 商户id
 * @property string $customer_id 法大大客户编号
 * @property string $transaction_id 交易号
 * @property string $download_url 合同下载地址
 * @property string $viewpdf_url 合同查看地址
 * @property string $result_desc 签章结果描述信息
 * @property int $contract_status 合同签署状态 0初始状态 1失败 2成功
 * @property int $contract_type 合同类型1初始值 1商户平台基础合同 2服务商基础合同
 * @property int $contract_material 合同材质0初始值 1电子合同 2纸质合同
 * @property int $is_deleted 删除 0否 1是
 * @property int $contract_begin_time 合同开始时间
 * @property int $contract_end_time 合同结束时间
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property string $shop_order_sn 订单号
 * @property int $contract_template_id 关联合同模板id
 */
class ClientContract extends \yii\db\ActiveRecord
{
    CONST IS_DELETED = 1;
    CONST IS_DELETED_NO = 0;

    //合同签署状态
    CONST STATUS_OK = 2; //成功
    CONST STATUS_NO = 1; //失败
    CONST STATUS_WAIT = 3; //等待回调
    CONST STATUS_DEFAULT = 0; //待签

    //合同类型
    CONST TYPE_MERCHANT = 1;  //商户平台基础合同
    CONST TYPE_BUY = 2 ; //商户套餐购买合约

    CONST TYPE_MATERIAL_ELEC = 1;
    CONST TYPE_MATERIAL_PAPER = 2;

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
        return '{{%sell_client_contract}}';
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
    public function rules(): array
    {
        return [
            [['contract_id', 'template_id', 'title', 'customer_id', 'transaction_id', 'download_url', 'viewpdf_url', 'result_desc'], 'safe'],
            [['id', 'client_id', 'contract_status', 'is_deleted', 'contract_type', 'contract_material', 'contract_begin_time', 'contract_end_time', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'template_name' => '模板名称',
            'template_id' => '模板ID',
            'title' => '合同名称',
            'customer_id' => '客户编号',
            'transaction_id' => '交易号',
            'download_url' => '合同下载地址',
            'viewpdf_url' => '合同查看地址',
            'result_desc' => '合同签章描述',
            'client_id' => '商户id',
            'contract_status' => '合同状态',
            'is_deleted' => '删除状态',
            'contract_type' => '合同类型',
            'contract_material' => '合同材质',
            'contract_begin_time' => '签署开始时间',
            'contract_end_time' => '签署结束时间',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'shop_order_sn' => '订单号',
            'contract_template_id' => '关联合同模板id'
        ];
    }

    /**
     * 查询商户的一条最新的成功合同
     * @param $client_id
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getFirstSuccess($client_id)
    {
        return self::find()
            ->where(['client_id'=>$client_id, 'contract_status'=>self::STATUS_OK,'is_deleted'=>0,'contract_type'=>self::TYPE_MERCHANT])
            ->orderBy('created_at DESC')
            ->one();

    }

    /**
     * 查询商户的合同
     * @param $client_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getList($client_id){
        return self::find()
            ->where(['client_id'=>$client_id, 'contract_status'=>self::STATUS_OK, 'is_deleted'=> 0])
            ->orderBy('created_at ASC')
            ->asArray()
            ->all();
    }

    /**
     * 获取合同列表
     * @param $client_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getWaitSignContractByClientId($client_id)
    {
        return self::find()
            ->where(['client_id' => $client_id, 'is_deleted'=> 0])
            ->andWhere([
                'contract_status' => self::STATUS_DEFAULT,
                'contract_type' => ClientContract::TYPE_BUY
            ])
            ->select(['id','title','contract_type','contract_status'])
            ->orderBy('created_at ASC')
            ->asArray()
            ->all();
    }

    /**
     * 获取最新一条基础合同数据
     * @param $client_id
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function baseContractLatestByClientId($client_id)
    {
        return self::find()
                ->where([
                    'client_id'=>$client_id,
                    'contract_type'=>self::TYPE_MERCHANT,
                    'is_deleted'=> 0,
                    'contract_status'=>self::STATUS_OK
                ])
                ->select(['id','contract_end_time'])
                ->orderBy('created_at desc')
                ->one() ;
    }

    /**
     * 获取签署合同信息
     * @param $id
     * @param array $cols
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getClientContractById($id,$cols =['*'])
    {
        return self::find()
                ->andWhere(['id'=>$id,'is_deleted'=>0])
                ->select($cols)
                ->asArray()
                ->one();
    }


    public static function isExistWaitingContractByClientId($clientId, $checkBaseContract)
    {
        //待签合同
        $waitContract = self::find()
            ->andWhere([
                'contract_status' => self::STATUS_DEFAULT,
                'contract_type' => ClientContract::TYPE_BUY,
                'is_deleted' => 0,
                'client_id' => $clientId
            ])
            ->exists();
        if($waitContract){
            return true;
        }

        if ($checkBaseContract) {
            //是否存在基础合同
            $baseContract = self::getFirstSuccess($clientId);
            if(empty($baseContract)){
                return true;
            }
        }

        return false;

    }

    public static function getContractByContractId($contractId,$cols = ["*"])
    {
        return self::find()
            ->andWhere(['contract_id'=>$contractId,'is_deleted'=>0])
            ->select($cols)
            ->one();
    }

}