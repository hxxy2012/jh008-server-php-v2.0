<?php

/**
 * This is the model class for table "act_lov_user_map".
 *
 * The followings are the available columns in table 'act_lov_user_map':
 * @property string $id
 * @property string $act_id
 * @property string $u_id
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property ActInfo $act
 * @property UserInfo $u
 */
class ActLovUserMap extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_lov_user_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('act_id, u_id', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('act_id, u_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, act_id, u_id, status', 'safe', 'on'=>'search'),
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
			//'u' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
            
            'fkAct' => array(self::BELONGS_TO, 'ActInfo', 'act_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动收藏关联id',
			'act_id' => '活动id',
			'u_id' => '用户id',
			'status' => '状态：-1删除，0正常',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActLovUserMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 用户是否对此活动感兴趣
     * @param type $actId
     * @param type $uid
     */
    public function isLoved($actId, $uid) 
    {
        $criteria = new CDbCriteria();
        $criteria->compare('act_id', $actId);
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
     * 获取活动的感兴趣用户数
     * @param type $actId
     */
    public function getLovedNum($actId) 
    {
        $criteria = new CDbCriteria();
        $criteria->compare('act_id', $actId);
        return $this->count($criteria);
    }
    
    
    /**
     * 添加感兴趣的活动
     * @param type $actId
     * @param type $uid
     */
    public function addLove($actId, $uid) 
    {
        $criteria = new CDbCriteria();
        $criteria->compare('act_id', $actId);
        $criteria->compare('u_id', $uid);
        $model = $this->find($criteria);
        
        if (empty($model)) {
            $model =  new ActLovUserMap();
            $model->act_id = $actId;
            $model->u_id = $uid;
            $model->status = 0;
            $model->lov_time = date('Y-m-d H:i:s');
            return $model->save();
        }
        $model->lov_time = date('Y-m-d H:i:s');
        $model->status = 0;
        return $model->update();
    }
    
    
    /**
     * 取消感兴趣的活动
     * @param type $actId
     * @param type $uid
     */
    public function delLove($actId, $uid)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('act_id', $actId);
        $criteria->compare('u_id', $uid);
        $model = $this->find($criteria);
        
        if (empty($model)) {
            return FALSE;
        }
        $model->status = -1;
        return $model->update();
    }
    
    
    /**
     * 获取用户感兴趣的活动
     * @param type $uid
     */
    public function getUActs($uid, $page, $size) 
    {
        $criteria = new CDbCriteria();
        $criteria->compare('t.u_id', $uid);
        $criteria->compare('t.status', ConstStatus::NORMAL);
        $criteria->with = array('fkAct');
        $criteria->compare('fkAct.status', ConstActStatus::PUBLISHING);
        $criteria->order = 'fkAct.t_status asc, fkAct.publish_time desc';
        $totalNum = $this->count($criteria);
        
        $criteria->offset = ($page - 1) * $size;
        $criteria->limit = $size;
        $rst = $this->findAll($criteria);
        $ids = array();
        foreach ($rst as $k => $v) {
            $ids[$k] = intval($v['act_id']);
        }
        return array(
            'total_num' => $totalNum,
            'acts' => ActInfo::model()->getActs($ids, $uid),
            );
    }
    
    
    /**
     * 收藏的活动
     * 
     * @param type $uid 用户id
     */
    public function acts($uid, $page, $size) 
    {
        $criteria = new CDbCriteria();
        $criteria->compare('t.u_id', $uid);
        $criteria->compare('t.status', ConstStatus::NORMAL);
        
        $criteria->with = 'fkAct';
        $criteria->compare('fkAct.status', ConstActStatus::PUBLISHING);
        
        $count = $this->count($criteria);
        $criteria->order = 't.id desc';
        $criteria->offset = ($page - 1) * $size;
        $criteria->limit = $size;
        $rst = $this->findAll($criteria);
        
        $acts = array();
        foreach ($rst as $k => $v) {
            $act = ActInfo::model()->profile(NULL, $v->act_id);
            if (empty($act)) {
                continue;
            }
            $act['lov_time'] = $v->lov_time;
            array_push($acts, $act);
        }
        return array(
            'total_num' => $count,
            'acts' => $acts
            );
    }
    
}
