<?php

/**
 * This is the model class for table "user_message_map".
 *
 * The followings are the available columns in table 'user_message_map':
 * @property string $id
 * @property string $u_id
 * @property string $contact_id
 * @property integer $role
 * @property string $msg_id
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property UserInfo $u
 * @property UserInfo $contact
 * @property UserMessage $msg
 */
class UserMessageMap extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_message_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('u_id, contact_id, role, msg_id', 'required'),
			array('role, status', 'numerical', 'integerOnly'=>true),
			array('u_id, contact_id, msg_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, u_id, contact_id, role, msg_id, status', 'safe', 'on'=>'search'),
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
			'u' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
			'contact' => array(self::BELONGS_TO, 'UserInfo', 'contact_id'),
			'msg' => array(self::BELONGS_TO, 'UserMessage', 'msg_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '用户与私信关联id',
			'u_id' => '用户id',
			'contact_id' => '联系人id',
			'role' => '角色：1发送者，2接收者',
			'msg_id' => '私信id',
			'status' => '状态:-1已删除，0未读，1已读',
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
		$criteria->compare('role',$this->role);
		$criteria->compare('msg_id',$this->msg_id,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserMessageMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 用户发送私信
     * 
     * @param type $author_id 发送者用户id
     * @param type $rev_id 接收者用户id
     * @param type $content 内容
     */
    public function addUserMsg($author_id, $rev_id, $content)
    {
        $modelMsg = new UserMessage();
        if (!$modelMsg->add($content, $modelMsg)) {
            return FALSE;
        }
        
        $r = $this->add($author_id, $rev_id, 1, $modelMsg->id);
        $this->add($rev_id, $author_id, 2, $modelMsg->id);
        
        if ($r) {
            UserContact::model()->up($author_id, $rev_id, $modelMsg->id);
            UserContact::model()->up($rev_id, $author_id, $modelMsg->id);
        }
        return $r;
    }
    
    
    /**
     * 添加用户与私信关联
     * 
     * @param type $u_id
     * @param type $contact_id
     * @param type $role
     * @param type $msg_id
     */
    public function add($u_id, $contact_id, $role, $msg_id) 
    {
        $model = new UserMessageMap();
        $model->u_id = $u_id;
        $model->contact_id = $contact_id;
        $model->role = $role;
        $model->msg_id = $msg_id;
        if (1 == $role) {
            $model->status = 1;
        }  else {
            $model->status = ConstStatus::NORMAL;
        }
        return $model->save();
    }

    
    /**
     * 删除用户发的私信
     * 
     * @param type $id 用户发的消息id
     * @param type $u_id 用户id
     */
    public function del($id, $u_id = NULL)
    {
        $model = $this->findByPk($id);
        
        if (empty($model)) {
            return FALSE;
        }
        
        //验证是否本人删除属于自己聊天记录里的消息
        if (!empty($u_id) && $u_id != $model->u_id) {
            return FALSE;
        }
        
        if (ConstStatus::DELETE == $model->status) {
            return FALSE;
        }
        
        $model->status = ConstStatus::DELETE;
        return $model->update();
    }
    
    
    /**
     * 设置用户消息为已读
     * 
     * @param type $u_id 用户id
     * @param type $contact_id 联系人id
     */
    public function read($u_id, $contact_id)
    {
        return $this->updateAll(
                array(
                    'status' => 1
                    ), 
                'u_id=:u_id and contact_id=:contact_id and status=0', 
                array(
                    ':u_id' => $u_id, 
                    ':contact_id' => $contact_id
                )
                );
    }


    /**
     * 双方私信消息
     * 
     * @param type $u_id 当前用户id
     * @param type $contact_id 用户id
     * @param type $page 页面
     * @param type $size 每天记录数
     */
    public function messages($u_id, $contact_id, $page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('u_id', $u_id);
        $cr->compare('contact_id', $contact_id);
        $cr->compare('status', '<>' . ConstStatus::DELETE);
        
        $count = $this->count($cr);
        $cr->order = 'id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $messages = array();
        foreach ($rst as $v) {
            $msg = UserMessage::model()->msg($v->msg_id);
            if (empty($msg)) {
                continue;
            }
            $message = array();
            $message['id'] = $v->id;
            $message['u_id'] = $v->u_id;
            $message['contact_id'] = $v->contact_id;
            $message['role'] = $v->role;
            $message['content'] = $msg['content'];
            $message['create_time'] = $msg['create_time'];
            array_push($messages, $message);
        }
        
        $this->read($u_id, $contact_id);
        
        return array(
            'total_num' => $count,
            'messages' => $messages
        );
    }
    
    
    /**
     * 双方私信新消息条数
     * 
     * @param type $u_id 当前用户id
     * @param type $contact_id 用户id
     */
    public function countMessages($u_id, $contact_id)
    {
        $cr = new CDbCriteria();
        $cr->compare('u_id', $u_id);
        $cr->compare('contact_id', $contact_id);
        $cr->compare('status', ConstStatus::NORMAL);
        
        return $this->count($cr);
    }
    
}
