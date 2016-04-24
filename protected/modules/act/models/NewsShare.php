<?php

/**
 * This is the model class for table "news_share".
 *
 * The followings are the available columns in table 'news_share':
 * @property string $id
 * @property string $news_id
 * @property string $u_id
 * @property string $share_type
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property UserInfo $u
 * @property NewsInfo $news
 */
class NewsShare extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'news_share';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('news_id, u_id, create_time', 'required'),
			array('news_id, u_id, share_type', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, news_id, u_id, share_type, create_time', 'safe', 'on'=>'search'),
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
			//'u' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
			//'news' => array(self::BELONGS_TO, 'NewsInfo', 'news_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '资讯分享关联id',
			'news_id' => '资讯id',
			'u_id' => '分享者用户id',
			'share_type' => '分享类型：1微信，2朋友圈，3新浪微博，4qq',
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
		$criteria->compare('news_id',$this->news_id,true);
		$criteria->compare('u_id',$this->u_id,true);
		$criteria->compare('share_type',$this->share_type,true);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return NewsShare the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 获取资讯被分享次数
     * 
     * @param type $newsId 资讯id
     */
    public function sharedNum($newsId) 
    {
        return $this->count('news_id=:newsId', array(':newsId' => $newsId));
    }
    
    
    /**
     * 添加分享
     * @param type $newsId 资讯id
     * @param type $uid 用户id
     * @param type $shareType 分享类型
     */
    public function addShare($newsId, $uid = NULL, $shareType = NULL)
    {
        $model = new NewsShare();
        $model->news_id = $newsId;
        $model->u_id = $uid;
        $model->share_type = $shareType;
        $model->create_time = date("Y-m-d H:i:s", time());
        return $model->save();
    }
    
}
