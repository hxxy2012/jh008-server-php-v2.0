<?php

/**
 * This is the model class for table "act_enroll_old".
 *
 * The followings are the available columns in table 'act_enroll_old':
 * @property string $id
 * @property string $act_id
 * @property string $u_id
 * @property string $name
 * @property string $phone
 * @property integer $people_num
 * @property double $lon
 * @property double $lat
 * @property string $address
 * @property integer $status
 * @property string $create_time
 */
class ActEnrollOld extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_enroll_old';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('act_id, u_id, name, phone, create_time', 'required'),
			array('people_num, status', 'numerical', 'integerOnly'=>true),
			array('lon, lat', 'numerical'),
			array('act_id, u_id', 'length', 'max'=>10),
			array('name, phone', 'length', 'max'=>32),
			array('address', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, act_id, u_id, name, phone, people_num, lon, lat, address, status, create_time', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动报名关联id',
			'act_id' => '活动id',
			'u_id' => '用户id',
			'name' => '姓名',
			'sex' => '性别',
			'birth' => '生日',
			'phone' => '联系电话',
			'with_people_num' => '随行人数',
			'lon' => '经度',
			'lat' => '纬度',
			'address' => '地理位置信息',
			'status' => '状态',
			'create_time' => '创建时间',
			'modify_time' => '修改时间',
			'group_id' => 'TA的分组id',
			'serial_no' => '序号',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActEnrollOld the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 是否报过名
     * 
     * @param type $actId 活动id
     * @param type $uid 用户id
     */
    public function checkEnroll($actId, $uid)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.u_id', $uid);
        $model = $this->find($cr);
        if (empty($model)) {
            return FALSE;
        }
        return TRUE;
    }
    
    
    public function add($model)
    {
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
    
    /**
     * 用户报名列表
     * 
     * @param type $uid 用户id
     * @param type $page 页数
     * @param type $size 每页记录数
     * @param type $currUid 当前用户id
     */
    public function userEnrolls($uid, $page, $size, $currUid = NULL)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $uid);
        $cr->compare('t.status', ConstStatus::NORMAL);
        
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $enrolls = array();
        foreach ($rst as $v) {
            $enroll = array();
            $enroll['id'] = $v->id;
            $enroll['act_id'] = $v->act_id;
            $enroll['name'] = $v->name;
            $enroll['phone'] = $v->phone;
            $enroll['people_num'] = $v->people_num;
            $enroll['create_time'] = $v->create_time;
            $enroll['act'] = ActInfo::model()->profile(NULL, $v->act_id, empty($currUid) ? $uid : $currUid, TRUE);
            array_push($enrolls, $enroll);
        }
        return array(
            'total_num' => $count,
            'enrolls' => $enrolls
        );
    }
    
}
