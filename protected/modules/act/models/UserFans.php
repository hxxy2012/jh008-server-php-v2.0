<?php

/**
 * This is the model class for table "user_fans".
 *
 * The followings are the available columns in table 'user_fans':
 * @property string $id
 * @property string $focus_id
 * @property string $fans_id
 * @property integer $status
 * @property string $update_time
 *
 * The followings are the available model relations:
 * @property UserInfo $focus
 * @property UserInfo $fans
 */
class UserFans extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_fans';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('focus_id, fans_id, status, update_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('focus_id, fans_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, focus_id, fans_id, status, update_time', 'safe', 'on'=>'search'),
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
			//'focus' => array(self::BELONGS_TO, 'UserInfo', 'focus_id'),
			//'fans' => array(self::BELONGS_TO, 'UserInfo', 'fans_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '用户关注id',
			'focus_id' => '被关注的用户id',
			'fans_id' => '粉丝的用户id',
			'status' => '状态',
			'update_time' => '更新时间',
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
		$criteria->compare('focus_id',$this->focus_id,true);
		$criteria->compare('fans_id',$this->fans_id,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('update_time',$this->update_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserFans the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加用户互相关注关联
     * 
     * @param type $focus_id 被关注者的用户id
     * @param type $fans_id 关注者的用户id
     */
    public function add($focus_id, $fans_id) 
    {
        if ($focus_id == $fans_id) {
            return FALSE;
        }
        $cr = new CDbCriteria();
        $cr->compare('t.focus_id', $focus_id);
        $cr->compare('t.fans_id', $fans_id);
        $model = $this->find($cr);
        
        if (empty($model)) {
            $model = new UserFans();
            $model->focus_id = $focus_id;
            $model->fans_id = $fans_id;
            $model->status = ConstStatus::NORMAL;
            $model->update_time = date('Y-m-d H:i:s');
            return $model->save();
        }
        
        if (ConstStatus::NORMAL == $model->status) {
            return FALSE;
        }
        
        $model->status = ConstStatus::NORMAL;
        $model->update_time = date('Y-m-d H:i:s');
        return $model->update();
    }
    
    
    /**
     * 取消关注
     * 
     * @param type $focus_id 被关注者的用户id
     * @param type $fans_id 关注者的用户id
     */
    public function del($focus_id, $fans_id)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.focus_id', $focus_id);
        $cr->compare('t.fans_id', $fans_id);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $model = $this->find($cr);
        
        if (empty($model)) {
            return FALSE;
        }
        
        $model->status = ConstStatus::DELETE;
        return $model->update();
    }
    
    
    /**
     * 关系
     * 
     * @param type $focus_id 被关注的用户id
     * @param type $fans_id 关注者用户id
     */
    public function get($focus_id, $fans_id)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.focus_id', $focus_id);
        $cr->compare('t.fans_id', $fans_id);
        $cr->compare('t.status', ConstStatus::NORMAL);
        return $this->find($cr);
    }


    /**
     * 关系用户
     * 
     * @param type $targetId 宿主用户id
     * @param type $cityId 城市id
     * @param type $type 类型：1关注，2粉丝
     * @param type $page 页数
     * @param type $size 每页记录数
     * @param type $uid 当前用户id
     */
    public function listUsers($targetId, $cityId, $type, $page, $size, $uid = NULL)
    {
        $cr = new CDbCriteria();
        if (1 == $type) {
            $cr->compare('t.fans_id', $targetId);
        }  else {
            $cr->compare('t.focus_id', $targetId);
        }
        $cr->compare('t.status', ConstStatus::NORMAL);
        
        $count = $this->count($cr);
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $users = array();
        //强制设置到用户资料中的参数值
        $is_focus = NULL;
        //是获取当前用户的关注者用户列表
        if ($targetId == $uid && $type == 1) {
            $is_focus = 1;
        }
        foreach ($rst as $v) {
            if (1 == $type) {
                $user = UserInfo::model()->profile(NULL, $v->focus_id, $cityId, $uid, $is_focus, FALSE);
            }  else {
                $user = UserInfo::model()->profile(NULL, $v->fans_id, $cityId, $uid, $is_focus, FALSE);
            }
            array_push($users, $user);
        }
        
        return array(
            'total_num' => $count,
            'users' => $users
        );
    }
    
    
    /**
     * 目标用户的关系用户数
     * @param type $targetId
     * @param type $type 类型：1关注，2粉丝
     */
    public function countUsers($targetId, $type)
    {
        $cr = new CDbCriteria();
        if (1 == $type) {
            $cr->compare('t.fans_id', $targetId);
        }  else {
            $cr->compare('t.focus_id', $targetId);
        }
        $cr->compare('t.status', ConstStatus::NORMAL);
        return $this->count($cr);
    }
    
    
    /**
     * 最后一个粉丝
     * 
     * @param type $uid
     */
    public function lastFans($uid, $lastMaxId = 0)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.focus_id', $uid);
        $cr->compare('t.fans_id', '>' . $lastMaxId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $cr->order = 't.fans_id asc';
        
        $model = $this->find($cr);
        if (empty($model)) {
            return NULL;
        }
        return UserInfo::model()->profile(NULL, $model->fans_id);
    }
    
}
