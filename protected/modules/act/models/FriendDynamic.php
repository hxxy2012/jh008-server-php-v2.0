<?php

/**
 * This is the model class for table "friend_dynamic".
 *
 * The followings are the available columns in table 'friend_dynamic':
 * @property string $id
 * @property string $u_id
 * @property string $dynamic_id
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property UserInfo $u
 * @property UserDynamic $dynamic
 */
class FriendDynamic extends ActModel
{
    const baseTableName = 'friend_dynamic';

    protected $tableName;

    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		//return 'friend_dynamic';
        return $this->tableName;
	}
    
    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('u_id, dynamic_id, create_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('u_id, dynamic_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, u_id, dynamic_id, status, create_time', 'safe', 'on'=>'search'),
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
			//'dynamic' => array(self::BELONGS_TO, 'UserDynamic', 'dynamic_id'),
            
            'fkDynamic' => array(self::BELONGS_TO, 'UserDynamic', 'dynamic_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '用户好友动态的关联id',
			'u_id' => '用户id',
			'dynamic_id' => '动态id',
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
		$criteria->compare('u_id',$this->u_id,true);
		$criteria->compare('dynamic_id',$this->dynamic_id,true);
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
	 * @return FriendDynamic the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 某个用户自己和好友的动态
     * 
     * @param type $u_id 用户id
     * @param type $cityId 城市id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function dynamics($u_id, $cityId, $page, $size)
    {
        $this->changeTableNameWithUid($u_id);
        
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $u_id);
        $cr->compare('t.status', ConstStatus::NORMAL);
        
        $cr->with = 'fkDynamic';
        $cr->compare('fkDynamic.status', ConstStatus::NORMAL);
        
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $dynamics = array();
        foreach ($rst as $v) {
            $dynamic = UserDynamic::model()->dynamic($v->dynamic_id);
            if (empty($dynamic)) {
                continue;
            }
            $dynamic['user'] = UserInfo::model()->profile(NULL, $dynamic['author_id'], $cityId, $u_id, NULL, FALSE);
            array_push($dynamics, $dynamic);
        }
        
        return array(
            'total_num' => $count,
            'dynamics' => $dynamics
        );
    }
    
    
    /**
     * 好友动态添加
     * 
     * @param type $model 模型
     * @param type $u_id 用户id
     * @param type $dynamic_id 被关注者发布的动态id
     */
    public function add($model, $u_id, $dynamic_id)
    {
        $this->changeTableNameWithUid($u_id);
        
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $u_id);
        $cr->compare('t.dynamic_id', $dynamic_id);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $fd = $this->find($cr);
        if (!empty($fd)) {
            return FALSE;
        }
        
        if (empty($model)) {
            $model = new FriendDynamic();
        }
        if (!empty($u_id)) {
            $model->u_id = $u_id;
        }
        if (!empty($dynamic_id)) {
            $model->dynamic_id = $dynamic_id;
        }
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
    
    /**
     * 将对应的用户id转化为对应的目标id（一致性哈希算法）
     * 
     * @param type $uid 用户id
     */
    public function uid2TargetId($uid)
    {
        $fhash = new Flexihash();
        $targets = range(1, 10);
        $fhash->addTargets($targets);
        return $fhash->lookup($uid);
    }
    
    
    /**
     * 根据用户id改变表名
     * 
     * @param type $uid 用户id
     */
    public function changeTableNameWithUid($uid) 
    {
        $this->tableName = self::baseTableName . '_' . $this->uid2TargetId($uid);
        //重置表相关配置
        $this->refreshMetaData();
    }
    
}
