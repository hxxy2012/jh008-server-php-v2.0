<?php

/**
 * This is the model class for table "user_regist_count".
 *
 * The followings are the available columns in table 'user_regist_count':
 * @property string $id
 * @property string $date
 * @property string $count
 * @property string $create_time
 */
class UserRegistCount extends AdminModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_regist_count';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date, create_time', 'required'),
			array('count', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, date, count, create_time', 'safe', 'on'=>'search'),
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
			'id' => '用户注册数统计id',
			'date' => '统计日期',
			'count' => '注册人数',
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
		$criteria->compare('date',$this->date,true);
		$criteria->compare('count',$this->count,true);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserRegistNum the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    
    /**
     * 获取日期内每天注册用户数
     * @param type $startDate
     * @param type $endDate
     */
    public function getRegistCount($startDate, $endDate)
    {
        if (empty($startDate)) {
            $startDate = date('Y-m-d', strtotime('2014-10-31'));
        }
        if (empty($endDate)) {
            $endDate = date('Y-m-d', time());
        }
        
        $cr = new CDbCriteria();
        $cr->addBetweenCondition('t.date', $startDate, $endDate);
        $countNum = $this->count($cr);
        $cr->order = 't.date asc';
        $rst = $this->findAll($cr);
        
        $counts = array();
        foreach ($rst as $v) {
            $count = array();
            $count['id'] = $v->id;
            $count['date'] = $v->date;
            $count['count'] = $v->count;
            $count['create_time'] = $v->create_time;
            array_push($counts, $count);
        }
        
        return array(
            'total_num' => $countNum,
            'counts' => $counts
        );
    }


    /**
     * 刷新每天注册用户统计
     */
    public function refreshRegistCount()
    {
        $model = UserInfo::model()->findByPk(1);
        $startDate = date('Y-m-d', strtotime('2014-10-31'));
        if (!empty($model) && !empty($model->create_time)) {
            $startDate = date('Y-m-d', strtotime($model->create_time));
        }
        
        $today = date('Y-m-d', time());
        
        $intervalDay = (strtotime($today) - strtotime($startDate)) / (3600 * 24);
        
        $transaction = Yii::app()->dbAdmin->beginTransaction();
        try {
            for ( $i = 0, $t = $startDate; $i < $intervalDay; $i++, $t = date('Y-m-d', strtotime($t . '+1 day'))) {
                $model = $this->find('date=:date', array(':date' => $t));
                if (empty($model)) {
                    $count = UserInfoAdmin::model()->countUsers(
                            date('Y-m-d 00:00:00', strtotime($t)), 
                            date('Y-m-d 23:59:59', strtotime($t))
                            );
                    $model = new UserRegistCount();
                    $model->date = $t;
                    $model->count = $count;
                    $model->create_time = date('Y-m-d H:i:s', time());
                    $model->save();
                }  else {
                    $count = UserInfoAdmin::model()->countUsers(
                            date('Y-m-d 00:00:00', strtotime($t)), 
                            date('Y-m-d 23:59:59', strtotime($t))
                            );
                    if ($model->count != $count) {
                        $model->count = $count;
                        $model->update();
                    }
                }
            }
            $transaction->commit();
        } catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            $transaction->rollBack();
        }
    }
    
}
