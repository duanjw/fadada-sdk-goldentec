<?php


namespace fadada_sdk_goldentec\api;


use Exception;
use fadada_sdk_goldentec\constant\BaseConstant;
use fadada_sdk_goldentec\constant\ContractError;
use fadada_sdk_goldentec\extend\BaseLog;
use fadada_sdk_goldentec\extend\Client;
use fadada_sdk_goldentec\extend\ClientUtils;
use fadada_sdk_goldentec\interfaces\FddInterface;


class FddApi implements FddInterface
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param int $type
     * @param string $account
     * @param string $adminAccountId
     * @param string $companyName
     * @return array
     */
    public function accountRegister(int $type, string $account, string $adminAccountId, string $companyName): array
    {
        // TODO: Implement accountRegister() method.
        $bizContent = [];
        if (empty($type) || !in_array($type, [BaseConstant::FDD_ACCOUNT_TYPE_PERSON, BaseConstant::FDD_ACCOUNT_TYPE_COMPANY])) {
            return ClientUtils::returnArray('账户类型异常');
        }
        $bizContent['type'] = $type;

        // 企业类型必须传企业名称
        if ($type == BaseConstant::FDD_ACCOUNT_TYPE_COMPANY){
            if (empty($companyName)) {
                return ClientUtils::returnArray('企业名称不能为空');
            }
            $bizContent['companyName'] = $companyName;
        }
        if (empty($account)) {
            return ClientUtils::returnArray('注册唯一标识不同为空');
        }
        $bizContent['account'] = $account;

        // 绑定管理员，通过注册个人类型获取到的编号
        if (!empty($adminAccountId)) {
            $bizContent['adminAccountId'] = $adminAccountId;
        }
        $result = $this->client->request(BaseConstant::ACCESS_REGISTER_PATH, $bizContent);
        BaseLog::info(['商户注册法大大 push url ' . $this->client->apiUrl . BaseConstant::ACCESS_REGISTER_PATH . ' | $bizContent ' . json_encode($bizContent, JSON_UNESCAPED_UNICODE) . ' | return ' . json_encode($result, JSON_UNESCAPED_UNICODE)]);
        if (!isset($result['code']) || $result['code'] != BaseConstant::FDD_SUCCESS_CODE) {
            return ClientUtils::returnArray($result['msg'] ?? '商户同步注册失败');
        }
        return ClientUtils::returnArray('商户同步注册成功', BaseConstant::SUCCESS_CODE, $result['data']);
    }

    /**
     * @param string $customerId
     * @param int $isRepeatVerified
     * @param int $type
     * @param string $preservationName
     * @param string $preservationDesc
     * @param string $fileName
     * @param string $noperTime
     * @param string|int $fileSize
     * @param string $originalSha256
     * @param string $transactionId
     * @return array
     */
    public function HashAuth(string $customerId, int $isRepeatVerified, int $type, string $preservationName,
                             string $preservationDesc, string $fileName, string $noperTime,
                             string $fileSize, string $originalSha256, string $transactionId): array
    {
        // TODO: Implement hashDeposit() method.
        $bizContent = [];
        if (empty($customerId)) {
            return ClientUtils::returnArray('客户id不能为空');
        }
        if (empty($isRepeatVerified)) {
            return ClientUtils::returnArray('是否重复存证不能为空');
        }
        if (empty($type)) {
            return ClientUtils::returnArray('存证类型不能为空');
        }
        if (empty($preservationName)) {
            return ClientUtils::returnArray('存证名称不能为空');
        }
        if (!empty($preservationDesc)) {
            $bizContent['preservationDesc'] = $preservationDesc;
        }
        if (!empty($fileName)) {
            $bizContent['fileName'] = $fileName;
        }
        if (empty($noperTime)) {
            return ClientUtils::returnArray('文件最后修改时间不能为空');
        }
        if (empty($fileSize)) {
            return ClientUtils::returnArray('文件大小不能为空');
        }
        if (empty($originalSha256)) {
            return ClientUtils::returnArray('文件hash值不能为空');
        }
        if (empty($transactionId)) {
            return ClientUtils::returnArray('交易号不能为空');
        }
        $bizContent['customerId'] = $customerId;
        $bizContent['isRepeatVerified'] = $isRepeatVerified;
        $bizContent['type'] = $type;
        $bizContent['preservationName'] = $preservationName;
        $bizContent['noperTime'] = $noperTime;
        $bizContent['fileSize'] = $fileSize;
        $bizContent['originalSha256'] = $originalSha256;
        $bizContent['transactionId'] = $transactionId;
        $result = $this->client->request(BaseConstant::EVIDENCE_HASH_SAVE_PATH, $bizContent);
        BaseLog::info(['法大大哈希存证 push url ' . $this->client->apiUrl . BaseConstant::EVIDENCE_HASH_SAVE_PATH . ' | $bizContent ' . json_encode($bizContent, JSON_UNESCAPED_UNICODE) . ' | return ' . json_encode($result, JSON_UNESCAPED_UNICODE)]);
        if (!isset($result['code']) || $result['code'] != BaseConstant::FDD_SUCCESS_CODE) {
            return ClientUtils::returnArray($result['msg'] ?? '企业存证失败');
        }
        return ClientUtils::returnArray('企业存证成功', BaseConstant::SUCCESS_CODE, $result['data']);
    }

    /**
     * @param string $customerId
     * @param string $evidenceNo
     * @return array
     */
    public function applyClientNumberCert(string $customerId, string $evidenceNo): array
    {
        // TODO: Implement applyClientNumberCert() method.

        if (empty($customerId)) {
            return ClientUtils::returnArray('客户编号不能为空');
        }
        if (empty($evidenceNo)) {
            return ClientUtils::returnArray('存证编号不能为空');
        }
        $bizContent['customerId'] = $customerId;
        $bizContent['evidenceNo'] = $evidenceNo;
        $result = $this->client->request(BaseConstant::CERTIFICATE_EVIDENCE_NUMBER_APPLY_PATH, $bizContent);
        BaseLog::info(['法大大申请编号证书 push url ' . $this->client->apiUrl . BaseConstant::CERTIFICATE_EVIDENCE_NUMBER_APPLY_PATH . ' | $bizContent ' . json_encode($bizContent, JSON_UNESCAPED_UNICODE) . ' | return ' . json_encode($result, JSON_UNESCAPED_UNICODE)]);
        if (!isset($result['code']) || $result['code'] != BaseConstant::FDD_SUCCESS_CODE) {
            return ClientUtils::returnArray($result['msg'] ?? '编号证号申请失败');
        }
        return ClientUtils::returnArray('编号证号申请成功', BaseConstant::SUCCESS_CODE);
    }

    /**
     * @param string $accountId
     * @param string $name
     * @return array
     */
    public function customSignature(string $accountId, string $name): array
    {
        // TODO: Implement customSignature() method.

        if (empty($accountId)) {
            return ClientUtils::returnArray('客户编号不能为空');
        }
        if (empty($name)) {
            return ClientUtils::returnArray('印章名称不能为空');
        }
        $bizContent['accountId'] = $accountId;
        $bizContent['name'] = $name;
        $result = $this->client->request(BaseConstant::SEAL_CUSTOMIZE_PATH, $bizContent);
        BaseLog::info(['法大大自定义印章 push url ' . $this->client->apiUrl . BaseConstant::SEAL_CUSTOMIZE_PATH . ' | $bizContent ' . json_encode($bizContent, JSON_UNESCAPED_UNICODE) . ' | return ' . json_encode($result, JSON_UNESCAPED_UNICODE)]);
        if (!isset($result['code']) || !in_array($result['code'],[BaseConstant::FDD_SUCCESS_CODE, BaseConstant::FDD_SEAL_ALREADY_CODE, BaseConstant::FDD_SEAL_ALREADY_CODE_2])) {
            return ClientUtils::returnArray($result['msg'] ?? '商户自定义印章失败');
        }
        return ClientUtils::returnArray('商户自定义印章成功', BaseConstant::SUCCESS_CODE);

    }

    /**
     * @param string $tempNo
     * @param string $docTitle
     * @param string $docNo
     * @param string $fontSize
     * @param string $fontType
     * @param string $parameterMap
     * @param string $dynamicTables
     * @param string $qrInfo
     * @param string $personCustomerId
     * @param string $companyCustomerId
     * @return array
     */
    public function contractCreate(string $tempNo, string $docTitle, string $docNo, string $fontSize, string $fontType, string $parameterMap, string $dynamicTables, string $qrInfo, string $personCustomerId, string $companyCustomerId): array
    {
        // TODO: Implement contractCreate() method.

        $bizContent = [];
        if (empty($tempNo)) {
            return ClientUtils::returnArray('模板编号不能为空');
        }
        $bizContent['tempNo'] = $tempNo;
        if (empty($docTitle)) {
            return ClientUtils::returnArray('合同标题不能为空');
        }
        $bizContent['docTitle'] = $docTitle;
        if (empty($docNo)) {
            return ClientUtils::returnArray('合同编号不能为空');
        }
        $bizContent['docNo'] = $docNo;
        if (!empty($fontSize)) {
            $bizContent['fontSize'] = $fontSize;
        }
        if (!empty($fontType)) {
            $bizContent['fontType'] = $fontType;
        }
        if (empty($parameterMap)) {
            return ClientUtils::returnArray('填充内容不能为空');
        }
        $bizContent['parameterMap'] = $parameterMap;
        if (!empty($dynamicTables)) {
            $bizContent['dynamicTables'] = $dynamicTables;
        }
        if (!empty($qrInfo)) {
            $bizContent['qrInfo'] = $qrInfo;
        }
        if (empty($personCustomerId) && empty($companyCustomerId)) {
            return ClientUtils::returnArray('文档企业归属者客户编号，个人和企业不能同时为空');
        }
        if (!empty($personCustomerId)) {
            $bizContent['personCustomerId'] = $personCustomerId;
        }
        if (!empty($companyCustomerId)) {
            $bizContent['companyCustomerId'] = $companyCustomerId;
        }
        $result = $this->client->request(BaseConstant::CONTRACT_GENERATE_PATH, $bizContent);
        BaseLog::info(['法大大模板填充生成合同 push url ' . $this->client->apiUrl . BaseConstant::CONTRACT_GENERATE_PATH . ' | $bizContent ' . json_encode($bizContent, JSON_UNESCAPED_UNICODE) . ' | return ' . json_encode($result, JSON_UNESCAPED_UNICODE)]);
        if (!isset($result['code']) || $result['code'] != BaseConstant::FDD_SUCCESS_CODE) {
            return ClientUtils::returnArray($result['msg'] ?? '商户合同模板填充失败');
        }
        return ClientUtils::returnArray('商户合同模板填充成功', BaseConstant::SUCCESS_CODE);
    }


    /**
     * @param string $docNo
     * @param string $personSignerId
     * @param string $companySignerId
     * @param string $signDeadline
     * @param string $autoArchive
     * @param string $notifyUrl
     * @param string $sealName
     * @param int $locateMethod
     * @param string $locateKey
     * @param int $keywordStrategy
     * @param float $keywordOffsetX
     * @param float $keywordOffsetY
     * @param string $locateCoordinates
     * @param string $transactionNo
     * @param string $dateSeal
     * @return array
     */
    public function autoSignContract(string $docNo, string $personSignerId, string $companySignerId, string $signDeadline, string $autoArchive, string $notifyUrl, string $sealName, int $locateMethod, string $locateKey, int $keywordStrategy, float $keywordOffsetX, float $keywordOffsetY, string $locateCoordinates, string $transactionNo, string $dateSeal): array
    {
        // TODO: Implement autoSignContract() method.
        $bizContent = [];
        if (empty($docNo)) {
            return ClientUtils::returnArray('合同编号不能为空');
        }
        $bizContent['docNo'] = $docNo;
        if (!empty($personSignerId)) {
            $bizContent['personSignerId'] = $personSignerId;
        }
        if (!empty($companySignerId)) {
            $bizContent['companySignerId'] = $companySignerId;
        }
        if (empty($personSignerId) && empty($companySignerId)) {
            return ClientUtils::returnArray('签署个人客户编号和企业客户编号不能同时为空');
        }
        if (!empty($signDeadline)) {
            $bizContent['signDeadline'] = $signDeadline;
        }
        if (empty($autoArchive)) {
            return ClientUtils::returnArray('是否自动归档不能为空');
        }
        $bizContent['autoArchive'] = $autoArchive;
        if (empty($notifyUrl)) {
            return ClientUtils::returnArray('异步通知地址不能为空');
        }
        $bizContent['notifyUrl'] = $notifyUrl;
        if (!empty($sealName)) {
            $bizContent['sealName'] = $sealName;
        }
        if (empty($locateMethod)) {
            return ClientUtils::returnArray('定位方式不能为空');
        }
        $bizContent['locateMethod'] = $locateMethod;

        if (!empty($locateKey)) {
            $bizContent['locateKey'] = $locateKey;
        }
        if (!empty($keywordStrategy)) {
            $bizContent['keywordStrategy'] = $keywordStrategy;
        }
        if (!empty($keywordOffsetX)) {
            $bizContent['keywordOffsetX'] = $keywordOffsetX;
        }
        if (!empty($keywordOffsetY)) {
            $bizContent['keywordOffsetY'] = $keywordOffsetY;
        }
        if (!empty($locateCoordinates)) {
            $bizContent['locateCoordinates'] = $locateCoordinates;
        }
        if (empty($transactionNo)) {
            return ClientUtils::returnArray('交易号');
        }
        $bizContent['transactionNo'] = $transactionNo;
        if (!empty($dateSeal)) {
            $bizContent['dateSeal'] = $dateSeal;
        }
        $result = $this->client->request(BaseConstant::SIGN_AUTO_PATH, $bizContent);
        BaseLog::info(['法大大免验证签署 push url ' . $this->client->apiUrl . BaseConstant::SIGN_AUTO_PATH . ' | $bizContent ' . json_encode($bizContent, JSON_UNESCAPED_UNICODE) . ' | return ' . json_encode($result, JSON_UNESCAPED_UNICODE)]);
        if (!isset($result['code']) || $result['code'] != BaseConstant::FDD_SUCCESS_CODE) {
            return ClientUtils::returnArray($result['msg'] ?? '免验证签署失败');
        }
        return ClientUtils::returnArray('免验证签署成功', BaseConstant::SUCCESS_CODE, $result['data']);
    }

    /**
     * @param string $tempNo
     * @param string $tempTitle
     * @param string $tempPath
     * @param string $tempExtension
     * @param string $personCustomerId
     * @param string $companyCustomerId
     * @return array
     */
    public function createContractTemplate(string $tempNo, string $tempTitle, string $tempPath, string $tempExtension, string $personCustomerId, string $companyCustomerId): array
    {
        // TODO: Implement createContractTemplate() method.
        $bizContent = [];
        if (empty($tempNo)) {
            return ClientUtils::returnArray('模板编号不能为空');
        }
        $bizContent['tempNo'] = $tempNo;
        if (empty($tempTitle)) {
            return ClientUtils::returnArray('标题不能为空');
        }
        $bizContent['tempTitle'] = $tempTitle;
        if (empty($tempPath)) {
            return ClientUtils::returnArray('路径不能为空');
        }
        $bizContent['tempPath'] = $tempPath;
        if (empty($tempExtension)) {
            return ClientUtils::returnArray('扩展名不能为空');
        }
        $bizContent['tempExtension'] = $tempExtension;
        if (!empty($personCustomerId)) {
            $bizContent['personCustomerId'] = $personCustomerId;
        }
        if (!empty($companyCustomerId)) {
            $bizContent['companyCustomerId'] = $companyCustomerId;
        }
        if (empty($personCustomerId) && empty($companyCustomerId)) {
            return ClientUtils::returnArray('文档企业归属者客户编号，个人和企业不能同时为空');
        }
        $result = $this->client->request(BaseConstant::CONTRACT_TEMPLATE_SAVE_PATH, $bizContent);
        BaseLog::info(['法大大创建合同模板 push url ' . $this->client->apiUrl . BaseConstant::CONTRACT_TEMPLATE_SAVE_PATH . ' | $bizContent ' . json_encode($bizContent, JSON_UNESCAPED_UNICODE) . ' | return ' . json_encode($result, JSON_UNESCAPED_UNICODE)]);
        if (!isset($result['code']) || $result['code'] != BaseConstant::FDD_SUCCESS_CODE) {
            return ClientUtils::returnArray($result['msg'] ?? '创建合同模板失败');
        }
        return ClientUtils::returnArray('创建合同模板成功', BaseConstant::SUCCESS_CODE);

    }

    /**
     * @param string $docType
     * @param int $uploadType
     * @param string $filePath
     * @return array
     */
    public function fileUpload(string $docType, int $uploadType, string $filePath): array
    {
        // TODO: Implement fileUpload() method.
        $bizContent = [];
        if (empty($docType)) {
            return ClientUtils::returnArray('文件后缀不能为空');
        }
        $bizContent['docType'] = $docType;
        if (empty($uploadType)) {
            return ClientUtils::returnArray('上传类型不能为空');
        }
        $bizContent['uploadType'] = $uploadType;
        $files = Array();
        $files["file"] = $filePath;
        $result = $this->client->request(BaseConstant::FILE_UPLOAD_PATH, $bizContent, $files);
        BaseLog::info(['法大大上传文件 push url ' . $this->client->apiUrl . BaseConstant::FILE_UPLOAD_PATH . ' | $bizContent ' . json_encode($bizContent, JSON_UNESCAPED_UNICODE) . ' | return ' . json_encode($result, JSON_UNESCAPED_UNICODE)]);
        if (!isset($result['code']) || $result['code'] != BaseConstant::FDD_SUCCESS_CODE) {
            return ClientUtils::returnArray($result['msg'] ?? '法大大上传文件失败');
        }
        return ClientUtils::returnArray('法大大上传文件成功', BaseConstant::SUCCESS_CODE, $result['data']);


    }

    /**
     * @param string $docNo
     * @param string $watermarkInfo
     * @return array
     */
    public function contractDownload(string $docNo, string $watermarkInfo): array
    {
        $bizContent = [];
        if (empty($docNo)) {
            return ClientUtils::returnArray('合同编号不能为空');
        }
        $bizContent['docNo'] = $docNo;
        $result = $this->client->getRequestUrl(BaseConstant::CONTRACT_DOWNLOAD_PATH, $bizContent);
        BaseLog::info(['法大大合同下载 push url ' . $this->client->apiUrl . BaseConstant::CONTRACT_DOWNLOAD_PATH . ' | $bizContent ' . json_encode($bizContent, JSON_UNESCAPED_UNICODE) . ' | return ' . json_encode($result, JSON_UNESCAPED_UNICODE)]);
        return ClientUtils::returnArray('法大大合同下载成功', BaseConstant::SUCCESS_CODE, $result);
    }

    /**
     * @param string $docNo
     * @param bool $mobileDevice
     * @return array
     */
    public function contractView(string $docNo, bool $mobileDevice): array
    {
        $bizContent = [];
        if (empty($docNo)) {
            return ClientUtils::returnArray('合同编号不能为空');
        }
        $bizContent['docNo'] = $docNo;
        $bizContent['openTimesLimit'] = 2;
        $result = $this->client->getRequestUrl(BaseConstant::CONTRACT_VIEW_PATH, $bizContent);
        BaseLog::info(['法大大合同查看 push url ' . $this->client->apiUrl . BaseConstant::CONTRACT_VIEW_PATH . ' | $bizContent ' . json_encode($bizContent, JSON_UNESCAPED_UNICODE) . ' | return ' . json_encode($result, JSON_UNESCAPED_UNICODE)]);
        return ClientUtils::returnArray('法大大合同查看成功', BaseConstant::SUCCESS_CODE, $result);
    }

    /**
     * @param string $accountId
     * @return array
     */
    public function sealList(string $accountId): array
    {
        // TODO: Implement sealList() method.
        $bizContent = [];
        if (empty($accountId)) {
            return ClientUtils::returnArray('客户编号不能为空');
        }
        $bizContent['accountId'] = $accountId;
        $result = $this->client->request(BaseConstant::SEAL_LIST_PATH, $bizContent);
        BaseLog::info(['法大大查询印章 push url ' . $this->client->apiUrl . BaseConstant::SEAL_LIST_PATH . ' | $bizContent ' . json_encode($bizContent, JSON_UNESCAPED_UNICODE) . ' | return ' . json_encode($result, JSON_UNESCAPED_UNICODE)]);
        return ClientUtils::returnArray('法大大查询印章成功', BaseConstant::SUCCESS_CODE, $result['data'] ?? []);
    }

    /**
     * @param array $body
     * @return array
     */
    public function rollbackHandle(array $body): array
    {
        // TODO: Implement rollbackHandle() method.
        BaseLog::info(['法大大签署回调，body: '.json_encode($body)]);
        $sign = $body['sign'];
        unset($body['sign']);
        // 校验签名
        $realSign = ClientUtils::mkSign($body, $this->client->getRollbackAppKey());
        if ($sign != $realSign) {
            return ClientUtils::returnArray('签名错误');
        }
        // 解析参数
        try {
            $bizContent = json_decode(urldecode(base64_decode($body['bizContent'])), true);
        } catch (Exception $e) {
            BaseLog::info(['法大大签署回调参数解析错误，body:'.json_encode($body)]);
            return ClientUtils::returnArray('参数解析错误:'.$e->getMessage());
        }
        return ClientUtils::returnArray('法大大签署回调成功', BaseConstant::SUCCESS_CODE, $bizContent);
    }

}