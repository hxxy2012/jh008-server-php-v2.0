<?php

/**
 * This is the model class for table "msg_rev_user_map".
 *
 * The followings are the available columns in table 'msg_rev_user_map':
 * @property string $id
 * @property string $u_id
 * @property string $msg_id
 *
 * The followings are the available model relations:
 * @property UserInfo $u
 * @property MsgInfo $msg
 */
class MsgRevUserMap extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'msg_rev_user_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('u_id, msg_id', 'required'),
			array('u_id, msg_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, u_id, msg_id', 'safe', 'on'=>'search'),
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
			//'msg' => array(self::BELONGS_TO, 'MsgInfo', 'msg_id'),
            
            'fkMsg' => array(self::BELONGS_TO, 'MsgInfo', 'msg_id'),
            'fkUser' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '消息接收者关联id',
			'u_id' => '用户id',
			'msg_id' => '消息id',
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
		$criteria->compare('msg_id',$this->msg_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MsgRevUserMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 获取用户消息
     * @param type $uid
     */
    public function getUMsgs($uid, $page, $size)
    {
        $criteria = new CDbCriteria();
        $criteria->with = 'fkMsg.fkType';
        $criteria->compare('t.u_id', $uid);
        $criteria->compare('t.status', '<>' . ConstStatus::DELETE);
        $criteria->compare('publish_time', '<=' . date('Y-m-d H:i:s', time()));
        $totalNum = $this->count($criteria);
        
        $criteria->offset = ($page - 1) * $size;
        $criteria->limit = $size;
        $rst = $this->findAll($criteria);
        
        $msgs = array();
        foreach ($rst as $k => $v) {
            $msg = array();
            $msg['id'] = $v->fkMsg->id;
            $msg['content'] = $v->fkMsg->content;
            $msg['filter'] = $v->fkMsg->filter;
            $msg['status'] = $v->fkMsg->status;
            $msg['create_time'] = $v->fkMsg->create_time;
            $msg['publish_time'] = $v->fkMsg->publish_time;
            if (!empty($v->fkMsg->fkType)) {
                $type = array();
                $type['id'] = $v->fkMsg->fkType->id;
                $type['name'] = $v->fkMsg->fkType->name;
                $msg['type'] = $type;
            }
            array_push($msgs, $msg);
        }
        return array(
            'total_num' => $totalNum,
            'msgs' => $msgs,
            );
    }
    
    
    /**
     * 设置用户消息已读
     * @param type $msgId
     * @param type $uid
     */
    public function setRead($msgId, $uid)
    {
        return $this->updateAll(array('status' => 1), 'status=0 and msg_id=:msgId and u_id=:uid', array(':msgId' => $msgId, ':uid' => $uid));
    }
    
    
    /**
     * 删除用户消息
     * @param type $msgId
     * @param type $uid
     */
    public function del($msgId, $uid) 
    {
        return $this->updateAll(array('status' => -1), 'msg_id=:msgId and u_id=:uid', array(':msgId' => $msgId, ':uid' => $uid));
    }
    
    
    /**
     * 清空消息
     * @param type $uid
     */
    public function delAll($uid) 
    {
        return $this->updateAll(array('status' => -1), 'u_id=:uid', array(':uid' => $uid));
    }
    
    
    /**
     * 添加用户消息关联
     * @param type $msgId
     * @param type $isToAll
     * @param type $uids
     */
    public function add($msgId, $isToAll, $uids = array())
    {
        $transaction = Yii::app()->dbAct->beginTransaction();
        try {
            if ($isToAll) {
                $uids = UserInfoAdmin::model()->getAllIds();
            }
            foreach ($uids as $uid) {
                $model = new MsgRevUserMap();
                $model->u_id = $uid;
                $model->msg_id = $msgId;
                $model->status = ConstStatus::NORMAL;
                $model->save();
            }
            $transaction->commit();
            return TRUE;
        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
            $transaction->rollBack();
        }
        return FALSE;
    }
    
}
