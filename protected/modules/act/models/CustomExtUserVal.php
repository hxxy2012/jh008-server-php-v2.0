<?php

/**
 * This is the model class for table "custom_ext_user_val".
 *
 * The followings are the available columns in table 'custom_ext_user_val':
 * @property string $id
 * @property string $c_id
 * @property string $u_id
 * @property string $value
 * @property integer $status
 * @property string $create_time
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property UserCustomExtend $c
 * @property UserInfo $u
 */
class CustomExtUserVal extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'custom_ext_user_val';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('c_id, u_id, create_time, modify_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('c_id, u_id', 'length', 'max'=>10),
			array('value', 'length', 'max'=>512),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, c_id, u_id, value, status, create_time, modify_time', 'safe', 'on'=>'search'),
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
			//'c' => array(self::BELONGS_TO, 'UserCustomExtend', 'c_id'),
			//'u' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '自定义字段与用户关联id',
			'c_id' => '自定义字段id',
			'u_id' => '用户id',
			'value' => '值',
			'status' => '状态',
			'create_time' => '创建时间',
			'modify_time' => '修改时间',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CustomExtUserVal the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 用户的自定义字段及值
     * 
     * @param type $uid
     * @param type $page
     * @param type $size
     */
    public function userCustomKeys($uid, $page, $size) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $uid);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $count = $this->count($cr);
        $cr->order = 't.id asc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $customKeys = array();
        foreach ($rst as $v) {
            $customKey = UserCustomExtend::model()->profile($v->c_id);
            if (empty($customKey)) {
                continue;
            }
            $customKey['value'] = $v->value;
            array_push($customKeys, $customKey);
        }
        
        return array(
            'total_num' => $count,
            'custom_keys' => $customKeys,
        );
    }
    
    
    /**
     * 更新自己的自定义字段值
     * 
     * @param type $cid 字段id
     * @param type $uid 用户id
     * @param type $value 值
     */
    public function up($cid, $uid, $value) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.c_id', $cid);
        $cr->compare('t.u_id', $uid);
        $model = $this->find($cr);
        
        $isNew = FALSE;
        if (empty($model)) {
            $model = new CustomExtUserVal();
            $model->c_id = $cid;
            $model->u_id = $uid;
            $isNew = TRUE;
        }
        $model->value = $value;
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        $model->modify_time = date('Y-m-d H:i:s');
        if ($isNew) {
            return $model->save();
        }
        return $model->update();
    }
    
}
