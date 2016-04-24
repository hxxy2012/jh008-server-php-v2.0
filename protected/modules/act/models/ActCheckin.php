<?php

/**
 * This is the model class for table "act_checkin_map".
 *
 * The followings are the available columns in table 'act_checkin_map':
 * @property string $id
 * @property string $act_id
 * @property string $u_id
 * @property double $lon
 * @property double $lat
 * @property string $address
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property ActInfo $act
 * @property UserInfo $u
 */
class ActCheckin extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_checkin';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('act_id, create_time', 'required'),
			array('lon, lat', 'numerical'),
			array('act_id, u_id', 'length', 'max'=>10),
			array('address', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, act_id, u_id, lon, lat, address, create_time', 'safe', 'on'=>'search'),
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
			//'act' => array(self::BELONGS_TO, 'ActInfo', 'act_id'),
			//'u' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
            
            'fkAct' => array(self::BELONGS_TO, 'ActInfo', 'act_id'),
            'fkUser' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动签到关联id',
			'act_id' => '活动id',
			'u_id' => '用户id',
			'lon' => '经度',
			'lat' => '纬度',
			'address' => '地理位置信息',
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
		$criteria->compare('act_id',$this->act_id,true);
		$criteria->compare('u_id',$this->u_id,true);
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
	 * @return ActCheckin the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 活动签到
     * 
     * @param type $model 签到信息
     * @param type $act_id 活动id
     * @param type $u_id 用户id
     * @param type $lon 经度
     * @param type $lat 纬度
     * @param type $address 地理位置信息
     */
    public function add($model, $act_id = NULL, $u_id = NULL, $lon = NULL, $lat = NULL, $address = NULL)
    {
        if (empty($model)) {
            $model = new ActCheckin();
        }
        if (!empty($act_id)) {
            $model->act_id = $act_id;
        }
        if (!empty($u_id)) {
            $model->u_id = $u_id;
        }
        
        if ($this->check($model->act_id, $model->u_id)) {
            return FALSE;
        }
        
        if (!empty($lon)) {
            $model->lon = $lon;
        }
        if (!empty($lat)) {
            $model->lat = $lat;
        }
        if (!empty($address)) {
            $model->address = $address;
        }
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
    
    /**
     * 检测签到是否存在
     * @param type $actId
     * @param type $uid
     */
    public function check($actId, $uid)
    {
        $rst = $this->find('act_id=:actId and u_id=:uid', array('actId' => $actId, 'uid' => $uid));
        if (empty($rst)) {
            return FALSE;
        }
        return TRUE;
    }


    /**
     * 签到
     */
    public function checkin($model, $uid)
    {
        $model->u_id = $uid;
        $model->status = 0;
        $model->create_time = date("Y-m-d H:i:s", time());
        $r = $model->save();
        if ($r) {
            $this->pushToFans(ActInfo::model()->findByPk($model->act_id), $model->u_id);
        }
        return $r;
    }


    /**
     * 用户签到列表
     * 
     * @param type $uid 用户id
     * @param type $page 页数
     * @param type $size 每页记录数
     * @param type $currUid 当前用户id
     */
    public function userCheckins($uid, $page, $size, $currUid = NULL)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $uid);
        $cr->compare('t.status', '<>' . ConstStatus::DELETE);
        
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $checkins = array();
        foreach ($rst as $v) {
            $checkin = array();
            $checkin['id'] = $v->id;
            $checkin['act_id'] = $v->act_id;
            $act = ActInfo::model()->profile(NULL, $v->act_id, empty($currUid) ? $uid : $currUid, FALSE);
            if (empty($act)) {
                continue;
            }
            $checkin['act'] = $act;
            $checkin['create_time'] = $v->create_time;
            array_push($checkins, $checkin);
        }
        
        return array(
            'total_num' => $count,
            'checkins' => $checkins
        );
    }


    /**
     * 历史签到
     */
    public function getHistory($uid, $page, $size)
    {
        $criteria = new CDbCriteria();
        $criteria->with = 'fkAct.fkTags.fkTag';
        $criteria->compare('t.u_id', $uid);
        $totalNum = $this->count($criteria);
        $criteria->order = 't.id desc';
        $criteria->offset = ($page - 1) * $size;
        $criteria->limit = $size;
        $rst = $this->findAll($criteria);
        
        $checkinActs = array();
        foreach ($rst as $k => $v) {
            $checkin = array();
            $checkin['id'] = $v->id;
            $checkin['act_id'] = $v->fkAct->id;
            $checkin['act_title'] = $v->fkAct->title;
            $checkin['act_addr_city'] = $v->fkAct->addr_city;
            $checkin['act_addr_area'] = $v->fkAct->addr_area;
            $checkin['act_addr_road'] = $v->fkAct->addr_road;
            $checkin['act_addr_num'] = $v->fkAct->addr_num;
            $checkin['create_time'] = $v->create_time;
            
            $tags = $v->fkAct->fkTags;
            $checkin['act_tags'] = array();
            foreach ($tags as $key => $value) {
                if ($value->fkTag->status == -1) {
                    continue;
                }
                $tag = array();
                $tag['id'] = $value->fkTag->id;
                $tag['name'] = $value->fkTag->name;
                $tag['count'] = $value->fkTag->count;
                array_push($checkin['act_tags'], $tag);
            }
            array_push($checkinActs, $checkin);
        }
        return array(
            'total_num' => $totalNum,
            'checkins' => $checkinActs,
        );
    }
    
    
    /**
     * 获取签到的信息及用户信息
     * @param type $actId
     * @param type $keyWords
     */
    public function getCheckinWithUsers($actId, $keyWords, $page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('act_id', $actId);
        $cr->with = array('fkUser.fkHeadImg.fkImg');
        if (!empty($keyWords)) {
            $crs = new CDbCriteria();
            $crs->compare('user_name', $keyWords, TRUE, 'OR');
            $crs->compare('nick_name', $keyWords, TRUE, 'OR');
            $crs->compare('real_name', $keyWords, TRUE, 'OR');
            $cr->mergeWith($crs);
        }
        $count = $this->count($cr);
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $checkins = array();
        foreach ($rst as $k => $v) {
            $checkin = array();
            $checkin['id'] = $v->id;
            $checkin['lon'] = $v->lon;
            $checkin['lat'] = $v->lat;
            $checkin['address'] = $v->address;
            $checkin['status'] = $v->status;
            $checkin['create_time'] = $v->create_time;
            $checkin['descri'] = $v->descri;
            if (empty($v->fkUser)) {
                array_push($checkins, $checkin);
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
            $checkin['user'] = $user;
            array_push($checkins, $checkin);
        }
        return array(
            'total_num' => $count,
            'checkins' => $checkins,
        );
    }
    
    
    /**
     * 标注签到
     * @param type $id
     */
    public function mark($id) 
    {
        $model = $this->findByPk($id);
        if (empty($model)) {
            return FALSE;
        }
        if (ConstStatus::NORMAL != $model->status) {
            return FALSE;
        }
        $model->status = 1;
        return $model->update();
    }
    
    
    /**
     * 取消标注签到
     * @param type $id
     */
    public function unMark($id)
    {
        $model = $this->findByPk($id);
        if (empty($model)) {
            return FALSE;
        }
        if (1 != $model->status) {
            return FALSE;
        }
        $model->status = ConstStatus::NORMAL;
        return $model->update();
    }
    
    
    /**
     * 修改签到备注
     * @param type $id
     * @param type $descri
     */
    public function updateDescri($id, $descri)
    {
        $model = $this->findByPk($id);
        if (empty($model)) {
            return FALSE;
        }
        $model->descri = $descri;
        return $model->update();
    }
    
    
    /**
     * 给用户粉丝推送此用户签到动作
     * 
     * @param type $act
     * @param type $focusUser
     */
    public function pushToFans($act, $focusUser)
    {
        if (empty($act) || empty($focusUser)) {
            return FALSE;
        }
        $msg = PushMsgContentTool::makeActCheckin(
                $focusUser->nick_name
                );
        return PushMsgTask::model()->add(
                ConstPushMsgTaskType::TO_USER_FANS, 
                $focusUser->id, 
                $focusUser->id, 
                $msg['title'], 
                $msg['descri'], 
                PushMsgContentTool::makeFilterForAct($act->id)
                );
    }
    
}
