<?php

/**
 * This is the model class for table "custom_ext_act_user_val".
 *
 * The followings are the available columns in table 'custom_ext_act_user_val':
 * @property string $id
 * @property string $c_id
 * @property string $act_id
 * @property string $u_id
 * @property string $value
 * @property integer $status
 * @property string $create_time
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property UserCustomExtend $c
 * @property ActInfo $act
 * @property UserInfo $u
 */
class CustomExtActUserVal extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'custom_ext_act_user_val';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('c_id, act_id, u_id, create_time, modify_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('c_id, act_id', 'length', 'max'=>10),
			array('value', 'length', 'max'=>512),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, c_id, act_id, u_id, value, status, create_time, modify_time', 'safe', 'on'=>'search'),
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
			//'u' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '自定义字段与用户关联id',
			'c_id' => '自定义字段id',
			'act_id' => '活动id',
			'u_id' => '用户id',
			'value' => '值',
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
		$criteria->compare('u_id',$this->u_id,true);
		$criteria->compare('value',$this->value,true);
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
	 * @return CustomExtActUserVal the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 自定义字段活动对应用户的值
     * 
     * @param type $cid 自定义字段id
     * @param type $actId 活动id
     * @param type $uid 用户id
     * @param type $value 值
     */
    public function up($cid, $actId, $uid, $value)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.c_id', $cid);
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.u_id', $uid);
        $model = $this->find($cr);
        
        $isNew = FALSE;
        if (empty($model)) {
            $model = new CustomExtActUserVal();
            $model->c_id = $cid;
            $model->act_id = $actId;
            $model->u_id = $uid;
            $isNew = TRUE;
        }
        $model->value = $value;
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        $model->modify_time = date('Y-m-d H:i:s');
        if ($isNew) {
            return $model->save();
        }
        return $model->update();
    }
    
    /**
     * 获取用户自定义字段信息
     * @param type $cid 自定义字段id
     * @param type $actId 活动id
     * @param type $uid 用户id
     */
    public function get($cid, $actId, $uid)
    {
      return  $this->find("c_id = :c_id AND act_id = :act_id AND u_id = :u_id", array("c_id" => $cid,"act_id" => $actId,"u_id" => $uid));
    }
    
    
}
