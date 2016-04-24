<?php

/**
 * This is the model class for table "org_lov_user_map".
 *
 * The followings are the available columns in table 'org_lov_user_map':
 * @property string $id
 * @property string $org_id
 * @property string $u_id
 * @property integer $status
 * @property string $lov_time
 * @property string $create_time
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property OrgInfo $org
 * @property UserInfo $u
 */
class OrgLovUserMap extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'org_lov_user_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('org_id, u_id, lov_time, create_time, modify_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('org_id, u_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, org_id, u_id, status, lov_time, create_time, modify_time', 'safe', 'on'=>'search'),
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
			//'org' => array(self::BELONGS_TO, 'OrgInfo', 'org_id'),
			//'u' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
            
            'fkOrg' => array(self::BELONGS_TO, 'OrgInfo', 'org_id'),
            'fkUser' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动收藏关联id',
			'org_id' => '活动id',
			'u_id' => '用户id',
			'status' => '状态：-1删除，0正常',
			'lov_time' => '收藏时间',
			'create_time' => '创建时间',
			'modify_time' => '修改时间',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrgLovUserMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 用户是否对此社团感兴趣
     * 
     * @param type $orgId 社团id
     * @param type $uid 用户id
     */
    public function isLoved($orgId, $uid) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.org_id', $orgId);
        $cr->compare('t.u_id', $uid);
        $r = $this->find($cr);
        
        if (empty($r)) {
            return 0;
        }
        if (-1 == $r->status) {
            return -1;
        }
        return 1;
    }
    
    
    /**
     * 获取社团的感兴趣用户数
     * 
     * @param type $orgId 社团id
     */
    public function getLovedNum($orgId) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.org_id', $orgId);
        //当前已关注的人数
        $cr->compare('t.status', ConstStatus::NORMAL);
        return $this->count($cr);
    }
    
    
    /**
     * 添加感兴趣的社团
     * @param type $orgId 社团id
     * @param type $uid 用户id
     */
    public function addLove($orgId, $uid) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.org_id', $orgId);
        $cr->compare('t.u_id', $uid);
        $model = $this->find($cr);
        
        if (empty($model)) {
            $model =  new OrgLovUserMap();
            $model->org_id = $orgId;
            $model->u_id = $uid;
            $model->status = 0;
            $model->lov_time = date('Y-m-d H:i:s');
            $model->create_time = date('Y-m-d H:i:s');
            $model->modify_time = date('Y-m-d H:i:s');
            return $model->save();
        }
        $model->lov_time = date('Y-m-d H:i:s');
        $model->modify_time = date('Y-m-d H:i:s');
        $model->status = 0;
        return $model->update();
    }
    
    
    /**
     * 取消感兴趣的社团
     * 
     * @param type $orgId 社团id
     * @param type $uid 用户id
     */
    public function delLove($orgId, $uid)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.org_id', $orgId);
        $cr->compare('t.u_id', $uid);
        $model = $this->find($cr);
        
        if (empty($model)) {
            return FALSE;
        }
        $model->status = -1;
        $model->modify_time = date('Y-m-d H:i:s');
        return $model->update();
    }
    
    
    /**
     * 收藏的社团
     * 
     * @param type $uid 用户id
     * @param type $page 页数
     * @param type $size 每页记录数
     * @param type $currUid 当前用户id
     */
    public function orgs($uid, $page, $size, $currUid) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $uid);
        $cr->compare('t.status', ConstStatus::NORMAL);
        
        $cr->with = 'fkOrg';
        $cr->compare('fkOrg.status', ConstStatus::NORMAL);
        
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $orgs = array();
        foreach ($rst as $v) {
            $org = OrgInfo::model()->profile(NULL, $v->org_id, $currUid);
            if (empty($org)) {
                continue;
            }
            $org['lov_time'] = $v->lov_time;
            array_push($orgs, $org);
        }
        return array(
            'total_num' => $count,
            'orgs' => $orgs
            );
    }
    
    
    /**
     * 社团的关注者
     * 
     * @param type $orgId 社团id
     * @param type $page 页数
     * @param type $size 每页记录数
     * @param type $cityId 城市id
     * @param type $currUid 当前用户id
     * 
     */
    public function users($orgId, $page, $size, $cityId = NULL, $currUid = NULL)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.org_id', $orgId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        
        $cr->with = 'fkUser';
        $cr->compare('fkUser.status', ConstStatus::NORMAL);
        
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $users = array();
        foreach ($rst as $v) {
            if (empty($v->fkUser)) {
                continue;
            }
            $user = UserInfo::model()->profile($v->fkUser, NULL, $cityId, $currUid, NULL, FALSE, FALSE);
            if (empty($user)) {
                continue;
            }
            array_push($users, $user);
        }
        
        return array(
            'total_num' => $count,
            'users' => $users,
        );
    }
    
}
