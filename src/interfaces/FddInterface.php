<?php

namespace duanjw\fadada_sdk_goldentec\interfaces;

/**
 * Interface FddInterface
 *
 */
Interface FddInterface
{
    /**
     * @param int $type 注册类型：1:个人，2:企业
     * @param string $account 注册唯一标识
     * @param string $adminAccountId 管理员客户编号
     * @param string $companyName 企业名称
     * @return array
     */
    public function accountRegister(int $type, string $account, string $adminAccountId, string $companyName): array;

    /**
     * @param string $customerId 客户编号
     * @param int $isRepeatVerified 是否重复存证，1-首次存证，2-重新存证
     * @param int $type 存证类型 1-个人，2-企业
     * @param string $preservationName 存证名称
     * @param string $preservationDesc 存证描述
     * @param string $fileName 文件名
     * @param string $noperTime 文件最后修改时间,unix时间，单位s
     * @param string|int $fileSize 文件大小
     * @param string $originalSha256 文件hash值
     * @param string $transactionId 交易号
     * @return array
     */
    public function HashAuth(string $customerId, int $isRepeatVerified, int $type, string $preservationName,
                             string $preservationDesc, string $fileName, string $noperTime, string $fileSize,
                             string $originalSha256, string $transactionId): array;


    /**
     * 申请编号证书
     * @param string $customerId
     * @param string $evidenceNo
     * @return array
     */
    public function applyClientNumberCert(string $customerId, string $evidenceNo): array;

    /**
     * 自定义印章
     * @param string $accountId
     * @param string $name
     * @return array
     */
    public function customSignature(string $accountId, string $name): array;

    /**
     * @param string $docType 文件后缀
     * @param int $uploadType 上传类型
     * @param string $file 文件path
     * @return array
     */
    public function fileUpload(string $docType, int $uploadType, string $file): array;

    /**
     * 模板填充生成合同
     * @param string $tempNo 模板编号
     * @param string $docTitle 合同标题
     * @param string $docNo 合同编号
     * @param string $fontSize 字体大小
     * @param string $fontType 字体类型
     * @param string $parameterMap 填充内容
     * @param string $dynamicTables 动态表格
     * @param string $qrInfo 二维码信息
     * @param string $personCustomerId 文档个人归属者客户编号，个人和企业不能同时为空
     * @param string $companyCustomerId 文档企业归属者客户编号，个人和企业不能同时为空
     * @return array
     */
    public function contractCreate(string $tempNo, string $docTitle, string $docNo, string $fontSize, string $fontType,
                                   string $parameterMap, string $dynamicTables, string $qrInfo, string $personCustomerId, string $companyCustomerId): array;

    /**
     * 免验证签署
     * @param string $docNo 合同编号
     * @param string $personSignerId 签署个人客户编号和企业客户编号不能同时为空
     * @param string $companySignerId 签署个人客户编号和企业客户编号不能同时为空
     * @param string $signDeadline 截止时间
     * @param string $autoArchive 是否自动归档
     * @param string $notifyUrl 异步通知地址
     * @param string $sealName 印章名称
     * @param int $locateMethod 定位方式（1-关键字/2-坐标)
     * @param string $locateKey 定位关键字
     * @param int $keywordStrategy 定位关键字策略
     * @param float $keywordOffsetX 关键字X轴偏移量,默认为0.00，不偏移
     * @param float $keywordOffsetY 关键字Y轴偏移量,默认为0.00，不偏移
     * @param string $locateCoordinates 坐标
     * @param string $transactionNo 交易号
     * @param string $dateSeal 是否加盖签署日期,1:是;0:否(默认)
     * @return array
     */
    public function autoSignContract(string $docNo, string $personSignerId, string $companySignerId, string $signDeadline,
                                     string $autoArchive, string $notifyUrl, string $sealName, int $locateMethod, string $locateKey,
                                     int $keywordStrategy, float $keywordOffsetX, float $keywordOffsetY, string $locateCoordinates,
                                     string $transactionNo, string $dateSeal): array;

    /**
     * 创建合同模板
     * @param string $tempNo
     * @param string $tempTitle
     * @param string $tempPath
     * @param string $tempExtension
     * @param string $personCustomerId
     * @param string $companyCustomerId
     * @return array
     */
    public function createContractTemplate(string $tempNo, string $tempTitle, string $tempPath, string $tempExtension,
                                           string $personCustomerId, string $companyCustomerId): array;

    /**
     * 合同下载
     * @param string $docNo 合同编号
     * @param string $watermarkInfo 水印信息
     * @return array
     */
    public function contractDownload(string $docNo, string $watermarkInfo): array;

    /**
     * 合同查看
     * @param string $docNo
     * @param bool $mobileDevice
     * @return array
     */
    public function contractView(string $docNo, bool $mobileDevice): array;

    /**
     * 查询印章
     * @param string $accountId
     * @return array
     */
    public function sealList(string $accountId): array;

    /**
     * 签署结果回调处理，主要是来解析参数，校验签名
     * @param array $body
     * @return array
     */
    public function rollbackHandle(array $body): array;

}
