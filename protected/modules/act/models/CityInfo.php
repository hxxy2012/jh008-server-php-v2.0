<?php

/**
 * 城市数据表
 * This is the model class for table "city_info".
 *
 * The followings are the available columns in table 'city_info':
 * @property string $id
 * @property string $name
 * @property string $parent_id
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property ActInfo[] $actInfos
 * @property BrokeNews[] $brokeNews
 * @property CityActTagMap[] $cityActTagMaps
 * @property CityVipRandomDynamicsTask[] $cityVipRandomDynamicsTasks
 * @property ManagerCityMap[] $managerCityMaps
 * @property NewsInfo[] $newsInfos
 * @property OrgCityMap[] $orgCityMaps
 * @property UserCityMap[] $userCityMaps
 * @property UserFeedback[] $userFeedbacks
 * @property UserInfoExtend[] $userInfoExtends
 * @property VipApplyCityMap[] $vipApplyCityMaps
 */
class CityInfo extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'city_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>128),
			array('parent_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, parent_id, status', 'safe', 'on'=>'search'),
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
			//'actInfos' => array(self::HAS_MANY, 'ActInfo', 'city_id'),
			//'brokeNews' => array(self::HAS_MANY, 'BrokeNews', 'city_id'),
			//'cityActTagMaps' => array(self::HAS_MANY, 'CityActTagMap', 'city_id'),
			//'cityVipRandomDynamicsTasks' => array(self::HAS_MANY, 'CityVipRandomDynamicsTask', 'last_max_city_id'),
			//'managerCityMaps' => array(self::HAS_MANY, 'ManagerCityMap', 'city_id'),
			//'newsInfos' => array(self::HAS_MANY, 'NewsInfo', 'city_id'),
			//'orgCityMaps' => array(self::HAS_MANY, 'OrgCityMap', 'city_id'),
			//'userCityMaps' => array(self::HAS_MANY, 'UserCityMap', 'city_id'),
			//'userFeedbacks' => array(self::HAS_MANY, 'UserFeedback', 'city_id'),
			//'userInfoExtends' => array(self::HAS_MANY, 'UserInfoExtend', 'last_login_city_id'),
			//'vipApplyCityMaps' => array(self::HAS_MANY, 'VipApplyCityMap', 'city_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '城市id',
			'name' => '名称',
			'parent_id' => '层级',
			'status' => '状态',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CityInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 获取可用的城市列表
     * 
     * @param type $page 分页：页码
     * @param type $size 分页：每页条数
     */
    public function cities($page, $size)
    {
        $cr = new CDbCriteria();
        if (!empty($page) && !empty($size)) {
            $cr->offset = ($page - 1) * $size;
            $cr->limit = $size;
        }
        $cr->compare('status', 1);
        $count = $this->count($cr);
        $rst = $this->findAll($cr);
        
        $cities = array();
        foreach ($rst as $v) {
            $city = array();
            $city['id'] = $v->id;
            $city['name'] = $v->name;
            $city['status'] = $v->status;
            array_push($cities, $city);
        }
        
        return array(
            'total_num' => $count,
            'cities' => $cities
        );
    }
    
}
