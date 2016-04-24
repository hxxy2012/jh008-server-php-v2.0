<?php

/**
 * This is the model class for table "video_info".
 *
 * The followings are the available columns in table 'video_info':
 * @property string $id
 * @property string $ori_name
 * @property string $video_url
 * @property integer $status
 * @property string $create_time
 * @property string $descri
 *
 * The followings are the available model relations:
 * @property ActAlbumVideoMap[] $actAlbumVideoMaps
 * @property VideoUpCityManagerMap[] $videoUpCityManagerMaps
 * @property VideoUpManagerMap[] $videoUpManagerMaps
 */
class VideoInfo extends UserModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'video_info';
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
			array('ori_name, video_url', 'length', 'max'=>32),
			array('descri', 'length', 'max'=>120),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, ori_name, video_url, status, create_time, descri', 'safe', 'on'=>'search'),
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
			//'actAlbumVideoMaps' => array(self::HAS_MANY, 'ActAlbumVideoMap', 'video_id'),
			//'videoUpCityManagerMaps' => array(self::HAS_MANY, 'VideoUpCityManagerMap', 'video_id'),
			//'videoUpManagerMaps' => array(self::HAS_MANY, 'VideoUpManagerMap', 'video_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '视频id',
			'ori_name' => '原始名称',
			'video_url' => '视频url',
			'status' => '状态：-1删除，0正常',
			'create_time' => '创建时间',
			'descri' => '描述',
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
		$criteria->compare('ori_name',$this->ori_name,true);
		$criteria->compare('video_url',$this->video_url,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('descri',$this->descri,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return VideoInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 插入一条视频信息记录
     * @param type $oriName
     * @param type $imgUrl
     * @return type
     */
    public function ins($oriName, $videoUrl) 
    {
        $this->ori_name = $oriName;
        $this->video_url = $videoUrl;
        $this->status = ConstStatus::NORMAL;
        $this->create_time = date("Y-m-d H:i:s", time());
        return $this->save();
    }
    
    
    /**
     * 基本信息
     * 
     * @param type $id 视频id
     */
    public function profile($id) 
    {
        $model = $this->findByPk($id);
        if (empty($model) || ConstStatus::DELETE == $model->status) {
            return NULL;
        }
        return array(
            'id' => $model->id,
            //'video_url' => $model->video_url
            'video_url' => Yii::app()->imgUpload->getDownUrl($model->video_url)
        );
    }
    
}
