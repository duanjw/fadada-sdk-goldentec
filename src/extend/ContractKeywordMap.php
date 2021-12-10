<?php
/**
 * User: bridger
 * Date: 2020/8/21
 */

namespace fadada_sdk_goldentec\extend;

use fadada_sdk_goldentec\extend\models\ClientContractAddress;
use fadada_sdk_goldentec\extend\models\Client;

class ContractKeywordMap
{

    const HAINAN_GAODENG = '海南高灯科技有限公司';


    public static function getContractKeywordMap($map, $client_id): array
    {
        $temp = [];
        if (empty($map)) {
            return $temp;
        }
        $client = Client::find()->where(['id' => $client_id])->asArray()->one();
        foreach ($map as $key => $val) {

            if ($val['key'] == 'signedB') {
                $temp[$val['key']] = $client['name'] ?? '商户名称';
                continue;
            }
            if ($val['key'] == 'siteB') {
                $clientContractAddress = ClientContractAddress::findOne(['client_id' => $client_id]);
                if ($clientContractAddress) {
                    $sell_address = ClientUtils::getAreas($clientContractAddress->contract_region_code) . $clientContractAddress->contract_address;
                } else {
                    $sell_address = $client['sell_address']?? '商户地址';
                }
                $temp[$val['key']] = $sell_address;
                continue;
            }
            if ($val['key'] == 'phoneB') {
                $temp[$val['key']] =isset($client['sell_phone']) ? $client['sell_phone'] : '企业联系电话';
                continue;
            }
            if ($val['key'] == 'emailB') {
                $temp[$val['key']] = isset($client['email']) ? $client['email'] : '';
                continue;
            }
            if ($val['key'] == 'dateB') {
                $temp[$val['key']] = date('Y年m月d日');
                continue;
            }
            if ($val['key'] == 'nameB') {
                $temp[$val['key']] = isset($client['name']) ? $client['name'] : '商户名称';
                continue;
            }
            if ($val['key'] == 'taxB') {
                $temp[$val['key']] = isset($client['tax_code']) ? $client['tax_code'] : '商户税号';
                continue;
            }
            if ($val['key'] == 'dateA') {
                $temp[$val['key']] = date('Y年m月d日');
                continue;
            }
            if ($val['key'] == 'nameA') {
                $temp[$val['key']] = self::HAINAN_GAODENG;
                continue;
            }
            if ($val['key'] == 'liaisonB') {
                $temp[$val['key']] = isset($client['legal_person_name']) ? $client['legal_person_name'] : '企业法人';
                continue;
            }
            if ($val['key'] == 'timeB') {
                $temp[$val['key']] = date('Y年m月d日');
                continue;
            }

            if (!empty($val['value'])) {
                $temp[$val['key']] = $val['value'];
                continue;
            }
        }
        return $temp;
    }

}