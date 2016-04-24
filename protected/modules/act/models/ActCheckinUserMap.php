<?php

/**
 * This is the model class for table "act_checkin_user_map".
 *
 * The followings are the available columns in table 'act_checkin_user_map':
 * @property string $id
 * @property string $step_id
 * @property string $u_id
 * @property double $lon
 * @property double $lat
 * @property string $address
 * @property integer $status
 * @property string $create_time
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property ActCheckinStep $step
 * @property UserInfo $u
 */
class ActCheckinUserMap extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_checkin_user_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('step_id, u_id, status, create_time, modify_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('lon, lat', 'numerical'),
			array('step_id, u_id', 'length', 'max'=>10),
			array('address', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, step_id, u_id, lon, lat, address, status, create_time, modify_time', 'safe', 'on'=>'search'),
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
			//'step' => array(self::BELONGS_TO, 'ActCheckinStep', 'step_id'),
			//'u' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
            
            'fkStep' => array(self::BELONGS_TO, 'ActCheckinStep', 'step_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动签到环节与用户关联id',
			'step_id' => '活动签到环节id',
			'u_id' => '用户id',
			'lon' => '经度',
			'lat' => '纬度',
			'address' => '地址信息',
			'status' => '状态',
			'create_time' => '创建时间',
			'modify_time' => '修改时间',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActCheckinUserMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 用户在活动里已签到的
     * 
     * @param type $actId 活动id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function userCheckins($actId, $uid, $page, $size) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $uid);
        $cr->compare('t.status', '<>' . ConstStatus::DELETE);
        $cr->with = 'fkStep';
        $cr->compare('fkStep.act_id', $actId);
        $count = $this->count($cr);
        $cr->order = 't.id asc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $checkins = array();
        foreach ($rst as $v) {
            $checkin = ActCheckinStep::model()->profile(NULL, $v->fkStep);
            if (empty($checkin)) {
                continue;
            }
            $checkin['status'] = $v->status;
            array_push($checkins, $checkin);
        }
        
        return array(
            'total_num' => $count,
            'checkins' => $checkins,
        );
    }
    
    
    /**
     * 签到
     * 
     * @param type $stepid 签到id
     * @param type $uid 用户id
     * @param type $lon 经度
     * @param type $lat 纬度
     * @param type $address 地址信息
     */
    public function checkin($stepid, $uid, $lon, $lat, $address)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.step_id', $stepid);
        $cr->compare('t.u_id', $uid);
        $model = $this->find($cr);
        
        $isNew = FALSE;
        if (empty($model)) {
            $model = new ActCheckinUserMap();
            $model->step_id = $stepid;
            $model->u_id = $uid;
            $isNew = TRUE;
        }  else {
            if (ConstStatus::DELETE != $model->status) {
                return ActCheckinStep::model()->profile($model->step_id);
            }
        }
        $model->lon = $lon;
        $model->lat = $lat;
        $model->address = $address;
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        $model->modify_time = date('Y-m-d H:i:s');
        $r = FALSE;
        if ($isNew) {
            $r = $model->save();
        }
        $r = $model->update();
        
        if (!$r) {
            return FALSE;
        }
        $actId = ActCheckinStep::model()->actId($model->step_id);
        $firstStepId = ActCheckinStep::model()->find('t.act_id=:actId', array(':actId' => $actId));
        if ($firstStepId == $model->step_id) {
            $act = ActInfo::model()->findByPk($actId);
            $focusUser = UserInfo::model()->findByPk($model->u_id);
            $this->pushToFans($act, $focusUser);
        }
        return ActCheckinStep::model()->profile($model->step_id);
    }
    
    
    /**
     * 签到确认
     * 
     * @param type $stepid 签到id
     * @param type $uid 用户id
     */
    public function sureCheckin($stepid, $uid)
    {
        $model = $this->get($stepid, $uid);
        if (empty($model) || ConstStatus::DELETE == $model->status) {
            return FALSE;
        }
        $model->status = 1;
        $model->modify_time = date('Y-m-d H:i:s');
        return $model->update();
    }
    
    
    /**
     * 获取model
     * 
     * @param type $stepid 签到id
     * @param type $uid 用户id
     */
    public function get($stepid, $uid)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.step_id', $stepid);
        $cr->compare('t.u_id', $uid);
        return $this->find($cr);
    }
    
    
    /**
     * 验证是否可以签到
     * 
     * @param type $actId
     * @param type $uid
     * @param type $stepId
     */
    public function valid($actId, $uid, $stepId) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $uid);
        $cr->compare('t.status', '<>' . ConstStatus::DELETE);
        $cr->with = 'fkStep';
        $cr->compare('fkStep.act_id', $actId);
        $cr->order = 't.id desc';
        $model = $this->find($cr);
        if (!empty($model) && !empty($model->fkStep) && 1 == $model->fkStep->need_sure && ConstStatus::NORMAL == $model->status) {
            //未点击确认无法继续进行之后的签到
            return FALSE;
        }
        //找活动的下一签到的id
        $checkin = ActCheckinStep::model()->nextCheckin(
                $actId, 
                empty($model) ? NULL : $model->step_id
                );
        if (empty($checkin)) {
            //没有需要签到的id了
            return FALSE;
        }
        if ($checkin->id != $stepId) {
            //与应该进行的签到id不匹配
            return FALSE;
        }
        return TRUE;
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
