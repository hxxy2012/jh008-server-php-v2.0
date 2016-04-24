<?php

/**
 * This is the model class for table "up_admin_map".
 *
 * The followings are the available columns in table 'up_admin_map':
 * @property string $id
 * @property string $up_id
 * @property string $a_id
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property AdminInfo $a
 * @property UpInfo $up
 */
class UpAdminMap extends AdminModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'up_admin_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('up_id, a_id', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('up_id, a_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, up_id, a_id, status', 'safe', 'on'=>'search'),
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
			//'a' => array(self::BELONGS_TO, 'AdminInfo', 'a_id'),
			//'up' => array(self::BELONGS_TO, 'UpInfo', 'up_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '管理员上传关联id',
			'up_id' => '文件id',
			'a_id' => '管理员id',
			'status' => '状态',
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
		$criteria->compare('up_id',$this->up_id,true);
		$criteria->compare('a_id',$this->a_id,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UpAdminMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 插入一条文件上传者关联
     * @param type $imgId
     * @param type $aid
     */
    public function ins($upId, $aid) 
    {
        $this->up_id = $upId;
        $this->a_id = $aid;
        $this->status = ConstStatus::NORMAL;
        return $this->save();
    }
    
}
