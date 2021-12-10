<?php

namespace fadada_sdk_goldentec\constant;


class ContractError
{

    public static $SYSTEM_ERROR                     = array(500000,'系统错误');
    public static $CONTRACT_HAS_EXIST               = array(500001,'已经存在基础合同,不能重复签署');
    public static $CONTRACT_SIGN_FAIL               = array(500002,'签署合同盖章失败');
    public static $CONTRACT_TEMPLATE_FAIL               = array(500003,'获取模板信息失败');
    public static $CONTRACT_PARAMS_FAIL               = array(500004,'参数验证失败');
    public static $CONTRACT_MERCHANT_CUSTOMER_ID_SAVE_ERR       = array(500005,'保存customer_id失败');
    public static $CONTRACT_MERCHANT_EVIDENCE_NO_SAVE_ERR       = array(500006,'保存evidence_no失败');
    public static $CONTRACT_TEMPLATE_CONTENT_EMPTY_ERR       = array(500007,'合同模板填充内容不能为空');
    public static $CONTRACT_MERCHANT_INFO_SAVE_ERR       = array(500008,'保存商户信息失败');
}
