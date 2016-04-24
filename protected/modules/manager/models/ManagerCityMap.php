<?php

/**
 * This is the model class for table "manager_city_map".
 *
 * The followings are the available columns in table 'manager_city_map':
 * @property string $id
 * @property string $m_id
 * @property string $city_id
 * @property string $type
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property ManagerInfo $m
 * @property CityInfo $city
 */
class ManagerCityMap extends ManagerModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'manager_city_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('m_id, city_id, type, status, create_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('m_id, city_id, type', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, m_id, city_id, type, status, create_time', 'safe', 'on'=>'search'),
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
			//'m' => array(self::BELONGS_TO, 'ManagerInfo', 'm_id'),
			//'city' => array(self::BELONGS_TO, 'CityInfo', 'city_id'),
            
            'fkManager' => array(self::BELONGS_TO, 'ManagerInfo', 'm_id'),
            'fkCity' => array(self::BELONGS_TO, 'CityInfo', 'city_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '城市管理员与城市关联id',
			'm_id' => '城市管理员id',
			'city_id' => '城市id',
			'type' => '类型：1管理员，2操作员',
			'status' => '状态',
			'create_time' => '创建时间',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('m_id',$this->m_id,true);
		$criteria->compare('city_id',$this->city_id,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ManagerCityMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 是否允许添加
     * 
     * @param type $cityId 城市id
     * @param type $type 类型
     */
    public function cityCanAdd($cityId, $type) 
    {
        if (ConstCityManagerStatus::CITY_MANAGER == $type) {
            $cr = new CDbCriteria();
            $cr->compare('t.city_id', $cityId);
            $cr->compare('t.type', ConstCityManagerStatus::CITY_MANAGER);
            $model = $this->find($cr);
            if (!empty($model)) {
                return FALSE;
            }
        }
        return TRUE;
    }


    /**
     * 添加城市管理员与城市关联
     * 一个mid只能对应一个城市，一个城市只能有一个type为1
     * 
     * @param type $mid
     * @param type $cityId
     * @param type $type
     */
    public function add($mid, $cityId, $type)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.m_id', $mid);
        $model = $this->find($cr);
        if (!empty($model)) {
            return FALSE;
        }
        if (ConstCityManagerStatus::CITY_MANAGER == $type) {
            $cr = new CDbCriteria();
            $cr->compare('t.city_id', $cityId);
            $cr->compare('t.type', ConstCityManagerStatus::CITY_MANAGER);
            $model = $this->find($cr);
            if (!empty($model)) {
                return FALSE;
            }
        }
        $model = new ManagerCityMap();
        $model->m_id = $mid;
        $model->city_id = $cityId;
        $model->type = $type;
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        return $model->save();
    }


    /**
     * 城市管理员列表
     * 
     * @param type $type 类型
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function cityManagers($type = NULL, $page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.status', ConstStatus::NORMAL);
        if (!empty($type)) {
            $cr->compare('t.type', $type);
        }
        
        $cr->with = array('fkCity', 'fkManager');
        $cr->compare('fkCity.status', ConstStatus::NORMAL);
        $cr->compare('fkManager.status', ConstStatus::NORMAL);
        
        $count = $this->count($cr);
        $cr->order = 't.id ';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $cityManagers = array();
        foreach ($rst as $v) {
            $cityManager = CityManager::model()->profile($v->m_id);
            $city = CityInfoM::model()->city($v->city_id);
            if (empty($cityManager) || empty($city)) {
                continue;
            }
            $cityManger['city_id'] = $v->city_id;
            $cityManger['city_name'] = $city['name'];
            $cityManger['type'] = $v->type;
            array_push($cityManagers, $cityManager);
        }
        
        return array(
            'total_num' => $count,
            'city_managers' => $cityManagers,
        );
    }
    
    
    /**
     * 城市管理员信息
     * 
     * @param type $mid 管理员id
     */
    public function cityManager($mid)
    {
        $model = $this->find('m_id=:mid and status=0', array(':mid' => $mid));
        if (empty($model)) {
            return NULL;
        }
        $cityManger = CityManager::model()->profile($model->m_id);
        $city = CityInfoM::model()->city($model->city_id);
        if (empty($cityManger) || empty($city)) {
            return NULL;
        }
        
        $cityManger['city_id'] = $model->city_id;
        $cityManger['city_name'] = $city['name'];
        $cityManger['type'] = $model->type;
        return $cityManger;
    }
    
}
