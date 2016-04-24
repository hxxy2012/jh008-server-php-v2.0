<?php

/**
 * This is the model class for table "city_vip_random_dynamics_task".
 *
 * The followings are the available columns in table 'city_vip_random_dynamics_task':
 * @property string $id
 * @property string $last_max_city_id
 * @property integer $status
 * @property string $begin_time
 * @property string $end_time
 * @property integer $sum
 *
 * The followings are the available model relations:
 * @property CityInfo $lastMaxCity
 */
class CityVipRandomDynamicsTask extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'city_vip_random_dynamics_task';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status, sum', 'numerical', 'integerOnly'=>true),
			array('last_max_city_id', 'length', 'max'=>10),
			array('begin_time, end_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, last_max_city_id, status, begin_time, end_time, sum', 'safe', 'on'=>'search'),
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
			'lastMaxCity' => array(self::BELONGS_TO, 'CityInfo', 'last_max_city_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '好友动态任务关联id',
			'last_max_city_id' => '最后处理的最大的城市id',
			'status' => '状态',
			'begin_time' => '开始时间',
			'end_time' => '结束时间',
			'sum' => '总数',
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
		$criteria->compare('last_max_city_id',$this->last_max_city_id,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('begin_time',$this->begin_time,true);
		$criteria->compare('end_time',$this->end_time,true);
		$criteria->compare('sum',$this->sum);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CityVipRandomDynamicsTask the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加任务
     */
    public function add()
    {
        //如果还存在未执行完的任务则不添加
        if ($this->isExistTask()) {
            return FALSE;
        }
        
        $model = new CityVipRandomDynamicsTask();
        $model->last_max_city_id = NULL;
        $model->status = ConstStatus::NORMAL;
        return $model->save();
    }
    
    
    /**
     * 刷新所有任务
     */
    public function refreshCityVipRandomDynamicsTask()
    {
        $this->add();
        while ($task = $this->isExistTask()) {
            if (empty($task->last_max_city_id) && empty($task->begin_time) && ConstStatus::NORMAL == $task->status) {
                $task->begin_time = date('Y-m-d H:i:s');
            }
            $this->runTask($task);
        }
    }

    
    /**
     * 是否存在未执行的任务
     */
    private function isExistTask()
    {
        $cr = new CDbCriteria();
        $cr->compare('t.status', ConstStatus::NORMAL);
        $cr->order = 't.id asc';
        
        $task = $this->find($cr);
        return empty($task) ? FALSE : $task;
    }
    
    
    /**
     * 执行任务
     * 
     * @param type $task 任务
     */
    private function runTask($task)
    {
        while ($nextCity = $this->isExistCity($task)) {
            $this->updateCityVipRandomDynamics($task, $nextCity['id']);
        }
        $task->status = 1;
        $task->end_time = date('Y-m-d H:i:s');
        $task->update();
    }
    
    
    /**
     * 是否存在未更新的城市
     * 
     * @param type $task 任务
     */
    private function isExistCity($task)
    {
        $nextCity = CityInfo::model()->find('t.status=1 and t.id>:lastMaxCityId', array(
            ':lastMaxCityId' => empty($task->last_max_city_id) ? 0 : $task->last_max_city_id,
        ));
        return empty($nextCity) ? FALSE : $nextCity;
    }
    
    
    /**
     * 给对应城市更新达人随机动态
     * 
     * @param type $task 任务
     * @param type $cityId 城市id
     */
    public function updateCityVipRandomDynamics($task, $cityId)
    {
        $cr = new CDbCriteria();
        $cr->alias = 'Dynamic';
        $cr->compare('Dynamic.status', ConstStatus::NORMAL);
        $cr->join = 'LEFT JOIN user_city_map AS UserCityMap ON Dynamic.author_id=UserCityMap.u_id';
        $cr->compare('UserCityMap.city_id', $cityId);
        $cr->compare('UserCityMap.status', ConstStatus::NORMAL);

        $count = UserDynamic::model()->count($cr);
        $cr->order = 'Dynamic.id desc';
        $max = ConstKeyVal::CITY_VIP_RANDOM_DYNAMIC_MAX * 100;
        if ($count < $max) {
            $max = $count;
        }
        $cr->limit = 1;
        
        //取max组数据
        for ($i = 0; $i < ConstKeyVal::CITY_VIP_RANDOM_DYNAMIC_MAX; $i++) {
            //每组数据取20条
            $dynamicIds = array();
            for ($j = 0; $j < 20; $j++) {
                $cr->offset = rand(1, $max);
                $model = UserDynamic::model()->find($cr);
                if (!empty($model)) {
                    array_push($dynamicIds, $model->id);
                }
            }
            //插入到kv
            KeyValInfo::model()->upCityVipRandomDynamics($cityId, $i, $dynamicIds);
        }
        //标注现在已经执行的进度
        $task->last_max_city_id = $cityId;
        $task->sum = $task->sum + 1;
        $task->update();
    }
    
}
