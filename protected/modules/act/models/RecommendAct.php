<?php

/**
 * This is the model class for table "recommend_act".
 *
 * The followings are the available columns in table 'recommend_act':
 * @property string $id
 * @property string $u_id
 * @property string $img_id
 * @property string $act_name
 * @property string $act_time
 * @property string $act_address
 * @property string $act_contact
 * @property string $remark
 * @property double $lon
 * @property double $lat
 * @property string $address
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property UserInfo $u
 * @property ImgInfo $img
 */
class RecommendAct extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'recommend_act';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('img_id, create_time', 'required'),
			array('lon, lat', 'numerical'),
			array('u_id, img_id', 'length', 'max'=>10),
			array('act_name, act_time, act_address, act_contact, address', 'length', 'max'=>64),
			array('remark', 'length', 'max'=>240),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, u_id, img_id, act_name, act_time, act_address, act_contact, remark, lon, lat, address, create_time', 'safe', 'on'=>'search'),
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
			//'u' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
			//'img' => array(self::BELONGS_TO, 'ImgInfo', 'img_id'),
            
            'fkUser' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
			'fkUpImg' => array(self::BELONGS_TO, 'ImgInfo', 'img_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动推荐id',
			'u_id' => '用户id',
			'img_id' => '图像id',
			'act_name' => '名称',
			'act_time' => '时间',
			'act_address' => '地址',
			'act_contact' => '联系方式',
			'remark' => '备注',
			'lon' => '经度',
			'lat' => '纬度',
			'address' => '上传地址',
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
		$criteria->compare('u_id',$this->u_id,true);
		$criteria->compare('img_id',$this->img_id,true);
		$criteria->compare('act_name',$this->act_name,true);
		$criteria->compare('act_time',$this->act_time,true);
		$criteria->compare('act_address',$this->act_address,true);
		$criteria->compare('act_contact',$this->act_contact,true);
		$criteria->compare('remark',$this->remark,true);
		$criteria->compare('lon',$this->lon);
		$criteria->compare('lat',$this->lat);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RecommendAct the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 删除推荐信息
     * @param type $id
     */
    public function del($id)
    {
        $model = $this->findByPk($id);
        if (empty($model)) {
            return FALSE;
        }
        $model->status = ConstStatus::DELETE;
        return $model->update();
    }


    /**
     * 获取推荐活动信息
     * @param type $startTime
     * @param type $endTime
     * @param type $page
     * @param type $size
     * @param type $isDel
     */
    public function getRecommend($id)
    {
        $model = $this->with('fkUpImg')->findByPk($id);
        
        $recommend = array();
        $recommend['id'] = $model->id;
        if (!empty($model->fkUpImg)) {
            $recommend['img_url'] = Yii::app()->imgUpload->getDownUrl($model->fkUpImg->img_url);
        }
        $recommend['act_name'] = $model->act_name;
        $recommend['act_time'] = $model->act_time;
        $recommend['act_address'] = $model->act_address;
        $recommend['act_contact'] = $model->act_contact;
        $recommend['remark'] = $model->remark;
        $recommend['lon'] = $model->lon;
        $recommend['lat'] = $model->lat;
        $recommend['address'] = $model->address;
        $recommend['status'] = $model->status;
        $recommend['create_time'] = $model->create_time;
        if (!empty($model->u_id)) {
            $userModel = UserInfo::model()->findByPk($model->u_id);
            $user = array();
            $user['id'] = $userModel->id;
            $user['nick_name'] = $userModel->nick_name;
            $user['sex'] = $userModel->sex;
            $user['birth'] = $userModel->birth;
            $user['address'] = $userModel->address;
            $user['email'] = $userModel->email;
            $user['real_name'] = $userModel->real_name;
            $user['contact_qq'] = $userModel->contact_qq;
            $user['contact_phone'] = $userModel->contact_phone;
            $user['head_img_url'] = UserHeadImgMap::model()->getCurImgUrl($model->u_id);
            $user['status'] = $model->fkUser->status;
            $recommend['user'] = $user;
        }
        
        return $recommend;
    }
    
    
    /**
     * 搜索推荐活动信息列表
     * @param type $startTime
     * @param type $endTime
     * @param type $page
     * @param type $size
     * @param type $isDel
     */
    public function searchRecords($startTime, $endTime, $page, $size, $isDel = FALSE)
    {
        $cr = new CDbCriteria();
        if ($isDel) {
            $cr->compare('t.status', ConstStatus::DELETE);
        }  else {
            $cr->compare('t.status', '<>' . ConstStatus::DELETE);
        }
        if (!empty($startTime)) {
            $cr->compare('t.create_time', '>=' . $startTime);
        }
        if (!empty($endTime)) {
            $cr->compare('t.create_time', '<=' . $endTime);
        }
        $cr->with = array('fkUser.fkHeadImg.fkImg', 'fkUpImg');
        $count = $this->count($cr);
        
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $recommends = array();
        foreach ($rst as $v) {
            $recommend = array();
            $recommend['id'] = $v->id;
            if (!empty($v->fkUpImg)) {
                $recommend['img_url'] = Yii::app()->imgUpload->getDownUrl($v->fkUpImg->img_url);
            }
            $recommend['act_name'] = $v->act_name;
            $recommend['act_time'] = $v->act_time;
            $recommend['act_address'] = $v->act_address;
            $recommend['act_contact'] = $v->act_contact;
            $recommend['remark'] = $v->remark;
            $recommend['lon'] = $v->lon;
            $recommend['lat'] = $v->lat;
            $recommend['address'] = $v->address;
            $recommend['status'] = $v->status;
            $recommend['create_time'] = $v->create_time;
            if (empty($v->fkUser)) {
                array_push($recommends, $recommend);
                continue;
            }
            $user = array();
            $user['id'] = $v->fkUser->id;
            $user['nick_name'] = $v->fkUser->nick_name;
            $user['sex'] = $v->fkUser->sex;
            $user['birth'] = $v->fkUser->birth;
            $user['address'] = $v->fkUser->address;
            $user['email'] = $v->fkUser->email;
            $user['real_name'] = $v->fkUser->real_name;
            $user['contact_qq'] = $v->fkUser->contact_qq;
            $user['contact_phone'] = $v->fkUser->contact_phone;
            if (!empty($v->fkUser->fkHeadImg)) {
                $user['head_img_url'] = Yii::app()->imgUpload->getDownUrl($v->fkUser->fkHeadImg->fkImg->img_url);
            }
            $user['status'] = $v->fkUser->status;
            $recommend['user'] = $user;
            array_push($recommends, $recommend);
        }
        
        return array(
            'total_num' => $count,
            'recommends' => $recommends,
        );
    }
    
}
