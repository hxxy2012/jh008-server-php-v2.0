<?php

/**
 * This is the model class for table "time_log".
 *
 * The followings are the available columns in table 'time_log':
 * @property string $id
 * @property string $name
 * @property string $create_time
 */
class TimeLog extends AdminModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'time_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, create_time', 'required'),
			array('name', 'length', 'max'=>120),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, create_time', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'create_time' => 'Create Time',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TestTime the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    public function getAll()
    {
        $rst = $this->findAll();
        $count = $this->count();
        $logs = array();
        foreach ($rst as $v) {
            $log = array();
            $log['id'] = $v->id;
            $log['name'] = $v->name;
            $log['create_time'] = $v->create_time;
            $log['update_time'] = $v->update_time;
            array_push($logs, $log);
        }
        return array(
            'total_num' => $count,
            'logs' => $logs,
        );
    }
    

    public function timeAll()
    {
        $name = 'time_task_all';
        $this->check($name);
    }
    
    
    public function timeActTimeStatus()
    {
        $name = 'time_task_act_time_status';
        $this->check($name);
    }
    
    
    public function timeTagCount()
    {
        $name = 'time_task_tag_count';
        $this->check($name);
    }
    
    
    public function timeIndexPageActList()
    {
        $name = 'time_task_index_page_act_list';
        $this->check($name);
    }
    
    
    public function timeUserRegistCount()
    {
        $name = 'time_task_user_regist_count';
        $this->check($name);
    }
    
    
    public function timePush()
    {
        $name = 'time_task_push';
        $this->check($name);
    }
    
    
    public function timePushMsgTask()
    {
        $name = 'time_push_msg_task';
        $this->check($name);
    }
    
    
    public function timeMsg()
    {
        $name = 'time_task_msg';
        $this->check($name);
    }
    
    
    public function timeTask() 
    {
        $name = 'time_task_exist';
        $this->check($name);
    }
    
    
    public function timeActBaseGrowNums() 
    {
        $name = 'time_act_base_grow_nums';
        $this->check($name);
    }
    
    
    public function timeFriendDynamicTask() 
    {
        $name = 'time_friend_dynamic_task';
        $this->check($name);
    }
    
    
    public function timeVipSearchTask() 
    {
        $name = 'time_vip_search_task';
        $this->check($name);
    }
    
    
    public function timeSystemMsgUserTask() 
    {
        $name = 'time_system_msg_user_task';
        $this->check($name);
    }
    
    
    public function timeCityVipRandomDynamicsTask() 
    {
        $name = 'time_city_vip_random_dynamics_task';
        $this->check($name);
    }
    
    
    public function timeActInfoBaiduSynchroTask() 
    {
        $name = 'time_act_info_baidu_synchro_task';
        $this->check($name);
    }
    
    
    public function check($name)
    {
        $model = $this->find('name=:name', array(':name' => $name));
        if (empty($model)) {
            $model = new TimeLog();
            $model->name = $name;
            $model->create_time = date("Y-m-d H:i:s", time());
            $model->update_time = date("Y-m-d H:i:s", time());
            $model->save();
        }  else {
            $model->update_time = date("Y-m-d H:i:s", time());
            $model->update();
        }
    }
    
}
