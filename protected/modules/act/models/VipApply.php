<?php

/**
 * This is the model class for table "vip_apply".
 *
 * The followings are the available columns in table 'vip_apply':
 * @property string $id
 * @property string $author_id
 * @property string $real_name
 * @property string $contact_phone
 * @property string $email
 * @property string $intro
 * @property double $lon
 * @property double $lat
 * @property string $address
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property UserInfo $author
 * @property VipApplyCityMap[] $vipApplyCityMaps
 * @property VipApplyImgMap[] $vipApplyImgMaps
 */
class VipApply extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'vip_apply';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('author_id, create_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('lon, lat', 'numerical'),
			array('author_id', 'length', 'max'=>10),
			array('real_name, contact_phone, email', 'length', 'max'=>32),
			array('address', 'length', 'max'=>64),
			array('intro', 'length', 'max'=>240),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, author_id, real_name, contact_phone, email, intro, lon, lat, address, status, create_time', 'safe', 'on'=>'search'),
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
			'author' => array(self::BELONGS_TO, 'UserInfo', 'author_id'),
			'vipApplyCityMaps' => array(self::HAS_MANY, 'VipApplyCityMap', 'apply_id'),
			'vipApplyImgMaps' => array(self::HAS_MANY, 'VipApplyImgMap', 'apply_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '达人申请id',
			'author_id' => '申请者id',
			'real_name' => '真实姓名',
			'contact_phone' => '联系电话',
			'email' => '常用邮箱',
			'intro' => '个人简介',
			'lon' => '经度',
			'lat' => '纬度',
			'address' => '地址',
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
		$criteria->compare('author_id',$this->author_id,true);
		$criteria->compare('real_name',$this->real_name,true);
		$criteria->compare('contact_phone',$this->contact_phone,true);
		$criteria->compare('email',$this->email,true);
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
	 * @return VipApply the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 申请成为达人
     * 
     * @param type $model 申请资料的model
     * @param type $author_id 申请者用户id
     * @param type $cityIds 城市id数组
     * @param type $tagIds 活动分类标签id数组
     * @param type $userTagIds 用户标签id数组
     * @param type $imgIds 图片id数组
     */
    public function add($model, $author_id, array $cityIds, array $tagIds, array $userTagIds, array $imgIds)
    {
        $model->author_id = $author_id;
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        $rst = $model->save();
        if (!$rst) {
            return FALSE;
        }
        
        foreach ($cityIds as $v) {
            VipApplyCityMap::model()->add($model->id, $v);
        }
        
        foreach ($tagIds as $v) {
            VipApplyTagMap::model()->add($model->id, $v);
        }
        
        foreach ($userTagIds as $v) {
            VipApplyUserTagMap::model()->add($model->id, $v);
        }
        
        foreach ($imgIds as $v) {
            VipApplyImgMap::model()->add($model->id, $v);
        }
        
        return TRUE;
    }
    
}
