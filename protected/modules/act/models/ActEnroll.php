<?php

/**
 * This is the model class for table "act_enroll".
 *
 * The followings are the available columns in table 'act_enroll':
 * @property string $id
 * @property string $act_id
 * @property string $u_id
 * @property string $name
 * @property string $birth
 * @property integer $sex
 * @property string $phone
 * @property integer $with_people_num
 * @property double $lon
 * @property double $lat
 * @property string $address
 * @property integer $status
 * @property string $create_time
 * @property string $modify_time
 * @property integer $group_id
 * @property integer $serial_no
 *
 * The followings are the available model relations:
 * @property ActInfo $act
 * @property UserInfo $u
 */
class ActEnroll extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_enroll';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('act_id, u_id, name, birth, sex, phone, create_time, modify_time', 'required'),
			array('sex, with_people_num, status, group_id, serial_no', 'numerical', 'integerOnly'=>true),
			array('lon, lat', 'numerical'),
			array('act_id', 'length', 'max'=>10),
			array('name, phone', 'length', 'max'=>32),
			array('address', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, act_id, u_id, name, birth, sex, phone, with_people_num, lon, lat, address, status, create_time, modify_time, group_id, serial_no', 'safe', 'on'=>'search'),
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
            'fkUser' => array(self::BELONGS_TO, 'UserInfo', 'u_id', 'on' => 'fkUser.status=' . ConstStatus::NORMAL),
            'fkHeadImg' => array(self::HAS_ONE, 'UserHeadImgMap', '', 'on' => 't.u_id = fkHeadImg.u_id and fkHeadImg.status=1'),
            'fkCustomExtActMap' =>  array(self::BELONGS_TO, 'ActInfo', 'act_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动报名关联id',
			'act_id' => '活动id',
			'u_id' => '用户id',
			'name' => '姓名',
			'birth' => '生日',
			'sex' => '性别',
			'phone' => '联系电话',
			'with_people_num' => '随行人数',
			'lon' => '经度',
			'lat' => '纬度',
			'address' => '地理位置信息',
			'status' => '状态',
			'create_time' => '创建时间',
			'modify_time' => '修改时间',
			'group_id' => 'TA的分组id',
			'serial_no' => '序号',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActEnroll the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 是否是活动成员
     * 
     * @param type $actId 活动id
     * @param type $uid 用户id
     */
    public function isMember($actId, $uid)
    {
        $model = $this->get($actId, $uid);
        if (empty($model)) {
            return FALSE;
        }
        if (ConstCheckStatus::PASS == $model->status) {
            return TRUE;
        }
        return FALSE;
    }
    
    
    /**
     * 获取用户报名情况
     * 
     * @param type $act_id 活动id
     * @param type $u_id 用户id
     */
    public function get($act_id, $u_id)
    {
        $cr = new CDbCriteria();
        $cr->compare('act_id', $act_id);
        $cr->compare('u_id', $u_id);
        return $this->find($cr);
    }
    
    
    /**
     * 用户的活动报名后相关信息
     * 
     * @param type $actId 活动id
     * @param type $uid 用户id
     */
    public function userStatus($actId, $uid) 
    {
        $cr = new CDbCriteria();
        $cr->compare('act_id', $actId);
        $cr->compare('u_id', $uid);
        $model = $this->find($cr);
        
        if (empty($model)) {
            return array(
                'group_id' => -1,
                'enroll_status' => -1,
                'serial_no' => -1,
            );
        }
        
        return array(
            'group_id' => empty($model->group_id) ? -1 : $model->group_id,
            'enroll_status' => $model->status,
            'serial_no' => $model->serial_no,
        );
    }


    /**
     * 添加
     */
    public function add()
    {
        $this->create_time = date('Y-m-d H:i:s');
        $this->modify_time = date('Y-m-d H:i:s');
        $r = $this->save();
        if ($r && ConstCheckStatus::PASS == $this->status) {
            $act = ActInfo::model()->findByPk($this->act_id);
            $focusUser = UserInfo::model()->findByPk($this->u_id);
            $this->pushToFans(ActInfo::model()->findByPk($act), $focusUser);
        }
        return $r;
    }
    
    
    /**
     * 修改
     */
    public function up()
    {
        $this->modify_time = date('Y-m-d H:i:s');
        $r = $this->update();
        if ($r && ConstCheckStatus::PASS == $this->status) {
            $this->pushToFans(ActInfo::model()->findByPk($this->act_id), $this->u_id);
        }
        return $r;
    }
    
    
    /**
     * 用户报名列表
     * 
     * @param type $uid 用户id
     * @param type $page 页数
     * @param type $size 每页记录数
     * @param type $currUid 当前用户id
     */
    public function userEnrolls($uid, $page, $size, $currUid = NULL)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $uid);
        $cr->compare('t.status', ConstCheckStatus::PASS);
        $cr->with = 'fkAct';
        $cr->compare('fkAct.status', ConstActStatus::PUBLISHING);
        
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $enrolls = array();
        foreach ($rst as $v) {
            $enroll = array();
            $enroll['id'] = $v->id;
            $enroll['act_id'] = $v->act_id;
            $enroll['name'] = $v->name;
            $enroll['sex'] = $v->sex;
            $enroll['bith'] = $v->birth;
            $enroll['phone'] = $v->phone;
            $enroll['with_people_num'] = $v->with_people_num;
            $enroll['create_time'] = $v->create_time;
            $enroll['status'] = $v->status;
            if (empty($v->fkAct)) {
                continue;
            }
            $enroll['act'] = ActInfo::model()->profile($v->fkAct, NULL, empty($currUid) ? $uid : $currUid, TRUE);
            array_push($enrolls, $enroll);
        }
        return array(
            'total_num' => $count,
            'enrolls' => $enrolls
        );
    }
    
    
    /**
     * 给用户粉丝推送此用户报名动作
     * 
     * @param type $act
     * @param type $focusUser
     */
    public function pushToFans($act, $focusUser)
    {
        if (empty($act) || empty($focusUser)) {
            return FALSE;
        }
        $msg = PushMsgContentTool::makeActEnroll(
                $focusUser->nick_name, 
                $act->title
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
    
    
    /**
     * 活动成员
     * 
     * @param type $actId 活动id
     * @param type $page 页数
     * @param type $size 每页记录数
     * @param type $cityId 城市id
     * @param type $uid 当前用户id
     */
    public function members($actId, $page, $size, $cityId, $uid)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.u_id', '>0');
        $cr->compare('t.status', ConstCheckStatus::PASS);
        $count = $this->count($cr);
        $cr->order = 't.id asc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $users = array();
        foreach ($rst as $v) {
            $user = UserInfo::model()->profile(NULL, $v->u_id, $cityId, $uid, NULL, FALSE);
            if (empty($user)) {
                continue;
            }
            array_push($users, $user);
        }
        return array(
            'total_num' => $count,
            'users' => $users,
        );
    }
    
    
    /**
     * 分组成员列表
     * 
     * @param type $groupId 分组id
     * @param type $page 页数
     * @param type $size 每页记录数
     * @param type $cityId 城市id
     * @param type $uid 当前用户id
     */
    public function groupUsers($groupId, $page, $size, $cityId, $uid)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.group_id', $groupId);
        $cr->compare('t.u_id', '>0');
        $cr->compare('t.status', ConstCheckStatus::PASS);
        $count = $this->count($cr);
        $cr->order = 't.id asc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $users = array();
        foreach ($rst as $v) {
            $user = UserInfo::model()->profile(NULL, $v->u_id, $cityId, $uid, NULL, FALSE);
            if (empty($user)) {
                continue;
            }
            array_push($users, $user);
        }
        return array(
            'total_num' => $count,
            'users' => $users,
        );
    }
    
    
    public function groupUsersId($groupId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.group_id', $groupId);
        $cr->compare('t.status', ConstCheckStatus::PASS);
        $cr->with = 'fkHeadImg.fkImg';
                
        $rst =  $this->findAll($cr);
        $users = array();
        foreach($rst as $v){
            $users[] = array(
                'u_id' => $v->u_id,
                'name' => $v->name,
                'group_id'=>$v->group_id,
                'head_img_url'=> Yii::app()->imgUpload->getDownUrl($v->fkHeadImg->fkImg->img_url),
            );
        }
        return $users;
    }
    
    
    /**
     * 报名通过人数
     * 
     * @param type $actId 活动id
     * @param type $sex 性别：1男，2女
     */
    public function enrollPeopleNum($actId, $sex = NULL)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.status', ConstCheckStatus::PASS);
        if (!empty($sex)) {
            $cr->with = 'fkUser.fkExtend';
            $cr->compare('fkExtend.sex', $sex);
        }
        return $this->count($cr);
    }
    
    
    /**
     * 正在提交以及已经已报名的人数
     * 
     * @param type $actId 活动id
     * @param type $sex 性别：1男，2女
     */
    public function commitingNum($actId, $sex = NULL) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.status', array(
            ConstCheckStatus::COMMIT,
            ConstCheckStatus::INVIEW,
            ConstCheckStatus::PASS,
            ));
        if (!empty($sex)) {
            $cr->with = 'fkUser.fkExtend';
            $cr->compare('fkExtend.sex', $sex);
        }
        $hasCommitNum = $this->count($cr);
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.status', ConstCheckStatus::NOT_COMMIT);
        $cr->compare('t.modify_time', '<' . date('Y-m-d H:i:s', time() - 60 * 15));
        if (!empty($sex)) {
            $cr->with = 'fkUser.fkExtend';
            $cr->compare('fkExtend.sex', $sex);
        }
        return $this->count($cr) + $hasCommitNum;
    }
    
    
    /**
     * 分组成员的个数
     * 
     * @param type $groupId 分组id
     */
    public function countGroupUserNum($groupId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.group_id', $groupId);
        $cr->compare('t.status', ConstCheckStatus::PASS);
        return $this->count($cr);
    }
    
    private function age($dob){
        $dob = strtotime($dob);
        if(!$dob){
            return '';
        }
        $y = date('Y', $dob);
        if (($m = (date('m') - date('m', $dob))) < 0) {
            $y++;
        } elseif ($m == 0 && date('d') - date('d', $dob) < 0) {
            $y++;
        }
        return date('Y') - $y;
    }
    
    /**
     * 获取活动报名列表
     * @param type $actId 活动id
     * @param type $status 报名状态
     * @return int
     */
    public function getEnrollList($actId, $status, $sort = 'desc')
    {
        //这个活动的自定义字段
        $list = array('custom_keys' => array(), 'enroll_list' => array());
        $customKeys = CustomExtActMap::model()->allCustomKeys($actId);
        //$list['custom_keys'] = $customKeys;
        foreach ($customKeys as $key){
            $list['custom_keys'][] = $key['subject'];
        }
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.status', $status);
        $cr->order = 't.id ' . $sort;
        //$cr->with = 'fkHeadImg.fkImg';
        $rst =  $this->findAll($cr);
        if(!$rst){
            return $list;
        }
 
        //var_dump($customKeys);
        foreach($rst as $v){
            $customKeyValues = array();
            if($customKeys){
                foreach ($customKeys as $key){
                    $info =  CustomExtActUserVal::model()->get($key['id'], $actId, $v->u_id);
                    $customKeyValues[$key['subject']] = $info ? $info->value : '';
                }
            }
            
            array_push($list['enroll_list'], array_merge(array(
                'enroll_id' => $v->id,
                'u_id' => $v->u_id,
                'real_name' => $v->name,
                'contact_phone'=>$v->phone,
                'sex' => $v->sex,
                'birth' => $v->birth,
                'age' => $this->age($v->birth),
                'status' => $v->status,
                'group_id' => $v->group_id,
                //'head_img_url'=> Yii::app()->imgUpload->getDownUrl($v->fkHeadImg->fkImg->img_url),
            ),$customKeyValues));
        }
        return $list;
    }
    
    
    public function getNextEnrollUser($actId, $lastUid = 0)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.status', ConstCheckStatus::PASS);
        $cr->compare('t.u_id', '>' , $lastUid);
        $cr->select  = 't.u_id as id';
        $cr->distinct = true;
        $cr->order = 't.u_id asc';
        $rst = $this->find($cr);
        if($rst){
            return $rst->attributes;
        }
        return FALSE;
    }
    
}