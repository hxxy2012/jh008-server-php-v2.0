<?php

/**
 * This is the model class for table "system_msg_user_map".
 *
 * The followings are the available columns in table 'system_msg_user_map':
 * @property string $id
 * @property string $msg_id
 * @property string $u_id
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property SystemMsg $msg
 * @property UserInfo $u
 */
class SystemMsgUserMap extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'system_msg_user_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('msg_id, u_id, create_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('msg_id, u_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, msg_id, u_id, status, create_time', 'safe', 'on'=>'search'),
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
			//'msg' => array(self::BELONGS_TO, 'SystemMsg', 'msg_id'),
			//'u' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
            
            'fkMsg' => array(self::BELONGS_TO, 'SystemMsg', 'msg_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '系统消息与用户关联id',
			'msg_id' => '系统消息id',
			'u_id' => '用户id',
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
		$criteria->compare('msg_id',$this->msg_id,true);
		$criteria->compare('u_id',$this->u_id,true);
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
	 * @return SystemMsgUserMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 用户系统消息
     * 
     * @param type $uid 用户id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function userSystemMsgs($uid, $page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.status', '<>' . ConstStatus::DELETE);
        $cr->compare('u_id', $uid);
        
        $cr->with = 'fkMsg';
        $cr->compare('fkMsg.status', ConstStatus::NORMAL);
        
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $systemMsgs = array();
        foreach ($rst as $v) {
            $systemMsg = SystemMsg::model()->profile(NULL, $v->msg_id);
            if (empty($systemMsg)) {
                continue;
            }
            $systemMsg['status'] = $v->status;
            array_push($systemMsgs, $systemMsg);
        }
        
        $this->setRead($uid);
        
        return array(
            'total_num' => $count,
            'system_msgs' => $systemMsgs,
        );
    }
    
    
    /**
     * 设置已读
     * 
     * @param type $uid 用户id
     */
    public function setRead($uid)
    {
        return $this->updateAll(
                array(
                    'status' => 1,
                    ), 
                'u_id=:uid and status=:status', 
                array(
                    ':uid' => $uid,
                    ':status' => ConstStatus::NORMAL,
                )
                );
    }
    
    
    /**
     * 添加系统消息与用户关联
     * 
     * @param type $msgId 系统消息id
     * @param type $uid 用户id
     */
    public function add($msgId, $uid)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.msg_id', $msgId);
        $cr->compare('t.u_id', $uid);
        $model = $this->find($cr);
        
        if (!empty($model)) {
            return FALSE;
        }
        
        $model = new SystemMsgUserMap();
        $model->msg_id = $msgId;
        $model->u_id = $uid;
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
}
