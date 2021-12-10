<?php
namespace fadada_sdk_goldentec\extend\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "{{%_client}}".
 *
 * @property integer $id
 * @property string $client_no
 * @property integer $user_id
 * @property integer $pid
 * @property integer $pioneer_type
 * @property integer $source
 * @property string $quota
 * @property string $template_id
 * @property string $merchant_id
 * @property string $channel
 * @property integer $service_type
 * @property string $enterpriseid
 * @property integer $is_hide_detail
 * @property integer $is_deleted
 * @property string $city_name
 * @property string $tax_licence_image
 * @property string $business_licence_image
 * @property string $bw_register_platform_code
 * @property string $bw_registration_code
 * @property string $bw_register_authorization_code
 * @property string $logo
 * @property string $name
 * @property string $client_nickname
 * @property string $operation
 * @property integer $state
 * @property string $tax_code
 * @property integer $type
 * @property string $sell_licence_image
 * @property integer $sc_id
 * @property string $sell_bank_name
 * @property string $sell_bank_account
 * @property string $sell_address
 * @property string $sell_phone
 * @property integer $provider_id
 * @property integer $machine
 * @property string $extra
 * @property string $drawer
 * @property string $reviewer
 * @property string $payee
 * @property string $email
 * @property integer $register_status
 * @property string $register_status_msg
 * @property string $goods_collect_name
 * @property string $register_platform_code
 * @property string $registration_code
 * @property string $register_authorization_code
 * @property string $region_code
 * @property string $industry
 * @property integer $invoice_mode
 * @property string $legal_person_name
 * @property string $contact_name
 * @property string $company_logo
 * @property string $applicant_name
 * @property string $applicant_phone
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $single_limit
 * @property string $order_id
 * @property string $invoice_type
 * @property integer $is_allow_invoice_static
 * @property integer $is_allow_invoice_dynamic
 * @property integer $is_allow_block_invoice_online
 * @property array paperQualificationList
 * @property string register_address
 * @property string customer_id
 * @property string evidence_no
 *
 * @property User $user
 * @property ClientStore $clientStore
 * @property StoreDrawer $storeDrawer
 * @property ServProviderAuth $providerAuth
 * @property Provider $provider
 */
class Client extends \yii\db\ActiveRecord
{

    public function behaviors(): array
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * 常量定义（字段枚举值）
     * 商户注册状态
     */
    CONST REGISTER_STATUS_REGISTERING = 0;
    CONST REGISTER_STATUS_REGISTERED = 1;
    CONST REGISTER_STATUS_ABORTED = 2;
    CONST REGISTER_STATUS_WORKER_ORDER_FAILED = 3;
    public static $register_status_config = [
        self::REGISTER_STATUS_REGISTERING => '正在注册',
        self::REGISTER_STATUS_REGISTERED => '注册成功',
        self::REGISTER_STATUS_ABORTED => '注册失败',
        self::REGISTER_STATUS_WORKER_ORDER_FAILED => '提交工单失败',
    ];

    /**
     * 常量定义（字段枚举值）
     * 商户状态
     */
    CONST CLIENT_STATE_FORBIDDEN = 0;
    CONST CLIENT_STATE_NORMAL = 1;
    CONST CLIENT_STATE_TO_BE_ACTIVATED  = 4;
    public static $client_status_config = [
        self::CLIENT_STATE_FORBIDDEN => '不能使用',
        self::CLIENT_STATE_NORMAL => '正常使用',
        self::CLIENT_STATE_TO_BE_ACTIVATED => '待激活',
    ];

    /**
     *
     */
    CONST DEFAULT_BUYER_TYPE_MERCHANT = 1;
    CONST DEFAULT_BUYER_TYPE_PERSON = 2;
    public static $default_buyer_type_config = [
        self::DEFAULT_BUYER_TYPE_MERCHANT => '企业',
        self::DEFAULT_BUYER_TYPE_PERSON => '个人及政府事业机构'
    ];

    /**
     *商户类型
     */
    CONST MERCHANT_TYPE_ORDINARY = 0;
    CONST MERCHANT_TYPE_PARTICULARLY = 1;
    CONST MERCHANT_TYPE_NO_SUCCESS_ORDER = 8;
    public static $MERCHANT_TYPE = [
        self::MERCHANT_TYPE_ORDINARY => '一般商户',
        self::MERCHANT_TYPE_PARTICULARLY => '特约商户'
    ];

    /**
     * 商户来源
     */
    CONST SOURCE_PC = 1;
    CONST SOURCE_YUM = 2;
    CONST SOURCE_CLOUD = 3;
    CONST SOURCE_BAIWANG = 4;
    CONST SOURCE_SERVICE = 5;
    CONST SOURCE_OPEN_PLATFORM = 6;
    CONST SOURCE_MANAGER_PLATFORM = 7;
    CONST SOURCE_YUNPIAOER = 8;
    CONST SOURCE_WANGDA = 9;
    CONST SOURCE_HAIDING = 10; //改成跟发票管理后台一样
    CONST SOURCE_BLOCK = 11;
    CONST SOURCE_JT = 12;
    CONST SOURCE_CARD_CENTER = 13;
    CONST SOURCE_TITLE_HELP = 14;
    CONST SOURCE_PLUG_CLIENT = 15;
    CONST SOURCE_MERCHANT_PLATFORM = 16;
    CONST SOURCE_YUNNAN_BLOCK = 17;
    CONST SOURCE_PTD = 18;
    CONST SOURCE_INTERNAL_GOLDEN_CLOUD = 19;//高灯云


    public static $SOURCE_REGISTER = [
        self::SOURCE_PC => 'PC端',
        self::SOURCE_YUM => '云票儿商家版',
        self::SOURCE_CLOUD => '云平台',
        self::SOURCE_BAIWANG => '百望插件',
        self::SOURCE_SERVICE => '服务商平台',
        self::SOURCE_OPEN_PLATFORM => '开放平台',
        self::SOURCE_MANAGER_PLATFORM => '管理后台',
        self::SOURCE_YUNPIAOER => '云票儿',//发票儿
        self::SOURCE_WANGDA => '万达',
        self::SOURCE_HAIDING => '海鼎',
        self::SOURCE_BLOCK => '区块链',
        self::SOURCE_JT => '集团',
        self::SOURCE_CARD_CENTER => '插卡中心',
        self::SOURCE_TITLE_HELP => '抬头助手',
        self::SOURCE_MERCHANT_PLATFORM => '新商户平台',//云平台
        self::SOURCE_YUNNAN_BLOCK => '云南区块链',
        self::SOURCE_PTD => '平谭岛',
        self::SOURCE_INTERNAL_GOLDEN_CLOUD => '高灯云'
    ];

    public static $register_source = [
        self::SOURCE_PC => 'business_platform',
        self::SOURCE_YUM => 'cloud_invoice',
        self::SOURCE_CLOUD => 'cloud_platform',
        self::SOURCE_BAIWANG => 'baiwang',
        self::SOURCE_SERVICE => 'service_platform',
        self::SOURCE_OPEN_PLATFORM => 'open_platform',
        self::SOURCE_MANAGER_PLATFORM => 'manager_platform',
        self::SOURCE_WANGDA => 'wanda',
        self::SOURCE_HAIDING => 'haiding',
        self::SOURCE_JT => 'business_platform',
        self::SOURCE_CARD_CENTER => 'card_center',
        self::SOURCE_TITLE_HELP => 'title_help',
        self::SOURCE_MERCHANT_PLATFORM => 'merchant_platform',//云平台
    ];

    CONST PIONEER_DEFAULT = 0; //默认值
    CONST PIONEER_HAI_NAN = 1;
    CONST PIONEER_GUANG_ZHOU = 2;
    public static $pioneers = [
        self::PIONEER_HAI_NAN => '海南先锋城市',
        self::PIONEER_GUANG_ZHOU => '广州先锋城市',
    ];

    //开票类型
    CONST INVOICE_TYPE_ELEC = 0;
    CONST INVOICE_TYPE_PAPER_COMMON = 1;
    CONST INVOICE_TYPE_PAPER_SPECIAL = 2;
    CONST INVOICE_TYPE_PAPER_TITLE = 3;
    CONST INVOICE_TYPE_BLOCK = 4;
    CONST INVOICE_TYPE_BAT = 5;
    CONST INVOICE_TYPE_ROLL = 6;
    public static $invoice_type_arr = [
        self::INVOICE_TYPE_ELEC => '电子发票',
        self::INVOICE_TYPE_PAPER_COMMON => '纸质发票(普票)',
        self::INVOICE_TYPE_PAPER_SPECIAL => '纸质发票(专票)',
        self::INVOICE_TYPE_PAPER_TITLE => '导入抬头',
        self::INVOICE_TYPE_BLOCK => '区块链电子发票',
        self::INVOICE_TYPE_BAT => '批量开票',
        self::INVOICE_TYPE_ROLL => '纸质发票(卷票)',
    ];
    public static $invoice_type_string = [
        self::INVOICE_TYPE_ELEC => 'elec',
        self::INVOICE_TYPE_PAPER_COMMON => 'paper_common',
        self::INVOICE_TYPE_PAPER_SPECIAL => 'paper_special',
        self::INVOICE_TYPE_PAPER_TITLE => 'paper_title',
        self::INVOICE_TYPE_BLOCK => 'blockchain',
        self::INVOICE_TYPE_ROLL => 'paper_roll',
    ];


    const INDUSTRY_JD = "jd";
    public static $industry_type = [
        self::INDUSTRY_JD => '酒店'
    ];

    //开票模式
    CONST INVOICE_MODE_STANDARD = 0;
    CONST INVOICE_MODE_HOTEL = 1;
    public static $invoice_mode_arr = [
        self::INVOICE_MODE_STANDARD => '标准开票',
        self::INVOICE_MODE_HOTEL => '智慧酒店开票'
    ];

    //区块链商户是否允许固定码开票
    CONST ALLOW_INVOICE_STATIC_NO = 0;
    CONST ALLOW_INVOICE_STATIC_YES = 1;
    public static $allow_invoice_static_txt = [
        self::ALLOW_INVOICE_STATIC_NO => '否',
        self::ALLOW_INVOICE_STATIC_YES => '是',
    ];

    //区块链商户是否允许动态码开票
    CONST ALLOW_INVOICE_DYNAMIC_NO = 0;
    CONST ALLOW_INVOICE_DYNAMIC_YES = 1;
    public static $allow_invoice_dynamic_txt = [
        self::ALLOW_INVOICE_DYNAMIC_NO => '否',
        self::ALLOW_INVOICE_DYNAMIC_YES => '是',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%sell_client_new}}';
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public static function getDb()
    {
        return Yii::$app->get('db_merchant');
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['user_id', 'pid', 'order_id', 'pioneer_type', 'source', 'service_type', 'is_hide_detail', 'is_deleted', 'state', 'type', 'sc_id', 'provider_id', 'machine', 'register_status', 'created_at', 'updated_at', 'is_allow_invoice_static', 'is_allow_invoice_dynamic'], 'integer'],
            [['quota'], 'number'],
            [['name'], 'required'],
            [['client_no', 'sell_phone'], 'string', 'max' => 20],
            [['template_id', 'city_name', 'bw_register_platform_code', 'bw_registration_code', 'bw_register_authorization_code', 'goods_collect_name', 'register_platform_code', 'registration_code', 'register_authorization_code', 'region_code', 'legal_person_name', 'contact_name'], 'string', 'max' => 50],
            [['merchant_id'], 'string', 'max' => 30],
            [['channel', 'name', 'sell_bank_account', 'email', 'client_nickname'], 'string', 'max' => 200],
            [['enterpriseid', 'tax_licence_image', 'business_licence_image', 'sell_licence_image'], 'string', 'max' => 255],
            [['logo', 'extra', 'company_logo'], 'string', 'max' => 500],
            [['operation', 'tax_code', 'sell_bank_name', 'drawer', 'reviewer', 'payee', 'register_status_msg'], 'string', 'max' => 100],
            [['sell_address'], 'string', 'max' => 300],
            [['register_address'], 'string', 'max' => 80],
            [['industry'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'client_no' => '企业编号',
            'order_id' => '套餐ID',
            'user_id' => '用户ID',
            'pid' => '父级ID',
            'pioneer_type' => '特约商户类型: 1海南先锋城市',
            'source' => '用户来源：1|PC 2|云票儿',
            'quota' => '单张开票限额',
            'template_id' => '微信卡包模板ID',
            'merchant_id' => '商户标识(用来区别大类,例如武汉天然气对应一个开票记录表)',
            'channel' => '渠道名称对应gp_invoice_platform 逗号隔开1,2,3',
            'service_type' => '业务类型;1:开票;3:插卡',
            'enterpriseid' => '航信返回企业唯一标示',
            'is_hide_detail' => '是否影藏发票商品明细 0:不隐藏; 1:隐藏商品明细',
            'is_deleted' => '0未删除 1已删除',
            'city_name' => '城市名称',
            'tax_licence_image' => '税务登记证的图片',
            'business_licence_image' => '营业执照图片',
            'bw_register_platform_code' => '百望平台编码',
            'bw_registration_code' => '百望企业平台注册码',
            'bw_register_authorization_code' => '百望企业平台授权码',
            'logo' => '商户logo(默认只存储图片名)',
            'name' => '商户名称',
            'client_nickname' => '商户昵称',
            'operation' => '经营范围',
            'state' => '商家状态 0无效 1有效 2禁止开蓝票 3禁止冲红',
            'tax_code' => '销方纳税人识别号(销方信息:税号)',
            'type' => '商户类型;0:一般商户;1:特约商户',
            'sell_licence_image' => '营业执照图片',
            'sc_id' => '开票中心原商户ID',
            'sell_bank_name' => '银行名称(销方信息:中国银行武汉市宝丰支行)',
            'sell_bank_account' => '银行账号(销方信息:802721140108091001)',
            'sell_address' => '销方地址(销方信息:地址信息)',
            'sell_phone' => '销方手机号(销方信息:企业座机号)',
            'provider_id' => '服务商ID',
            'machine' => '开票分机号(销方信息)',
            'extra' => '销方备注信息(销方信息)',
            'drawer' => '开票人(销方信息)',
            'reviewer' => '复核人(销方信息)',
            'payee' => '收款人(销方信息)',
            'email' => '邮箱地址',
            'register_status' => '商户注册状态 0:正在注册; 1:注册成功; 2:注册失败; 3:提交工单失败',
            'register_status_msg' => '注册返回信息',
            'goods_collect_name' => '当设置为隐藏发票时,所代替的商品名称',
            'register_platform_code' => '企业平台编码',
            'registration_code' => '企业平台注册码',
            'register_authorization_code' => '企业平台授权码',
            'region_code' => '企业平台地区编码',
            'industry' => '行业类型：jt 交通，tx通信',
            'invoice_mode' => '开票模式', //开票模式：0.标准开票;1.智慧酒店开票;
            'legal_person_name' => '注册企业法人代表名称',
            'company_logo' => '企业logo',
            'contact_name' => '联系人姓名',
            'applicant_name' => '申请人姓名',
            'applicant_phone' => '申请人手机号',
            'created_at' => '创建时间戳',
            'updated_at' => '更新时间戳',
            'single_limit' => '单笔限额',
            'is_allow_invoice_static' => '是否允许静态码开票',
            'is_allow_invoice_dynamic' => '是否允许设置金额动态码开票',
            'register_address' => '企业注册地址',
        ];
    }

    /**
     * @inheritdoc
     * @return ClientQuery the active query used by this AR class.
     */
    public static function find(): ClientQuery
    {
        return new ClientQuery(get_called_class());
    }
}

