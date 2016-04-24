<?php

/**
 * This is the model class for table "user_contact".
 *
 * The followings are the available columns in table 'user_contact':
 * @property string $id
 * @property string $u_id
 * @property string $contact_id
 * @property integer $status
 * @property string $modify_time
 * @property string $last_msg_id
 *
 * The followings are the available model relations:
 * @property UserInfo $u
 * @property UserInfo $contact
 * @property UserMessage $lastMsg
 */
class UserContact extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_contact';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('u_id, contact_id, modify_time, last_msg_id', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('u_id, contact_id, last_msg_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, u_id, contact_id, status, modify_time, last_msg_id', 'safe', 'on'=>'search'),
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
			//'contact' => array(self::BELONGS_TO, 'UserInfo', 'contact_id'),
			//'lastMsg' => array(self::BELONGS_TO, 'UserMessage', 'last_msg_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '用户与联系人关联id',
			'u_id' => '用户id',
			'contact_id' => '联系人id',
			'status' => '状态',
			'modify_time' => '创建时间',
			'last_msg_id' => '最后一条消息的id',
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
		$criteria->compare('contact_id',$this->contact_id,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('modify_time',$this->modify_time,true);
		$criteria->compare('last_msg_id',$this->last_msg_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserContact the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 是否屏蔽
     * 
     * @param type $uid 用户id
     * @param type $contactId 联系人id
     */
    public function isShield($uid, $contactId)
    {
        $cr = new CDbCriteria();
        $cr->compare('u_id', $uid);
        $cr->compare('contact_id', $contactId);
        $model = $this->find($cr);
        if (!empty($model) && $model->status == 1) {
            return TRUE;
        }
        return FALSE;
    }


    /**
     * 更新联系人
     * 
     * @param type $u_id
     * @param type $contact_id
     * @param type $last_msg_id
     * @return type
     */
    public function up($u_id, $contact_id, $last_msg_id)
    {
        $cr = new CDbCriteria();
        $cr->compare('u_id', $u_id);
        $cr->compare('contact_id', $contact_id);
        $model = $this->find($cr);
        
        if (empty($model)) {
            $model = new UserContact();
            $model->u_id = $u_id;
            $model->contact_id = $contact_id;
            $model->status = ConstStatus::NORMAL;
            $model->modify_time = date('Y-m-d H:i:s');
            $model->last_msg_id = $last_msg_id;
            return $model->save();
        }
        
        //1为已屏蔽提醒的联系人
        if (1 == $model->status) {
            return FALSE;
        }
        $model->status = ConstStatus::NORMAL;
        $model->modify_time = date('Y-m-d H:i:s');
        $model->last_msg_id = $last_msg_id;
        return $model->update();
    }
    
    
    /**
     * 联系人
     * 
     * @param type $uid
     */
    public function contacts($uid, $cityId, $page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('u_id', $uid);
        $cr->compare('status', '<>' . ConstStatus::DELETE);
        
        $count = $this->count($cr);
        $cr->order = 'last_msg_id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $users = array();
        foreach ($rst as $v) {
            $user = UserInfo::model()->profile(NULL, $v->contact_id, $cityId, $uid, NULL, FALSE);
            $user['last_contact_time'] = $v->modify_time;
            $user['new_msg_num'] = UserMessageMap::model()->countMessages($v->u_id, $v->contact_id);
            $msg = UserMessage::model()->msg($v->last_msg_id);
            $user['content'] = empty($msg) ? NULL : $msg['content'];
            $user['status'] = $v->status;
            array_push($users, $user);
        }
        
        return array(
            'total_num' => $count,
            'users' => $users
        );
    }
    
    
    /**
     * 删除用户的联系人（暂时删除，有新消息恢复成为联系人）
     * 
     * @param type $u_id
     * @param type $contact_id
     */
    public function del($u_id, $contact_id)
    {
        $cr = new CDbCriteria();
        $cr->compare('u_id', $u_id);
        $cr->compare('contact_id', $contact_id);
        $cr->compare('status', '<>' . ConstStatus::DELETE);
        $model = $this->find($cr);
        if (empty($model)) {
            return FALSE;
        }
        $model->status = ConstStatus::DELETE;
        return $model->update();
    }
    
    
    /**
     * 屏蔽用户的联系人（不更新提示最新消息）
     * 
     * @param type $u_id
     * @param type $contact_id
     */
    public function shield($u_id, $contact_id)
    {
        $cr = new CDbCriteria();
        $cr->compare('u_id', $u_id);
        $cr->compare('contact_id', $contact_id);
        //$cr->compare('status', '<>' . ConstStatus::DELETE);
        $model = $this->find($cr);
        if (empty($model)) {
            return FALSE;
        }
        $model->status = 1;
        return $model->update();
    }
    
    
    /**
     * 取消屏蔽用户的联系人（不更新提示最新消息）
     * 
     * @param type $u_id
     * @param type $contact_id
     */
    public function delShield($u_id, $contact_id)
    {
        $cr = new CDbCriteria();
        $cr->compare('u_id', $u_id);
        $cr->compare('contact_id', $contact_id);
        $model = $this->find($cr);
        if (empty($model)) {
            return FALSE;
        }
        $model->status = ConstStatus::NORMAL;
        return $model->update();
    }
    
}
