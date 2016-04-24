<?php

/**
 * This is the model class for table "img_up_admin_map".
 *
 * The followings are the available columns in table 'img_up_admin_map':
 * @property string $id
 * @property string $img_id
 * @property string $a_id
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property AdminInfo $a
 * @property ImgInfo $img
 */
class ImgUpAdminMap extends AdminModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'img_up_admin_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('img_id, a_id', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('img_id, a_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, img_id, a_id, status', 'safe', 'on'=>'search'),
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
			//'img' => array(self::BELONGS_TO, 'ImgInfo', 'img_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '图像上传管理员关联id',
			'img_id' => '图像id',
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
		$criteria->compare('img_id',$this->img_id,true);
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
	 * @return ImgUpAdminMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 插入一条图像上传者关联
     * @param type $imgId
     * @param type $aid
     */
    public function ins($imgId, $aid) 
    {
        $this->img_id = $imgId;
        $this->a_id = $aid;
        $this->status = ConstStatus::NORMAL;
        return $this->save();
    }
    
}
