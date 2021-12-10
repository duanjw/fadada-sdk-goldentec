<?php

namespace duanjw\fadada_sdk_goldentec\extend\models;

use Yii;

/**
 * This is the model class for table "{{%areas}}".
 *
 * @property integer $area_id
 * @property string $area_name
 * @property string $area_name_en
 * @property integer $area_parent_id
 * @property integer $area_sort
 * @property integer $area_deep
 * @property string $area_region
 * @property integer $country_id
 * @property string $code
 * @property integer $type
 */
class Areas extends \yii\db\ActiveRecord
{
    const LEVEL_ONE = 1;
    const LEVEL_TWO = 2;
    const LEVEL_THREE = 3;
    const LEVEL_FOUR = 4;

    const AREA_NOT_RELY_ON_TYPE_FILTER = ['510129112','510182102','510184102','510115103'];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%areas}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['area_name'], 'required'],
            [['area_parent_id', 'area_sort', 'area_deep', 'country_id', 'type'], 'integer'],
            [['area_name', 'area_name_en'], 'string', 'max' => 255],
            [['area_region'], 'string', 'max' => 3],
            [['code'], 'string', 'max' => 6],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'area_id' => '索引ID',
            'area_name' => '地区名称',
            'area_name_en' => 'Area Name En',
            'area_parent_id' => '地区父ID',
            'area_sort' => '排序',
            'area_deep' => '地区深度，从1开始',
            'area_region' => '大区名称',
            'country_id' => '国家id',
            'code' => 'Code',
            'type' => 'Type',
        ];
    }

    public static function getOriginalByAreaId($areaId)
    {
        if (empty($areaId)) {
            return false;
        }

        $sql = "SELECT area_deep FROM gpi_areas WHERE area_id = :area_id";
        $result = Yii::$app->db->createCommand($sql, [':area_id' => $areaId])->queryOne();
        if($result && isset($result['area_deep'])){
            $areaDeep = $result['area_deep'];
        }else{
            return false;
        }

        if (intval($areaDeep) == 4) {
            return self::getOriginalByBericaStreetId($areaId);
        }
        $sql = "SELECT a.area_id as province_id,a.area_name as province_name,b.area_id as city_id,b.area_name as city_name,c.area_id,c.area_name from gpi_areas a INNER JOIN gpi_areas b on a.area_id = b.area_parent_id INNER JOIN gpi_areas c on b.area_id = c.area_parent_id  WHERE a.area_deep = 1 and b.area_deep = 2 and c.area_deep = 3 and c.area_id = :area_id";
        $result = Yii::$app->db->createCommand($sql, [':area_id' => $areaId])->queryOne();
        return $result;
    }

    public static function getOriginalByBericaStreetId($streetId)
    {
        if(empty($streetId))
        {
            return false;
        }
        $sql = "SELECT a.area_id as province_id,a.area_name as province_name,b.area_id as city_id,b.area_name as city_name,c.area_id,c.area_name,d.area_id as street_id,d.area_name as street_name from gpi_areas a INNER JOIN gpi_areas b on a.area_id = b.area_parent_id INNER JOIN gpi_areas c on b.area_id = c.area_parent_id INNER JOIN gpi_areas d on c.area_id = d.area_parent_id  WHERE a.area_deep = 1 and b.area_deep = 2 and c.area_deep = 3 and d.area_deep = 4 and d.area_id = :area_id";
        $result = Yii::$app->db->createCommand($sql,[':area_id'=>$streetId])->queryOne();
        return $result;
    }
}
