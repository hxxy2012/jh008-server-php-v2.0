<?php

/**
 * This is the model class for table "push_msg_info".
 *
 * The followings are the available columns in table 'push_msg_info':
 * @property string $id
 * @property string $text
 * @property string $url
 * @property string $filter
 * @property string $create_time
 * @property integer $status
 * @property integer $fail_num
 * @property string $last_fail_time
 *
 * The followings are the available model relations:
 * @property PushMsgType $type0
 */
class PushMsgInfo extends AdminModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'push_msg_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('create_time, status', 'required'),
			array('status, fail_num', 'numerical', 'integerOnly'=>true),
			array('text, filter', 'length', 'max'=>64),
			array('url', 'length', 'max'=>240),
			array('last_fail_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, text, url, filter, create_time, status, fail_num, last_fail_time', 'safe', 'on'=>'search'),
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
			//'type0' => array(self::BELONGS_TO, 'PushMsgType', 'type_id'),
            
            'fkType' => array(self::BELONGS_TO, 'PushMsgType', 'type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'push消息id',
			'text' => '文字',
			'url' => '链接',
			'filter' => '属性',
			'create_time' => '创建时间',
			'status' => '状态：-1删除，0未发送，1成功',
			'fail_num' => '失败次数',
			'last_fail_time' => '最后一次失败时间',
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
		$criteria->compare('text',$this->text,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('filter',$this->filter,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('fail_num',$this->fail_num);
		$criteria->compare('last_fail_time',$this->last_fail_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PushMsgInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加push消息
     */
    public function add($isSendNow = 0)
    {
        $this->create_time = date('Y-m-d H:i:s');
        $this->fail_num = 0;
        $this->status = ConstPushMsgStatus::NOT_SEND;
        //即时发送的情况
        if ($isSendNow) {
            $this->publish_time = date('Y-m-d H:i:s');
            //if ($this->send($this)) {
            //    $this->status = ConstPushMsgStatus::SEND_SUCCESS;
            //}  else {
            //    $this->fail_num = $this->fail_num + 1;
            //    $this->last_fail_time = date('Y-m-d H:i:s', time());
            //}
        }
        TimeTask::model()->addTimeTask(ConstTimeTaskType::PUSH_MSG, $this->publish_time);
        return $this->save();
    }
    
    
    /**
     * 发送push消息（即时发送）
     */
    public function send($model)
    {
        if (empty($model)) {
            return FALSE;
        }
        return Yii::app()->jPush->sendMsg(
                $model->send_type,
                $model->recv,
                $model->type_id,
                $model->title,
                $model->text,
                $model->url,
                $model->filter
                );
    }
    
    
    /**
     * 搜索push列表
     */
    public function searchPushs($page, $size, $isDel = FALSE) {
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
        
        $pushs = array();
        foreach ($rst as $v) {
            $push = array();
            $push['id'] = $v->id;
            $push['send_type'] = $v->send_type;
            $push['recv'] = $v->recv;
            $push['title'] = $v->title;
            $push['text'] = $v->text;
            $push['url'] = $v->url;
            $push['filter'] = $v->filter;
            $push['create_time'] = $v->create_time;
            $push['publish_time'] = $v->publish_time;
            $push['status'] = $v->status;
            $push['fail_num'] = $v->fail_num;
            $push['last_fail_time'] = $v->last_fail_time;
            if (!empty($v->fkType)) {
                $type = array();
                $type['id'] = $v->fkType->id;
                $type['name'] = $v->fkType->name;
                $push['type'] = $type;
            }
            array_push($pushs, $push);
        }
        return array(
            'total_num' => $totalNum,
            'pushs' => $pushs,
            );
    }
    
    
    /**
     * 刷新消息发送表
     */
    public function refreshPushs($isBackground = FALSE) {
        $criteria = new CDbCriteria();
        $criteria->compare('t.status', ConstPushMsgStatus::NOT_SEND);
        $criteria->compare('t.fail_num', '<' . ConstPushMsgStatus::MAX_FAIL_NUM);
        $criteria->order = 't.publish_time asc, t.id asc';
        $rst = $this->findAll($criteria);
        
        $transaction = Yii::app()->dbAdmin->beginTransaction();
        try {
            foreach ($rst as $k => $v) {
                //最后一次失败时间与现在时间间隔大于失败重发时间间隔
                if ((time() < strtotime($v->publish_time)) || (time() - strtotime($v->last_fail_time) < ConstPushMsgStatus::FAIL_RESEND_TIME_INTERVAL)) {
                    continue;
                }
                if ($this->send($v)) {
                    $v->status = ConstPushMsgStatus::SEND_SUCCESS;
                }  else {
                    $v->fail_num = $v->fail_num + 1;
                    $v->last_fail_time = date('Y-m-d H:i:s', time());
                    if ($isBackground) {
                        echo 'push id ' . $v->id . ' is pushed failed ' . $v->fail_num . ' time at ' . date('Y-m-d H:i:s');
                    }
                }
                $v->update();
            }
            $transaction->commit();
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            $transaction->rollBack();
        }
    }
    
}
