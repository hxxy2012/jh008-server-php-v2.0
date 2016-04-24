<?php

/**
 * This is the model class for table "award_info".
 *
 * The followings are the available columns in table 'award_info':
 * @property string $id
 * @property string $name
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property AwardUserMap[] $awardUserMaps
 * @property PrizeAwardMap[] $prizeAwardMaps
 */
class AwardInfo extends AdminModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'award_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('create_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, status, create_time', 'safe', 'on'=>'search'),
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
			//'awardUserMaps' => array(self::HAS_MANY, 'AwardUserMap', 'award_id'),
			//'prizeAwardMaps' => array(self::HAS_MANY, 'PrizeAwardMap', 'award_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '奖项id',
			'name' => '名称',
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
		$criteria->compare('name',$this->name,true);
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
	 * @return AwardInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加活动抽奖方案的奖项
     * @param type $prizeId
     * @param type $model
     */
    public function addActAward($prizeId)
    {
        $this->status = ConstStatus::NORMAL;
        $this->create_time = date('Y-m-d H:i:s');
        $r = $this->save();
        if (!$r) {
            return FALSE;
        }
        return PrizeAwardMap::model()->add($prizeId, $this->id);
    }
    
}
