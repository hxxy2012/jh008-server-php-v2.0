<?php

/**
 * This is the model class for table "org_city_map".
 *
 * The followings are the available columns in table 'org_city_map':
 * @property string $id
 * @property string $org_id
 * @property string $city_id
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property OrgInfo $org
 * @property CityInfo $city
 */
class OrgCityMap extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'org_city_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('org_id, city_id, create_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('org_id, city_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, org_id, city_id, status, create_time', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			//'org' => array(self::BELONGS_TO, 'OrgInfo', 'org_id'),
			//'city' => array(self::BELONGS_TO, 'CityInfo', 'city_id'),
            
            'fkOrg' => array(self::BELONGS_TO, 'OrgInfo', 'org_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '组织与城市关联id',
			'org_id' => '组织id',
			'city_id' => '城市id',
			'status' => '状态',
			'create_time' => '创建时间',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrgCityMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 社团列表
     * 
     * @param type $cityId 城市id
     * @param type $keyWords 关键字
     * @param type $page 页数
     * @param type $size 每页记录数
     * @param type $currUid 当前用户id
     */
    public function orgs($cityId, $keyWords, $page, $size, $currUid = NULL)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.city_id', $cityId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $cr->with = 'fkOrg';
        $cr->compare('fkOrg.name', $keyWords, TRUE);
        $cr->compare('fkOrg.status', ConstStatus::NORMAL);
        $count = $this->count($cr);
        
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        $orgs = array();
        foreach ($rst as $v) {
            if (empty($v->fkOrg)) {
                continue;
            }
            $org = OrgInfo::model()->profile(NULL, $v->fkOrg, $currUid);
            if (empty($org)) {
                continue;
            }
            array_push($orgs, $org);
        }
        
        return array(
            'total_num' => $count,
            'orgs' => $orgs,
        );
    }
    
}
