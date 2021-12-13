<?php

namespace duanjw\fadada\extend\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\Connection;

/**
 * This is the model class for table "m_sell_client_contract_address".
 *
 * @property int $id 主键
 * @property int $client_id 商户id
 * @property string $contract_region_code 合同签署地区编码
 * @property string $contract_address 合同签署详细地址
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class ClientContractAddress extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%sell_client_contract_address}}';
    }

    /**
     * @return Connection the database connection used by this AR class.
     * @throws InvalidConfigException
     */
    public static function getDb(): Connection
    {
        return \Yii::$app->get('db_merchant');
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['client_id', 'created_at', 'updated_at'], 'integer'],
            [['contract_region_code'], 'string', 'max' => 50],
            [['contract_address'], 'string', 'max' => 255],
            [['client_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'client_id' => '商户id',
            'contract_region_code' => '合同签署地区编码',
            'contract_address' => '合同签署详细地址',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
