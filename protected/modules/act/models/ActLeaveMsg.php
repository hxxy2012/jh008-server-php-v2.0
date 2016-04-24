<?php

/**
 * This is the model class for table "act_leave_msg".
 *
 * The followings are the available columns in table 'act_leave_msg':
 * @property string $id
 * @property string $act_id
 * @property string $author_id
 * @property integer $is_manager
 * @property string $content
 * @property integer $status
 * @property string $create_time
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property ActInfo $act
 * @property UserInfo $author
 */
class ActLeaveMsg extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_leave_msg';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('act_id, author_id, create_time, modify_time', 'required'),
			array('is_manager, status', 'numerical', 'integerOnly'=>true),
			array('act_id, author_id', 'length', 'max'=>10),
			array('content', 'length', 'max'=>120),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, act_id, author_id, is_manager, content, status, create_time, modify_time', 'safe', 'on'=>'search'),
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
			'is_manager' => '是否管理员',
			'content' => '内容',
			'status' => '状态',
			'create_time' => '创建时间',
			'modify_time' => '修改时间',
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
		$criteria->compare('is_manager',$this->is_manager);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('modify_time',$this->modify_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActLeaveMsg the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加活动的留言
     * 
     * @param type $act_id 活动id
     * @param type $author_id 评论发送者用户id
     * @param type $content 内容
     * @param type $isManager 是否管理员
     */
    public function add($act_id, $author_id, $content, $isManager = FALSE)
    {
        $model = new ActLeaveMsg();
        $model->act_id = $act_id;
        $model->author_id = $author_id;
        $model->content = $content;
        if ($isManager) {
            $model->is_manager = 1;
        }  else {
            $model->is_manager = 0;
        }
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        $model->modify_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
    
    /**
     * 活动留言
     * 
     * @param type $actId 活动id
     * @param type $page 页数
     * @param type $size 每页记录数
     * @param type $currUid 当前用户id
     */
    public function messages($actId, $page, $size, $currUid = NULL)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        
        $count = $this->count($cr);
        $cr->order = 't.id asc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $messages = array();
        foreach ($rst as $v) {
            $model = array();
            $model['id'] = $v->id;
            $model['author_id'] = $v->author_id;
            $model['is_manager'] = $v->is_manager;
            $model['content'] = $v->content;
            $model['create_time'] = $v->create_time;
            if (!empty($v->author_id)) {
                $model['user'] = UserInfo::model()->profile(NULL, $v->author_id);
            }
            array_push($messages, $model);
        }
        
        return array(
            'total_num' => $count,
            'messages' => $messages,
        );
    }
    
}
