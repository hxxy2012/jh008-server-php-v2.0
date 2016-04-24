<?php

/**
 * This is the model class for table "org_manager_map".
 *
 * The followings are the available columns in table 'org_manager_map':
 * @property string $id
 * @property string $org_id
 * @property string $u_id
 * @property integer $status
 * @property string $create_time
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property OrgInfo $org
 * @property UserInfo $u
 */
class OrgManagerMap extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'org_manager_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('org_id, u_id, create_time, modify_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('org_id, u_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, org_id, u_id, status, create_time, modify_time', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '组织管理员关联id',
			'org_id' => '组织id',
			'u_id' => '用户id',
			'status' => '状态',
			'create_time' => '创建时间',
			'modify_time' => '修改时间',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrgManagerMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 管理员
     * 
     * @param type $orgId 社团id
     * @param type $page 页数
     * @param type $size 每页记录数
     * @param type $cityId 城市id
     * @param type $uid 当前用户id
     */
    public function managers($orgId, $page, $size, $cityId, $uid) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.org_id', $orgId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $count = $this->count($cr);
        $cr->order = 't.id asc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $users = array();
        foreach ($rst as $v) {
            $user = UserInfo::model()->profile(NULL, $v->u_id, $cityId, $uid, NULL, FALSE, FALSE);
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
    
    
    /**
     * 是否是管理员
     * 
     * @param type $orgId 社团id
     * @param type $uid 用户id
     */
    public function isManager($orgId, $uid)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.org_id', $orgId);
        $cr->compare('t.u_id', $uid);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $model = $this->find($cr);
        if (empty($model)) {
            return FALSE;
        }
        return TRUE;
    }
    
}
