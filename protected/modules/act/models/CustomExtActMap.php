<?php

/**
 * This is the model class for table "custom_ext_act_map".
 *
 * The followings are the available columns in table 'custom_ext_act_map':
 * @property string $id
 * @property string $c_id
 * @property string $act_id
 * @property integer $status
 * @property string $create_time
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property UserCustomExtend $c
 * @property ActInfo $act
 */
class CustomExtActMap extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'custom_ext_act_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('c_id, act_id, create_time, modify_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('c_id, act_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, c_id, act_id, status, create_time, modify_time', 'safe', 'on'=>'search'),
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
			//'c' => array(self::BELONGS_TO, 'UserCustomExtend', 'c_id'),
			//'act' => array(self::BELONGS_TO, 'ActInfo', 'act_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '自定义字段与活动关联id',
			'c_id' => '自定义字段id',
			'act_id' => '活动id',
			'status' => '状态',
			'create_time' => '创建时间',
			'modify_time' => '修改时间',
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
		$criteria->compare('c_id',$this->c_id,true);
		$criteria->compare('act_id',$this->act_id,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('modify_time',$this->modify_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CustomExtActMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 活动的自定义字段
     * 
     * @param type $actId 活动id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function customKeys($actId, $page, $size) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $count = $this->count($cr);
        $cr->order = 't.id asc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $customKeys = array();
        foreach ($rst as $v) {
            $customKey = UserCustomExtend::model()->profile($v->c_id);
            if (empty($customKey)) {
                continue;
            }
            array_push($customKeys, $customKey);
        }
        
        return array(
            'total_num' => $count,
            'custom_keys' => $customKeys,
        );
    }
    
    /**
     * 全部自定义字段
     * @param type $actId 活动id
     * @return array
     */
    public function allCustomKeys($actId) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $cr->order = 't.id asc';
        $rst = $this->findAll($cr);
        $customKeys = array();
        foreach ($rst as $v) {
            $customKey = UserCustomExtend::model()->profile($v->c_id);
            if (empty($customKey)) {
                continue;
            }
            array_push($customKeys, $customKey);
        }
        return $customKeys;
    }
    
    
}
