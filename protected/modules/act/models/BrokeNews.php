<?php

/**
 * This is the model class for table "broke_news".
 *
 * The followings are the available columns in table 'broke_news':
 * @property string $id
 * @property string $city_id
 * @property string $u_id
 * @property string $contact_phone
 * @property string $contact_address
 * @property string $intro
 * @property double $lon
 * @property double $lat
 * @property string $address
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property CityInfo $city
 * @property UserInfo $u
 * @property BrokeNewsImgMap[] $brokeNewsImgMaps
 */
class BrokeNews extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'broke_news';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('create_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('lon, lat', 'numerical'),
			array('city_id, u_id', 'length', 'max'=>10),
			array('contact_phone, contact_address, address', 'length', 'max'=>64),
			array('intro', 'length', 'max'=>256),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, city_id, u_id, contact_phone, contact_address, intro, lon, lat, address, status, create_time', 'safe', 'on'=>'search'),
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
			//'city' => array(self::BELONGS_TO, 'CityInfo', 'city_id'),
			//'u' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
			//'brokeNewsImgMaps' => array(self::HAS_MANY, 'BrokeNewsImgMap', 'b_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动推荐id',
			'city_id' => '城市id',
			'u_id' => '用户id',
			'contact_phone' => '联系电话',
			'contact_address' => '联系地址',
			'intro' => '介绍',
			'lon' => '经度',
			'lat' => '纬度',
			'address' => '定位地址',
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
		$criteria->compare('city_id',$this->city_id,true);
		$criteria->compare('u_id',$this->u_id,true);
		$criteria->compare('contact_phone',$this->contact_phone,true);
		$criteria->compare('contact_address',$this->contact_address,true);
		$criteria->compare('intro',$this->intro,true);
		$criteria->compare('lon',$this->lon);
		$criteria->compare('lat',$this->lat);
		$criteria->compare('address',$this->address,true);
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
	 * @return BrokeNews the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加爆料
     * 
     * @param type $model 爆料数据
     * @param array $imgIds 图片id数组
     */
    public function add($model, array $imgIds = NULL)
    {
        if (empty($model)) {
            return FALSE;
        }
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        $rst = $model->save();
        if ($rst && !empty($imgIds)) {
            foreach ($imgIds as $v) {
                BrokeNewsImgMap::model()->add(NULL, $model->id, $v);
            }
        }
        return $rst;;
    }
    
}
