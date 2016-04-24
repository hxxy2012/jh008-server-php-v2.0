<?php

/**
 * This is the model class for table "msg_info".
 *
 * The followings are the available columns in table 'msg_info':
 * @property string $id
 * @property string $type_id
 * @property string $content
 * @property string $filter
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property MsgType $type
 * @property MsgRevUserMap[] $msgRevUserMaps
 */
class MsgInfo extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'msg_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type_id, create_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('type_id', 'length', 'max'=>10),
			array('content', 'length', 'max'=>240),
			array('filter', 'length', 'max'=>120),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, type_id, content, filter, status, create_time', 'safe', 'on'=>'search'),
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
			//'type' => array(self::BELONGS_TO, 'MsgType', 'type_id'),
			//'msgRevUserMaps' => array(self::HAS_MANY, 'MsgRevUserMap', 'msg_id'),
            
            'fkType' => array(self::BELONGS_TO, 'MsgType', 'type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '消息id',
			'type_id' => '类型id',
			'content' => '内容',
			'filter' => '跳转',
			'status' => '状态:-1删除，0正常',
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
		$criteria->compare('type_id',$this->type_id,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('filter',$this->filter,true);
		$criteria->compare('status',$this->ststus);
		$criteria->compare('create_time',$this->creat_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MsgInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加消息
     * @param type $isPublishNow
     */
    public function add($isPublishNow)
    {
        $this->status = ConstStatus::NORMAL;
        $this->create_time = date('Y-m-d H:i:s', time());
        if ($isPublishNow) {
            $this->publish_time = date('Y-m-d H:i:s', time());
        }elseif (empty ($this->publish_time)) {
            return FALSE;
        }
        $r = $this->save();
        if (!$r) {
            return $r;
        }
        
        $msgType = MsgType::model()->findByPk($this->type_id);
        if (1 == $msgType->is_broadcast) {
            //用户与消息关联插入
            //$r =  MsgRevUserMap::model()->add($this->id, TRUE);
            TimeTask::model()->addTimeTask(ConstTimeTaskType::MSG_TO_ALL_USERS, date('Y-m-d H:i:s'));
            if ($r) {
                $this->addToPushAll($isPublishNow);
            }
        }
        
        return $r;
    }

    
    /**
     * 将群发消息添加至push
     */
    public function addToPushAll($isPublishNow)
    {
        $model = new PushMsgInfo();
        $model->send_type = ConstPushMsgSendType::TO_All;
        //1系统消息，2活动推荐
        //1活动，2url，3纯文本，4版本更新，5系统消息
        switch ($this->type_id) {
            case 1:
                $model->type_id = 5;
                break;
            case 2:
                $model->type_id = 1;
                break;
            default:
                $model->type_id = 3;
                break;
        }
        $msgType = MsgType::model()->getType($this->type_id);
        $model->title = $msgType['name'];
        $model->text = $this->content;
        $model->filter = $this->filter;
        $model->publish_time = $this->publish_time;
        $model->add($isPublishNow ? 1 : 0);
    }


    /**
     * 修改消息信息
     */
    public function updateMsg()
    {
        return $this->update();
    }



    /**
     * 删除消息
     * @return type
     */
    public function del()
    {
        $this->status = ConstStatus::DELETE;
        return $this->update();
    }
    
    
    /**
     * 获取消息信息
     * @param type $id
     * @return type
     */
    public function getMsg($id)
    {
        $model = $this->with('fkType')->findByPk($id);
        return array(
            'content' => $model->content,
            'filter' => $model->filter,
            'status' => $model->status,
            'create_time' => $model->create_time,
            'publish_time' => $model->publish_time,
            'type' => array(
                'id' => $model->fkType->id,
                'name' => $model->fkType->name,
            )
        );
    }


    /**
     * 搜索消息列表
     * @param type $page
     * @param type $size
     * @param type $isDel
     */
    public function searchMsgs($page, $size, $isDel = FALSE)
    {
        $cr = new CDbCriteria();
        $cr->with = 'fkType';
        if ($isDel) {
            $cr->compare('t.status', ConstStatus::DELETE);
        }  else {
            $cr->compare('t.status', '<>' . ConstStatus::DELETE);
        }
        $totalNum = $this->count($cr);
        
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $msgs = array();
        foreach ($rst as $v) {
            $msg = array();
            $msg['id'] = $v->id;
            $msg['content'] = $v->content;
            $msg['filter'] = $v->filter;
            $msg['status'] = $v->status;
            $msg['create_time'] = $v->create_time;
            $msg['publish_time'] = $v->publish_time;
            if (!empty($v->fkType)) {
                $type = array();
                $type['id'] = $v->fkType->id;
                $type['name'] = $v->fkType->name;
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
     * 刷新发送消息
     */
    public function refreshMsgToAllUsers()
    {
        $cr = new CDbCriteria();
        $cr->compare('status', ConstStatus::NORMAL);
        $cr->with = 'fkType';
        $cr->compare('fkType.status', '<>' . ConstStatus::DELETE);
        $cr->compare('fkType.is_broadcast', 1);
        $rst = $this->findAll($cr);
        
        foreach ($rst as $v) {
            MsgRevUserMap::model()->add($v->id, TRUE);
            $v->status = 1;
            $v->update();
        }
    }
    
}
