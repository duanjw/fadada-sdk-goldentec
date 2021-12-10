<?php

namespace fadada_sdk_goldentec\constant;

final class BaseConstant
{
    /**
     * 法大大成功的状态码
     */
    public const FDD_SUCCESS_CODE = '1';

    /**
     * 法大大印章已经存在状态码
     */
    public const FDD_SEAL_ALREADY_CODE = '1006006';

    /**
     * 法大大印章已经存在状态码-2 不知道为什么会出现两种情况，法大大建议都使用
     */
    public const FDD_SEAL_ALREADY_CODE_2 = '1006010';

    /**
     * 法大大模板已经存在状态码
     */
    public const FDD_TEMPLATE_ALREADY_CODE = '1004002';

    /**
     * 成功的状态码
     */
    public const SUCCESS_CODE = 0;

    /**
     * 个人
     */
    public const FDD_ACCOUNT_TYPE_PERSON = 1;

    /**
     * 企业
     */
    public const FDD_ACCOUNT_TYPE_COMPANY = 2;

    /**
     * fdd注册
     */
    public const ACCESS_REGISTER_PATH = "/account/register";

    /**
     * 实名哈希存证
     */
    public const EVIDENCE_HASH_SAVE_PATH = "/evidence/hash/save";

    /**
     * 存证申请编号证书
     */
    public const CERTIFICATE_EVIDENCE_NUMBER_APPLY_PATH = "/certificate/evidence/number/apply";

    /**
     * 自定义印章
     */
    public const SEAL_CUSTOMIZE_PATH = "/seal/customize";

    /**
     * 模板填充生成合同
     */
    public const CONTRACT_GENERATE_PATH = "/contract/generate";

    /**
     * 模板填充生成合同
     */
    public const SIGN_AUTO_PATH = "/sign/auto";

    /**
     * 创建合同模板
     */
    public const CONTRACT_TEMPLATE_SAVE_PATH = "/contract/template/save";

    /**
     * 上传接口
     */
    public const FILE_UPLOAD_PATH = "/file/upload";

    /**
     * 合同下载
     */
    public const CONTRACT_DOWNLOAD_PATH = "/contract/download";

    /**
     * 合同查看
     */
    public const CONTRACT_VIEW_PATH = "/contract/view";

    /**
     * 查询印章
     */
    public const SEAL_LIST_PATH = "/seal/list";


}