<?php

/**
 * This is the model class for table "user_feedback_img_map".
 *
 * The followings are the available columns in table 'user_feedback_img_map':
 * @property string $id
 * @property string $f_id
 * @property string $img_id
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property UserFeedback $f
 * @property ImgInfo $img
 */
class UserFeedbackImgMap extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_feedback_img_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('f_id, img_id, status, create_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('f_id, img_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, f_id, img_id, status, create_time', 'safe', 'on'=>'search'),
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
			'f' => array(self::BELONGS_TO, 'UserFeedback', 'f_id'),
			'img' => array(self::BELONGS_TO, 'ImgInfo', 'img_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '用户意见反馈图片关联id',
			'f_id' => '意见反馈id',
			'img_id' => '图片id',
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
		$criteria->compare('f_id',$this->f_id,true);
		$criteria->compare('img_id',$this->img_id,true);
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
	 * @return UserFeedbackImgMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加意见反馈与图片关联
     * 
     * @param UserFeedbackImgMap $model 意见反馈数据
     * @param type $fid 意见反馈id
     * @param type $imgId 图片id
     */
    public function add($model = NULL, $fid = NULL, $imgId = NULL)
    {
        if (empty($model)) {
            $model = new UserFeedbackImgMap();
        }
        if (!empty($fid)) {
            $model->f_id = $fid;
        }
        if (!empty($imgId)) {
            $model->img_id = $imgId;
        }
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
}
