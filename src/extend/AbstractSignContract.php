<?php

namespace fadada_sdk_goldentec\extend;

use Exception;
use fadada_sdk_goldentec\constant\BaseConstant;
use fadada_sdk_goldentec\constant\ContractError;
use fadada_sdk_goldentec\extend\models\ClientContract;
use fadada_sdk_goldentec\extend\models\Client;
use fadada_sdk_goldentec\extend\models\ClientContractTemplate;
use yii\base\Model;
use Yii;

/**
 * 签署合同
 * Class AbstractSignContract
 * @package models\models\Contract
 */
abstract class AbstractSignContract extends Model
{
    public $contract_id;
    public $contract;
    public $client_id;
    public $client;
    public $fddApi;

    const GAODENG_SIGN_KEYWORD = '深圳高灯计算机科技有限公司';

    const PARTY_B_SIGN_KEYWORD = '乙方盖章位';

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->fddApi = ClientUtils::getInstance();
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['contract_id', 'client_id'], 'safe'],
            [['client_id'], 'checkContract'],
            [['client_id'], 'checkClient'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'client_id' => '商户id',
            'constract_id' => '合同编码',
        ];
    }

    public abstract function syncGoldenCloud(array $params);

    public abstract function publishSyncContract(array $params);

    public abstract function getOrgId();

    /**
     * 抛异常
     * @param array $key
     * @param string|null $replace_msg 可替换参数一里面的错误信息，可用于透传底层错误描述，慎用
     * @return Exception
     * @throws Exception
     */
    public static function throwException(array $key, string $replace_msg = null): Exception
    {
        throw new Exception($replace_msg??$key[1], $key[0]) ;
    }


    /**
     * 检查商户合同数据是否完整
     * @param $attribute
     * @return bool
     */
    public function checkContract($attribute): bool
    {
        if (!($this->contract = ClientContract::findOne(['contract_id' => $this->contract_id]))) {
            $this->addError($attribute, '商户合同信息不完整');
            return false;
        }
        // 订单合同的时间在一填充的时候就根据订单写进去了
        if ($this->contract->contract_type != ClientContract::TYPE_BUY && $this->contract->contract_end_time > time()) {
            $this->addError($attribute, '商户合同还在合同有效期内');
            return false;
        }
        return true;
    }


    /**
     * 检查client_id
     * @param $attribute
     * @return bool
     */
    public function checkClient($attribute): bool
    {
        if (!($this->client = Client::findOne($this->client_id))) {
            $this->addError($attribute, '商户信息不完整');
            return false;
        }

        return true;
    }

    /**
     * 判断是否有合同了
     * @param $client_id
     * @param int $contract_type
     * @return boolean
     */
    public function checkContractHas($client_id, int $contract_type = ClientContract::TYPE_MERCHANT): bool
    {
        //非基础合同不做判断
        if($contract_type != ClientContract::TYPE_MERCHANT ){
            return false;
        }

        $contract = ClientContract::find()
            ->where(['client_id' => $client_id])
            ->andWhere([
                'contract_status' => 2,
                'contract_type' =>$contract_type
            ])
            ->orderBy('created_at DESC')
            ->one();
        if (!$contract) {
            return false;
        }

        if ($contract->contract_end_time > time()) {
            return true;
        }
        return false;
    }


    /**
     * 合同签署之前获取合同内容，这里需要注册，存证（编号证书申请），自定义印章，模板填充（先模板上传）
     * @param string $clientId
     * @param string $regionCode
     * @param string $templateId
     * @param string $orderSn
     * @param int $beginTime
     * @param int $endTime
     * @param int $type
     * @param bool $saveRecord
     * @return array
     * @throws Exception
     */
    public function getContract(string $clientId, string $regionCode='', string $templateId = '', string $orderSn = '',
                                int $beginTime = 0, int $endTime = 0, int $type = ClientContract::TYPE_MERCHANT, bool $saveRecord = true): array
    {

        $client = Client::findOne($clientId);
        var_dump($client);exit;

        #1.商户法大大注册
        $this->accountRegister($client);

        #2.商户向法大大存证
        $this->hashAuth($client);

        #3.申请编号证书
        $this->applyClientNumberCert($client->customer_id, $client->evidence_no);

        #4.商户自定义印章
        $result = $this->fddApi->customSignature($client->customer_id, $client->name);
        if (isset($result['code']) && $result['code'] != BaseConstant::SUCCESS_CODE) {
            self::throwException(ContractError::$SYSTEM_ERROR,$result['msg']);
        }

        #5.商户模板填充
        if (!empty($templateId)) {
            $contractTemplate = ClientContractTemplate::findOne(['id' => $templateId]);
        } else {
            $regionCode = !empty($regionCode) ? $regionCode : $client->region_code;
            $contractTemplate = ClientContractTemplate::getLastSuccess($regionCode);
        }

        $contractId = $this->contractCreate($client->id, $contractTemplate);

        // 是否保存记录，b端小程序是不用保存的，支付成功会直接创建合同并且签署
        if ($saveRecord){
            #6.模板填充完成写商户合同表
            $contract = new ClientContract();
            $contract->client_id = $clientId;
            $contract->customer_id = $client->customer_id;
            $contract->contract_id = $contractId;
            $contract->template_id = $contractTemplate->template_id;
            $contract->title = $contractTemplate->template_name;
            $contract->contract_type = $type;
            $contract->contract_material = ClientContract::TYPE_MATERIAL_ELEC;
            $contract->contract_template_id = $contractTemplate->id;
            $contract->contract_begin_time = $beginTime;
            $contract->contract_end_time = $endTime;
            if (!empty($orderSn)) {
                $contract->shop_order_sn = $orderSn;
            }
            if (!$contract->save(1)) {
                self::throwException(ContractError::$CONTRACT_MERCHANT_INFO_SAVE_ERR);
            }
        }

        #6.最后返回合同查看下载地址，新版本填充未返回，需要自己拼接
        $downloadUrl = $this->fddApi->contractDownload($contractId, "");
        $viewUrl = $this->fddApi->contractView($contractId, false);

        #6.最后返回合同查看下载地址
        return ['download_url'=>$downloadUrl['data'] ?? '','viewpdf_url'=>$viewUrl['data'] ?? '', 'contract_id' => $contractId];
    }

    /**
     * 合同签署，这里两次自动签署，盖两次章
     * @param array $params
     * @param string $goldenSignKeyword 高灯盖章关键字
     * @return array
     * @throws Exception
     */
    public function mkContract(array $params, string $goldenSignKeyword = ''): array
    {

        $this->load($params, '');
        if(!$this->validate()){
            self::throwException(ContractError::$CONTRACT_PARAMS_FAIL);
        }

        /** @var Client $client */
        $client = $this->client;
        /** @var ClientContract $clientContract */
        $clientContract = $this->contract;
        /** @var ClientContractTemplate $contractTemplate */
        $contractTemplate = ClientContractTemplate::findOne(['template_id' => $clientContract->template_id]);

        if (empty($contractTemplate)) {
            BaseLog::info(['获取模板信息失败' => ['client_id' => $client->id, 'template_id' => $clientContract->template_id]]);
            self::throwException(ContractError::$CONTRACT_TEMPLATE_FAIL);
        }

        #1.盖公司深圳高灯自己的章
        $secondTransactionId = ClientUtils::generateTransactionId();
        $this->autoSignContract($secondTransactionId, $this->contract_id, Yii::$app->params['gaopeng_contract_customer_id'], $goldenSignKeyword ? : self::GAODENG_SIGN_KEYWORD, 2);

        #2.这里盖商户的章,并且设置自动归档，这样回调接口中不用归档了
        $secondTransactionId = ClientUtils::generateTransactionId();
        $result = $this->autoSignContract($secondTransactionId, $this->contract_id, $client->customer_id, $contractTemplate->key, 1);

        $clientContract->download_url = $result['data']['downloadUrl'] ?? '';
        $clientContract->viewpdf_url = $result['data']['viewUrl'] ?? '';
        $clientContract->contract_status = ClientContract::STATUS_WAIT;
        $clientContract->transaction_id = $secondTransactionId;

        if (!$clientContract->save()) {
            BaseLog::info(['签署商户合同盖章更新表失败' => ['clientContract' => json_encode($clientContract)]]);
            self::throwException(ContractError::$CONTRACT_SIGN_FAIL);
        }

        // 同步地址
        if (!empty($params['region_code'])) {
            $data = [
                'client_id' => $client->id,
                'org_id' => $this->getOrgId(),
                'region_code' => $params['region_code'],
                'address' => $params['register_address']
            ];
            try {
                $data['type'] = 1;
                $this->syncGoldenCloud(['business_id'=>$clientContract->id, 'client_id'=>$data['client_id'], 'org_id'=>$data['org_id'],
                    'region_code'=>$data['region_code'], 'address'=>$data['address'], 'register_region_code'=> $data['region_code'],
                    'register_address'=>$data['address']]);
            } catch (\Exception $e) {
                BaseLog::error(['同步地址到高灯云控制台失败' => $e->getMessage()]);
                $this->publishSyncContract($data);
            }

            $client->region_code = $params['region_code'];
            $client->register_address = $params['register_address'];
            if (!$client->save()) {
                BaseLog::error(['同步地址到商家平台失败' => ['client' => json_encode($client)]]);
                $data['type'] = 2;
                $this->publishSyncContract($data);
            }

        }
        return ['download_url' => $result['data']['downloadUrl'], 'viewpdf_url' => $result['data']['viewUrl']];
    }

    /**
     * 套餐订单合同模板填充
     * @param string $template_id
     * @param string $client_id
     * @param string $order_sn
     * @param int $beginTime
     * @param int $endTime
     * @param bool $saveRecord
     * @return array
     * @throws Exception
     */
    public function getOrderContract(string $template_id, string $client_id, string $order_sn, int $beginTime = 0, int $endTime = 0, bool $saveRecord = true): array
    {
        return $this->getContract($client_id, "", $template_id, $order_sn, $beginTime, $endTime, ClientContract::TYPE_BUY, $saveRecord);
    }

    /**
     * 重新填充模板
     * @param $clientContractId
     * @return array
     * @throws Exception
     */
    public function updateOrderContract($clientContractId): array
    {
        $contract = ClientContract::findOne($clientContractId);

        // 查找合同模板
        $contract_template = ClientContractTemplate::findOne(['id' =>  $contract->contract_template_id]);
        if (empty($contract_template)) {
            self::throwException(ContractError::$CONTRACT_TEMPLATE_FAIL);
        }
        // 重新填充模板
        $contractId = $this->contractCreate($contract->client_id, $contract_template);

        $contract->contract_id = $contractId;
        $contract->template_id = $contract_template->template_id;
        $contract->title = $contract_template->template_name;
        if (!$contract->save()) {
            self::throwException(ContractError::$CONTRACT_MERCHANT_INFO_SAVE_ERR);
        }

        #6.最后返回合同查看下载地址，新版本填充未返回，需要自己拼接
        $downloadUrl = $this->fddApi->contractDownload($contractId, "");
        $viewUrl = $this->fddApi->contractView($contractId, false);
        $fourthRes = ['download_url'=>$downloadUrl['data'] ?? '', 'viewpdf_url'=>$viewUrl['data'] ?? ''];

        // 最后返回合同查看下载地址
        return ['url' => $fourthRes, 'contract_id' => $contractId];

    }

    /**
     * 账号注册
     * @param Client $client
     * @throws Exception
     */
    protected function accountRegister(Client $client): void
    {
        //已经有customer_id 说明已经注册过了
        if(!empty($client->customer_id)){
            return;
        }
        $first_res = $this->fddApi->accountRegister(BaseConstant::FDD_ACCOUNT_TYPE_COMPANY, $client->id, "", $client->name);
        if (isset($first_res['code']) && $first_res['code'] != BaseConstant::SUCCESS_CODE) {
            self::throwException(ContractError::$SYSTEM_ERROR,$first_res['msg']);
        }
        $client->customer_id = $first_res['data'];
        if (!$client->save(1)) {
            self::throwException(ContractError::$CONTRACT_MERCHANT_CUSTOMER_ID_SAVE_ERR);
        }
    }

    /**
     * 哈希存证
     * @param Client $client
     * @throws Exception
     */
    protected function hashAuth(Client $client): void
    {
        //evidence_no 已经认证过
        if(!empty($client->evidence_no)){
            return;
        }
        list($file_name, $noper_time, $file_size, $original_sha256) = $this->setHashFile($client->id);

        $second_res = $this->fddApi->HashAuth($client->customer_id, 1,
            BaseConstant::FDD_ACCOUNT_TYPE_COMPANY, $client->name, $client->name, $file_name, $noper_time, $file_size, $original_sha256,
            ClientUtils::generateTransactionId());
        if (isset($second_res['code']) && $second_res['code'] != BaseConstant::SUCCESS_CODE) {
            self::throwException(ContractError::$SYSTEM_ERROR,$second_res['msg']);
        }

        $client->evidence_no = $second_res['data'];
        if (!$client->save(1)) {
            self::throwException(ContractError::$CONTRACT_MERCHANT_EVIDENCE_NO_SAVE_ERR);
        }
    }

    /**
     * 申请编号证书
     * @param string $clientId 客户id
     * @param string $evidenceNo 编号
     * @return void
     * @throws Exception
     */
    protected function applyClientNumberCert(string $clientId, string $evidenceNo): void
    {
        $result = $this->fddApi->applyClientNumberCert($clientId, $evidenceNo);
        if (isset($result['code']) && $result['code'] != BaseConstant::SUCCESS_CODE) {
            self::throwException(ContractError::$SYSTEM_ERROR,$result['msg']);
        }
    }

    /**
     * 填充模板创建合同, 这里创建合同和创建模板一样，都用高灯客户编号创建
     * @param string $client_id
     * @param ClientContractTemplate $contractTemplate
     * @return string 合同编号
     * @throws Exception
     */
    protected function contractCreate(string $client_id, ClientContractTemplate $contractTemplate): string
    {
        $contractId = ClientUtils::generatePassword(32, 1);

        //合同关键字替换
        $keywordMap = $this->getContractKeywordMap($contractTemplate->template_params, $client_id);
        $fourthRes = $this->fddApi->contractCreate($contractTemplate->template_id, $contractTemplate->template_name,
            $contractId, 11, 0, json_encode($keywordMap), "", "","", Yii::$app->params['gaopeng_contract_customer_id']);
        if (isset($fourthRes['code']) && $fourthRes['code'] != BaseConstant::SUCCESS_CODE) {
            self::throwException(ContractError::$SYSTEM_ERROR,$fourthRes['msg']);
        }
        return $contractId;
    }

    /**
     * 合同签署
     * @param string $secondTransactionId 请求id
     * @param string $contractId 合同编号
     * @param string $customerId 签署客户编号
     * @param string $locateKey 关键字
     * @param int $autoArchive 是否归档
     * @return array
     * @throws Exception
     */
    protected function autoSignContract(string $secondTransactionId, string $contractId, string $customerId, string $locateKey, int $autoArchive): array
    {
        $result = $this->fddApi->autoSignContract($contractId, "", $customerId,
            "", $autoArchive, Yii::$app->params['contract_callback_url'], "", 1,
            $locateKey, 2, 0.00, 0.00,
            "", $secondTransactionId, "");
        if (isset($result['code']) && $result['code'] != BaseConstant::SUCCESS_CODE) {
            BaseLog::info(['签署合同盖章失败' => ['contractId' => $contractId, 'customer_id' => $customerId]]);
            self::throwException(ContractError::$SYSTEM_ERROR, $result['msg']);
        }
        return $result;
    }

    /**
     * 合同签署之前存证,存文件，返回存证文件所需字段
     * @param string $client_id
     * @return array
     */
    protected function setHashFile(string $client_id): array
    {
        $client = Client::findOne($client_id);
        //1.file_name 2.noper_time 3.file_size, 4.original_sha256
        $data = json_encode(['client_id' => $client_id, 'client_name' => $client->name, 'tax_code' => $client->tax_code]);
        return [$client->name, $client->updated_at, 40000000, hash('sha256', $data)];
    }

    /**
     * @param string $templateParams
     * @param string $client_id
     * @return array
     * @throws Exception
     */
    protected function getContractKeywordMap(string $templateParams, string $client_id): array
    {
        $paramMap = json_decode($templateParams, true);
        if (empty($paramMap)) {
            self::throwException(ContractError::$CONTRACT_TEMPLATE_CONTENT_EMPTY_ERR);
        }
        //合同关键字替换
        return ContractKeywordMap::getContractKeywordMap($paramMap,$client_id);
    }

}
