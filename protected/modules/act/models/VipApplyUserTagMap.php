<?php

/**
 * This is the model class for table "vip_apply_user_tag_map".
 *
 * The followings are the available columns in table 'vip_apply_user_tag_map':
 * @property string $id
 * @property string $apply_id
 * @property string $tag_id
 * @property integer $status
 * @property string $create_time
 */
class VipApplyUserTagMap extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'vip_apply_user_tag_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('apply_id, tag_id, status, create_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('apply_id, tag_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, apply_id, tag_id, status, create_time', 'safe', 'on'=>'search'),
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
			'id' => '达人申请与用户标签关联id',
			'apply_id' => '申请id',
			'tag_id' => '标签id',
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
		$criteria->compare('apply_id',$this->apply_id,true);
		$criteria->compare('tag_id',$this->tag_id,true);
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
	 * @return VipApplyUserTagMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加达人申请与用户标签关联
     * 
     * @param type $apply_id 申请id
     * @param type $tag_id 用户标签id
     * @return boolean
     */
    public function add($apply_id, $tag_id)
    {
        $cr = new CDbCriteria();
        $cr->compare('apply_id', $apply_id);
        $cr->compare('tag_id', $tag_id);
        $model = $this->find($cr);
        
        if (empty($model)) {
            $model = new VipApplyUserTagMap();
            $model->apply_id = $apply_id;
            $model->tag_id = $tag_id;
            $model->status = ConstStatus::NORMAL;
            $model->create_time = date('Y-m-d H:i:s');
            return $model->save();
        }
        
        if (ConstStatus::NORMAL == $model->status) {
            return FALSE;
        }
        
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        return $model->update();
    }
    
}
