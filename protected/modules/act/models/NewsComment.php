<?php

/**
 * This is the model class for table "news_comment".
 *
 * The followings are the available columns in table 'news_comment':
 * @property string $id
 * @property string $news_id
 * @property string $author_id
 * @property string $content
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property NewsInfo $news
 * @property UserInfo $author
 */
class NewsComment extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'news_comment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('news_id, author_id, create_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('news_id, author_id', 'length', 'max'=>10),
			array('content', 'length', 'max'=>120),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, news_id, author_id, content, status, create_time', 'safe', 'on'=>'search'),
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
			'author' => array(self::BELONGS_TO, 'UserInfo', 'author_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '资讯的评论id',
			'news_id' => '资讯id',
			'author_id' => '作者id',
			'content' => '内容',
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
		$criteria->compare('news_id',$this->news_id,true);
		$criteria->compare('author_id',$this->author_id,true);
		$criteria->compare('content',$this->content,true);
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
	 * @return NewsComment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加资讯的评论
     * 
     * @param type $news_id 资讯id
     * @param type $author_id 评论发送者用户id
     * @param type $content 内容
     */
    public function add($news_id, $author_id, $content)
    {
        $model = new NewsComment();
        $model->news_id = $news_id;
        $model->author_id = $author_id;
        $model->content = $content;
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
    
    /**
     * 删除用户发的资讯的评论
     * 
     * @param type $commentId 评论id
     * @param type $author_id 作者id
     */
    public function del($commentId, $author_id = NULL)
    {
        $model = $this->findByPk($commentId);
        
        if (empty($model)) {
            return FALSE;
        }
        
        //验证是否是本人删除
        if (!empty($author_id) && $author_id != $model->author_id) {
            return FALSE;
        }
        
        if (ConstStatus::DELETE == $model->status) {
            return FALSE;
        }
        
        $model->status = ConstStatus::DELETE;
        return $model->update();
    }
    
    
    /**
     * 用户评论过的资讯列表
     * 
     * @param type $uid 用户id
     * @param type $page 页数
     * @param type $size 每页记录数
     * @param type $currUid 当前用户id
     */
    public function news($uid, $page, $size, $currUid = NULL)
    {
        $cr = new CDbCriteria();
        $cr->select = 't.news_id';
        $cr->distinct = TRUE;
        $cr->compare('t.author_id', $uid);
        $cr->compare('t.status', ConstStatus::NORMAL);
        
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $news = array();
        foreach ($rst as $v) {
            $newsInfo = NewsInfo::model()->profile(NULL, $v->news_id, empty($currUid) ? $uid : $currUid, FALSE);
            array_push($news, $newsInfo);
        }
        
        return array(
            'total_num' => $count,
            'news' => $news
        );
    }
    
    
    /**
     * 资讯评论
     * 
     * @param type $newsId 资讯id
     * @param type $page 页数
     * @param type $size 每页记录数
     * @param type $currUid 当前用户id
     */
    public function comments($newsId, $page, $size, $currUid)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.news_id', $newsId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        
        $count = $this->count($cr);
        $cr->order = 't.id asc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $comments = array();
        foreach ($rst as $v) {
            $comment = array();
            $comment['id'] = $v->id;
            $comment['author_id'] = $v->author_id;
            $comment['content'] = $v->content;
            $comment['create_time'] = $v->create_time;
            if (!empty($v->author_id)) {
                $comment['user'] = UserInfo::model()->profile(NULL, $v->author_id);
            }
            array_push($comments, $comment);
        }
        
        return array(
            'total_num' => $count,
            'comments' => $comments
        );
    }
    
}
