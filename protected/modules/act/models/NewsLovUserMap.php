<?php

/**
 * This is the model class for table "news_lov_user_map".
 *
 * The followings are the available columns in table 'news_lov_user_map':
 * @property string $id
 * @property string $news_id
 * @property string $u_id
 * @property integer $status
 * @property string $lov_time
 *
 * The followings are the available model relations:
 * @property NewsInfo $news
 * @property UserInfo $u
 */
class NewsLovUserMap extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'news_lov_user_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('news_id, u_id, lov_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('news_id, u_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, news_id, u_id, status, lov_time', 'safe', 'on'=>'search'),
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
			'news' => array(self::BELONGS_TO, 'NewsInfo', 'news_id'),
			'u' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
            
            'fkNews' => array(self::BELONGS_TO, 'NewsInfo', 'news_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '用户收藏关联id',
			'news_id' => '资讯id',
			'u_id' => '用户id',
			'status' => '状态',
			'lov_time' => '收藏时间',
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
		$criteria->compare('status',$this->status);
		$criteria->compare('lov_time',$this->lov_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return NewsLovUserMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 用户是否收藏此资讯
     * 
     * @param type $newsId 资讯id
     * @param type $uid 用户id
     */
    public function isLoved($newsId, $uid) 
    {
        $criteria = new CDbCriteria();
        $criteria->compare('news_id', $newsId);
        $criteria->compare('u_id', $uid);
        $r = $this->find($criteria);
        
        if (empty($r)) {
            return 0;
        }
        if (-1 == $r->status) {
            return -1;
        }
        return 1;
    }
    
    
    /**
     * 资讯的收藏数
     * 
     * @param type $newsId 资讯id
     */
    public function lovedNum($newsId) 
    {
        $criteria = new CDbCriteria();
        $criteria->compare('news_id', $newsId);
        return $this->count($criteria);
    }
    
    
    /**
     * 添加收藏资讯
     * 
     * @param type $newsId 资讯id
     * @param type $uid 用户id
     */
    public function addLove($newsId, $uid) 
    {
        $criteria = new CDbCriteria();
        $criteria->compare('t.news_id', $newsId);
        $criteria->compare('t.u_id', $uid);
        $model = $this->find($criteria);
        
        if (empty($model)) {
            $model =  new NewsLovUserMap();
            $model->news_id = $newsId;
            $model->u_id = $uid;
            $model->status = ConstStatus::NORMAL;
            $model->lov_time = date('Y-m-d H:i:s');
            return $model->save();
        }
        $model->status = ConstStatus::NORMAL;
        return $model->update();
    }
    
    
    /**
     * 取消收藏资讯
     * 
     * @param type $newsId 资讯id
     * @param type $uid 用户id
     */
    public function delLove($newsId, $uid)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('news_id', $newsId);
        $criteria->compare('u_id', $uid);
        $model = $this->find($criteria);
        
        if (empty($model)) {
            return FALSE;
        }
        $model->status = -1;
        return $model->update();
    }
    
    
    /**
     * 收藏的资讯
     * 
     * @param type $uid 用户id
     * @param type $typeId 类型id
     */
    public function news($uid, $typeId, $page, $size) 
    {
        $criteria = new CDbCriteria();
        $criteria->compare('t.u_id', $uid);
        $criteria->compare('t.status', ConstStatus::NORMAL);
        
        $criteria->with = 'fkNews';
        if (!empty($typeId)) {
            $criteria->compare('fkNews.type_id', $typeId);
        }
        $criteria->compare('fkNews.status', ConstActStatus::PUBLISHING);
        
        $count = $this->count($criteria);
        $criteria->order = 't.id desc';
        $criteria->offset = ($page - 1) * $size;
        $criteria->limit = $size;
        $rst = $this->findAll($criteria);
        
        $news = array();
        foreach ($rst as $k => $v) {
            $newsInfo = NewsInfo::model()->profile(NULL, $v->news_id, $uid);
            if (empty($newsInfo)) {
                continue;
            }
            $newsInfo['lov_time'] = $v->lov_time;
            array_push($news, $newsInfo);
        }
        return array(
            'total_num' => $count,
            'news' => $news
            );
    }
    
}
