<?php

/**
 * This is the model class for table "news_info".
 *
 * The followings are the available columns in table 'news_info':
 * @property string $id
 * @property string $city_id
 * @property string $tag_id
 * @property string $title
 * @property string $img_id
 * @property string $intro
 * @property string $detail
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 * @property string $publish_time
 *
 * The followings are the available model relations:
 * @property CityInfo $city
 * @property ActTag $tag
 * @property ImgInfo $img
 */
class NewsInfo extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'news_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('city_id, type_id, create_time, update_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('city_id, tag_id, img_id', 'length', 'max'=>10),
			array('title', 'length', 'max'=>32),
			array('intro', 'length', 'max'=>64),
			array('detail', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, city_id, tag_id, title, img_id, intro, detail, status, create_time, update_time, publish_time', 'safe', 'on'=>'search'),
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
			//'city' => array(self::BELONGS_TO, 'CityInfo', 'city_id'),
			//'tag' => array(self::BELONGS_TO, 'ActTag', 'tag_id'),
			//'img' => array(self::BELONGS_TO, 'ImgInfo', 'img_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'city_id' => '城市id',
			'tag_id' => '标签id',
			'title' => '标题',
			'img_id' => '图像id',
			'intro' => '简介',
			'detail' => '详情',
			'status' => '状态',
			'create_time' => '创建时间',
			'update_time' => '更新时间',
			'publish_time' => '发布时间',
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
		$criteria->compare('city_id',$this->city_id,true);
		$criteria->compare('tag_id',$this->tag_id,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('img_id',$this->img_id,true);
		$criteria->compare('intro',$this->intro,true);
		$criteria->compare('detail',$this->detail,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_time',$this->update_time,true);
		$criteria->compare('publish_time',$this->publish_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return NewsInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 基本信息
     * 
     * @param type $model 资讯数据
     * @param type $newsId 资讯id
     * @param type $uid 当前用户id
     */
    public function profile($model = NULL, $newsId = NULL, $uid = NULL)
    {
        if (empty($model)) {
           $model = $this->findByPk($newsId);
        }
        if (empty($model) || ConstActStatus::PUBLISHING != $model->status) {
            return NULL;
        }
        
        $img = ImgInfo::model()->profile($model->img_id);
        
        $news = array(
            'id' => $model->id,
            'type_id' => $model->type_id,
            'title' => $model->title,
            'intro' => $model->intro,
            'publish_time' => $model->publish_time,
            'detail_url' => Yii::app()->webPage->getViewUrl('act/news/detailweb', array('newsId' => $model->id)),
            'h_img_url' => empty($img) ? NULL : $img['img_url'],
            'is_loved' => empty($uid) ? 0 : NewsLovUserMap::model()->isLoved($model->id, $uid),
        );
        
        if (ConstNewsType::TICKET == $model->type_id) {
            $ticketExtend = NewsTicketExtend::model()->profile(NULL, $model->id);
            if (!empty($ticketExtend)) {
                $news['price'] = $ticketExtend['price'];
            }
        }
        return $news;
    }
    
    
    /**
     * 完整信息
     * 
     * @param type $model 资讯数据
     * @param type $newsId 资讯id
     * @param type $uid 当前用户id
     */
    public function fullProfile($model = NULL, $newsId = NULL, $uid = NULL)
    {
        if (empty($model)) {
           $model = $this->findByPk($newsId);
        }
        if (empty($model) || ConstActStatus::PUBLISHING != $model->status) {
            return NULL;
        }
        $img = ImgInfo::model()->profile($model->img_id);
        
        $news = array(
            'id' => $model->id,
            'type_id' => $model->type_id,
            'title' => $model->title,
            'intro' => $model->intro,
            'publish_time' => $model->publish_time,
            'detail_url' => Yii::app()->webPage->getViewUrl('act/news/detailweb', array('newsId' => $model->id)),
            'h_img_url' => empty($img) ? NULL : $img['img_url'],
            'is_loved' => empty($uid) ? 0 : NewsLovUserMap::model()->isLoved($model->id, $uid),
            'loved_num' => NewsLovUserMap::model()->lovedNum($model->id) + $model->lov_base_num,
            'shared_num' => NewsShare::model()->sharedNum($model->id) + $model->share_base_num,
        );
        
        if (ConstNewsType::TICKET == $model->type_id) {
            $ticketExtend = NewsTicketExtend::model()->profile(NULL, $model->id);
            if (!empty($ticketExtend)) {
                $news['price'] = $ticketExtend['price'];
            }
        }
        
        return $news;
    }


    /**
     * 资讯搜索
     * 
     * @param type $cityId 城市id
     * @param type $typeId 类型id
     * @param type $tagId 标签id
     * @param type $keyWords 关键字
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function news($cityId, $typeId, $tagId, $keyWords, $page, $size, $uid = NULL) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.city_id', $cityId);
        $cr->compare('t.type_id', $typeId);
        $cr->compare('t.tag_id', $tagId);
        $cr->compare('t.status', ConstActStatus::PUBLISHING);
        
        if (!empty($keyWords)) {
            $cr->compare('t.title', $keyWords, TRUE);
        }
        
        $count = $this->count($cr);
        $cr->order = 't.publish_time desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $news = array();
        foreach ($rst as $v) {
            $newsInfo = $this->profile($v, NULL, $uid);
            if (empty($newsInfo)) {
                continue;
            }
            array_push($news, $newsInfo);
        }
        
        return array(
            'total_num' => $count,
            'news' => $news
        );
    }
    
    
    /**
     * 给对应城市的用户推送此资讯
     * 
     * @param type $news
     * @param type $title
     * @param type $descri
     */
    public function pushToCityUsers($news, $title, $descri)
    {
        if (empty($news)) {
            return FALSE;
        }
        return PushMsgTask::model()->add(
                ConstPushMsgTaskType::TO_NEWS_CITY_ALL, 
                $news->id, 
                NULL, 
                $title, 
                $descri, 
                PushMsgContentTool::makeFilterForNews($news->id)
                );
    }
    
}
