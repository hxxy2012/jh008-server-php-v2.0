<?php

/**
 * This is the model class for table "dynamic_comment".
 *
 * The followings are the available columns in table 'dynamic_comment':
 * @property string $id
 * @property string $dynamic_id
 * @property string $author_id
 * @property string $at_id
 * @property string $content
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property UserInfo $dynamic
 * @property UserInfo $author
 * @property UserInfo $at
 */
class DynamicComment extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'dynamic_comment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dynamic_id, author_id, create_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('dynamic_id, author_id, at_id', 'length', 'max'=>10),
			array('content', 'length', 'max'=>120),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, dynamic_id, author_id, at_id, content, status, create_time', 'safe', 'on'=>'search'),
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
			'dynamic' => array(self::BELONGS_TO, 'UserInfo', 'dynamic_id'),
			'author' => array(self::BELONGS_TO, 'UserInfo', 'author_id'),
			'at' => array(self::BELONGS_TO, 'UserInfo', 'at_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '动态的评论id',
			'dynamic_id' => '动态id',
			'author_id' => '作者id',
			'at_id' => '被回复的用户id',
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
		$criteria->compare('dynamic_id',$this->dynamic_id,true);
		$criteria->compare('author_id',$this->author_id,true);
		$criteria->compare('at_id',$this->at_id,true);
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
	 * @return DynamicComment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    

    /**
     * 添加动态的评论
     * 
     * @param type $dynamic_id 动态id
     * @param type $author_id 评论发送者用户id
     * @param type $at_id 被回复者用户id
     * @param type $content 内容
     */
    public function add($dynamic_id, $author_id, $at_id, $content)
    {
        $model = new DynamicComment();
        $model->dynamic_id = $dynamic_id;
        $model->author_id = $author_id;
        $model->at_id = $at_id;
        $model->content = $content;
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        $r = $model->save();
        if ($r) {
            $this->pushToUsers($model->id, $model->author_id, $model->at_id, $model->content);
        }
        return $r;
    }
    
    
    /**
     * 删除用户发的动态的评论
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
     * 设置已读
     * 
     * @param type $dynamicId 动态id
     * @param type $uid 读的用户id
     */
    public function read($dynamicId, $uid)
    {
        $this->updateAll(
                array(
                    'status' => 1
                    ), 
                'status=0 and dynamic_id=:dynamicId and at_id=:uid', 
                array(
                    ':dynamicId' => $dynamicId, 
                    ':uid' => $uid
                )
                );
    }


    /**
     * 动态的评论
     * 
     * @param type $dynamicId 动态id
     * @param type $page 页数
     * @param type $size 每页记录数
     * @param type $uid 当前用户的id
     */
    public function comments($dynamicId, $page, $size, $uid = NULL)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.dynamic_id', $dynamicId);
        $cr->compare('t.status', '<>' . ConstStatus::DELETE);
        
        $count = $this->count($cr);
        $cr->order = 'id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $comments = array();
        foreach ($rst as $v) {
            $comment = array();
            $comment['id'] = $v->id;
            $comment['author_id'] = $v->author_id;
            $comment['author_user'] = UserInfo::model()->profile(NULL, $v->author_id, NULL, NULL, NULL, FALSE);
            if (!empty($v->at_id)) {
                $comment['at_id'] = $v->at_id;
                $comment['at_user'] = UserInfo::model()->profile(NULL, $v->at_id, NULL, NULL, NULL, FALSE);
            }
            $comment['content'] = $v->content;
            $comment['create_time'] = $v->create_time;
            array_push($comments, $comment);
        }
        
        //已知当前用户时，将回复给当前用户的未读评论设为已读
        if (!empty($uid)) {
            $this->read($dynamicId, $uid);
        }
        
        return array(
            'total_num' => $count,
            'comments' => $comments
        );
    }
    
    
    /**
     * 动态的评论的条数
     * 
     * @param type $dynamicId 动态id
     */
    public function countComments($dynamicId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.dynamic_id', $dynamicId);
        $cr->compare('t.status', '<>' . ConstStatus::DELETE);
        return $this->count($cr);
    }
    
    
    public function pushToUsers($dynamicComment, $authorUser, $atUser, $content)
    {
        if (empty($dynamicComment) || empty($authorUser) || empty($atUser)) {
            return FALSE;
        }
        $msg = PushMsgContentTool::makeUserReply(
                $authorUser->nick_name,
                $atUser->nick_name,
                $content
                );
        return PushMsgTask::model()->add(
                ConstPushMsgTaskType::TO_DYNAMIC_COMMENT_USERS, 
                $dynamicComment->id, 
                $authorUser->id, 
                $msg['title'], 
                $msg['descri'], 
                PushMsgContentTool::makeFilterForDynamic($dynamicComment->dynamic_id)
                );
    }
    
}
