<?php

/**
 * This is the model class for table "video_up_city_manager_map".
 *
 * The followings are the available columns in table 'video_up_city_manager_map':
 * @property string $id
 * @property string $video_id
 * @property string $m_id
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property VideoInfo $video
 * @property CityManager $m
 */
class VideoUpCityManagerMap extends ManagerModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'video_up_city_manager_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('video_id, m_id', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('video_id, m_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, video_id, m_id, status', 'safe', 'on'=>'search'),
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
			'video' => array(self::BELONGS_TO, 'VideoInfo', 'video_id'),
			'm' => array(self::BELONGS_TO, 'CityManager', 'm_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '视频上传管理员关联id',
			'video_id' => '视频id',
			'm_id' => '管理员id',
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
		$criteria->compare('video_id',$this->video_id,true);
		$criteria->compare('m_id',$this->m_id,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return VideoUpCityManagerMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 插入一条视频上传者关联
     * @param type $videoId 视频id
     * @param type $mid 管理员
     */
    public function ins($videoId, $mid) 
    {
        $this->video_id = $videoId;
        $this->m_id = $mid;
        $this->status = ConstStatus::NORMAL;
        return $this->save();
    }
    
}
