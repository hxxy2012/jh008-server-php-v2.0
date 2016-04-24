<?php

/**
 * This is the model class for table "act_comment".
 *
 * The followings are the available columns in table 'act_comment':
 * @property string $id
 * @property string $act_id
 * @property string $author_id
 * @property string $content
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property ActInfo $act
 * @property UserInfo $author
 */
class ActComment extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_comment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('act_id, author_id, create_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('act_id, author_id', 'length', 'max'=>10),
			array('content', 'length', 'max'=>120),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, act_id, author_id, content, status, create_time', 'safe', 'on'=>'search'),
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
			//'act' => array(self::BELONGS_TO, 'ActInfo', 'act_id'),
			//'author' => array(self::BELONGS_TO, 'UserInfo', 'author_id'),
            
            'fkAct' => array(self::BELONGS_TO, 'ActInfo', 'act_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动的评论id',
			'act_id' => '活动id',
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
		$criteria->compare('act_id',$this->act_id,true);
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
	 * @return ActComment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加活动的评论
     * 
     * @param type $act_id 活动id
     * @param type $author_id 评论发送者用户id
     * @param type $content 内容
     */
    public function add($act_id, $author_id, $content)
    {
        $model = new ActComment();
        $model->act_id = $act_id;
        $model->author_id = $author_id;
        $model->content = $content;
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
    
    /**
     * 删除用户发的活动的评论
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
     * 用户评论过的活动列表
     * 
     * @param type $uid 用户id
     * @param type $page 页数
     * @param type $size 每页记录数
     * @param type $currUid 当前用户id
     */
    public function acts($uid, $page, $size, $currUid = NULL)
    {
        //cdbcriteria方式加了joindistinct无效
        //$cr = new CDbCriteria();
        //$cr->select = array('act_id');
        //$cr->distinct = TRUE;
        //$cr->compare('t.author_id', $uid);
        //$cr->compare('t.status', ConstStatus::NORMAL);
        
        //$cr->with = 'fkAct';
        //$cr->compare('fkAct.status', ConstActStatus::PUBLISHING);
        
        //$count = $this->count($cr);
        //$cr->order = 't.id desc';
        //$cr->offset = ($page - 1) * $size;
        //$cr->limit = $size;
        //$rst = $this->findAll($cr);
        
        $countSql = "SELECT COUNT(DISTINCT ac.act_id) AS count FROM act_comment AS ac LEFT JOIN act_info AS a ON ac.act_id=a.id WHERE ac.author_id=:authorId AND ac.status=:acStatus AND a.status=:actStatus";
        $countCommand = $this->getDbConnection()->createCommand($countSql);
        $countCommand->bindParam(':authorId', $uid, PDO::PARAM_INT);
        $countCommand->bindValue(':acStatus', ConstStatus::NORMAL, PDO::PARAM_INT);
        $countCommand->bindValue(':actStatus', ConstActStatus::PUBLISHING, PDO::PARAM_INT);
        $rst = $countCommand->queryRow();
        $count = $rst['count'];
        
        $sql = "SELECT DISTINCT ac.act_id FROM act_comment AS ac LEFT JOIN act_info AS a ON ac.act_id=a.id WHERE ac.author_id=:authorId AND ac.status=:acStatus AND a.status=:actStatus ORDER BY ac.id DESC LIMIT :offset,:limit";
        $command = $this->getDbConnection()->createCommand($sql);
        $command->bindParam(':authorId', $uid, PDO::PARAM_INT);
        $command->bindValue(':acStatus', ConstStatus::NORMAL, PDO::PARAM_INT);
        $command->bindValue(':actStatus', ConstActStatus::PUBLISHING, PDO::PARAM_INT);
        $offset = ($page - 1) * $size;
        $limit = $size * 1;
        $command->bindParam(':offset', $offset, PDO::PARAM_INT);
        $command->bindParam(':limit', $limit, PDO::PARAM_INT);
        $rst = $command->queryAll();
        
        $acts = array();
        foreach ($rst as $v) {
            $act = ActInfo::model()->profile(NULL, $v['act_id'], empty($currUid) ? $uid : $currUid, FALSE);
            if (empty($act)) {
                continue;
            }
            array_push($acts, $act);
        }
        
        return array(
            'total_num' => $count,
            'acts' => $acts
        );
    }
    
    
    /**
     * 活动评论
     * 
     * @param type $actId 活动id
     * @param type $page 页数
     * @param type $size 每页记录数
     * @param type $currUid 当前用户id
     */
    public function comments($actId, $page, $size, $currUid = NULL)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        
        $count = $this->count($cr);
        $cr->order = 't.id desc';
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
