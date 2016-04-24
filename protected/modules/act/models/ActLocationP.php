<?php

/**
 * This is the model class for table "act_location_p".
 *
 * The followings are the available columns in table 'act_location_p':
 * @property string $id
 * @property string $l_id
 * @property double $lon
 * @property double $lat
 * @property string $addr_city
 * @property string $addr_area
 * @property string $addr_road
 * @property string $addr_num
 * @property string $addr_name
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 *
 * The followings are the available model relations:
 * @property ActLocation $l
 */
class ActLocationP extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_location_p';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('l_id, status, create_time, update_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('lon, lat', 'numerical'),
			array('l_id', 'length', 'max'=>10),
			array('addr_city, addr_area, addr_road, addr_num, addr_name', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, l_id, lon, lat, addr_city, addr_area, addr_road, addr_num, addr_name, status, create_time, update_time', 'safe', 'on'=>'search'),
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
			//'l' => array(self::BELONGS_TO, 'ActLocation', 'l_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '位置id',
			'l_id' => '所属地点id',
			'lon' => '经度',
			'lat' => '纬度',
			'addr_city' => '地址（城市）',
			'addr_area' => '地址（区）',
			'addr_road' => '地址（路）',
			'addr_num' => '地址（号）',
			'addr_name' => '地址（名称）',
			'status' => '状态',
			'create_time' => '创建时间',
			'update_time' => '修改时间',
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
		$criteria->compare('l_id',$this->l_id,true);
		$criteria->compare('lon',$this->lon);
		$criteria->compare('lat',$this->lat);
		$criteria->compare('addr_city',$this->addr_city,true);
		$criteria->compare('addr_area',$this->addr_area,true);
		$criteria->compare('addr_road',$this->addr_road,true);
		$criteria->compare('addr_num',$this->addr_num,true);
		$criteria->compare('addr_name',$this->addr_name,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_time',$this->update_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActLocationP the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 地点对应的停车点
     * 
     * @param type $locationId 地点id
     */
    public function parkings($locationId, $page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.l_id', $locationId);
        $cr->compare('t.status', '<>' . ConstStatus::DELETE);
        $count = $this->count($cr);
        $cr->order = 't.id asc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $parkings = array();
        foreach ($rst as $v) {
            $model = array();
            $model['id'] = $v->id;
            $model['lon'] = $v->lon;
            $model['lat'] = $v->lat;
            $model['addr_city'] = $v->addr_city;
            $model['addr_area'] = $v->addr_area;
            $model['addr_road'] = $v->addr_road;
            $model['addr_num'] = $v->addr_num;
            $model['addr_name'] = $v->addr_name;
            array_push($parkings, $model);
        }
        
        return array(
            'total_num' => $count,
            'parkings' => $parkings,
        );
    }
    
}
