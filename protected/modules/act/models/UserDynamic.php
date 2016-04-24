<?php

/**
 * This is the model class for table "user_dynamic".
 *
 * The followings are the available columns in table 'user_dynamic':
 * @property string $id
 * @property string $author_id
 * @property string $content
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property DynamicImgMap[] $dynamicImgMaps
 * @property UserInfo $author
 */
class UserDynamic extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_dynamic';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('author_id, create_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('author_id', 'length', 'max'=>10),
			array('content', 'length', 'max'=>240),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, author_id, content, status, create_time', 'safe', 'on'=>'search'),
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
			//'dynamicImgMaps' => array(self::HAS_MANY, 'DynamicImgMap', 'dynamic_id'),
			//'author' => array(self::BELONGS_TO, 'UserInfo', 'author_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '用户动态id',
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
	 * @return UserDynamic the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 发动态
     * 
     * @param type $author_id 发送者用户id
     * @param type $content 内容
     * @param array $imgIds 图片id数组
     */
    public function add($author_id, $content, array $imgIds)
    {
        $model = new UserDynamic();
        $model->author_id = $author_id;
        $model->content = $content;
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        $rst = $model->save();
        
        if (!$rst) {
            return FALSE;
        }
        
        foreach ($imgIds as $v) {
            DynamicImgMap::model()->add($model->id, $v);
        }

        //添加到自己的好友动态
        FriendDynamic::model()->add(NULL, $author_id, $model->id);
        //添加到粉丝的好友动态任务
        FriendDynamicTask::model()->add($model->id, $model->create_time);
        return TRUE;
    }
    
    
    /**
     * 删除用户发的动态
     * 
     * @param type $dynamic_id 动态id
     */
    public function del($dynamic_id, $author_id = NULL)
    {
        $model = $this->findByPk($dynamic_id);

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
     * 某条动态信息
     * 
     * @param type $dynamicId 动态id
     */
    public function dynamic($dynamicId)
    {
        $model = $this->findByPk($dynamicId);
        if (empty($model) || ConstStatus::DELETE == $model->status) {
            return FALSE;
        }

        $dynamic = array();
        $dynamic['id'] = $model->id;
        $dynamic['author_id'] = $model->author_id;
        $dynamic['create_time'] = $model->create_time;
        $dynamic['content'] = $model->content;
        $dynamic['imgs'] = DynamicImgMap::model()->imgs($model->id, 1, 9);
        $dynamic['comment_num'] = DynamicComment::model()->countComments($model->id);
        return $dynamic;
    }
    
    
    /**
     * 某个用户自己的动态
     * 
     * @param type $author_id 发送者用户id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function dynamics($author_id, $page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.author_id', $author_id);
        $cr->compare('t.status', ConstStatus::NORMAL);
        
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $dynamics = array();
        foreach ($rst as $v) {
            $dynamic = array();
            $dynamic['id'] = $v->id;
            $dynamic['author_id'] = $v->author_id;
            $dynamic['content'] = $v->content;
            $dynamic['create_time'] = $v->create_time;
            $dynamic['imgs'] = DynamicImgMap::model()->imgs($v->id, 1, 9);
            $dynamic['comment_num'] = DynamicComment::model()->countComments($v->id);
            array_push($dynamics, $dynamic);
        }
        
        return array(
            'total_num' => $count,
            'dynamics' => $dynamics
        );
    }
    
}
