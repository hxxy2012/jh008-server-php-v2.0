<?php

/**
 * This is the model class for table "manager_user_tag_remark".
 *
 * The followings are the available columns in table 'manager_user_tag_remark':
 * @property string $id
 * @property string $m_id
 * @property string $tag_id
 * @property string $remark
 * @property integer $status
 * @property string $create_time
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property ManagerInfo $m
 * @property UserTag $tag
 */
class ManagerUserTagRemark extends ManagerInfo
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'manager_user_tag_remark';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('create_time, modify_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('m_id, tag_id', 'length', 'max'=>10),
			array('remark', 'length', 'max'=>256),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, m_id, tag_id, remark, status, create_time, modify_time', 'safe', 'on'=>'search'),
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
			'm' => array(self::BELONGS_TO, 'ManagerInfo', 'm_id'),
			'tag' => array(self::BELONGS_TO, 'UserTag', 'tag_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '管理员标签备注id',
			'm_id' => '管理员id',
			'tag_id' => '标签id',
			'remark' => '备注内容',
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
		$criteria->compare('m_id',$this->m_id,true);
		$criteria->compare('tag_id',$this->tag_id,true);
		$criteria->compare('remark',$this->remark,true);
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
	 * @return ManagerUserTagRemark the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加管理员对用户标签的备注
     * 
     * @param type $mid 管理员id
     * @param type $tagId 用户标签id
     * @param type $remark 备注
     * @param type $model
     */
    public function addM($mid, $tagId, $remark, $model = NULL)
    {
        if (empty($model)) {
            $model = new ManagerUserTagRemark();
        }
        $model->m_id = $mid;
        $model->tag_id = $tagId;
        $model->remark = $remark;
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        $model->modify_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
    
    /**
     * 备注列表
     * 
     * @param type $tagId 用户标签id
     * @param type $mid 管理员id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function remarksM($tagId, $mid, $page, $size) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.tag_id', $tagId);
        if (!empty($mid)) {
            $cr->compare('t.m_id', $mid);
        }
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $remarks = array();
        foreach ($rst as $v) {
            $remark = $this->profileM(NULL, $v);
            if (empty($remark)) {
                return NULL;
            }
            array_push($remarks, $remark);
        }
        
        return array(
            'total_num' => $count,
            'remarks' => $remarks,
        );
    }
    
    
    /**
     * 基本信息
     * 
     * @param type $id 备注id
     * @param type $model
     */
    public function profileM($id, $model = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($id);
        }
        $remark = array();
        $remark['id'] = $model->id;
        $remark['m_id'] = $model->m_id;
        $remark['remark'] = $model->remark;
        $remark['status'] = $model->status;
        $remark['create_time'] = $model->create_time;
        $remark['modify_time'] = $model->modify_time;
        $manager = ManagerInfo::model()->profile($model->m_id);
        if (!empty($manager)) {
            $remark['manager'] = $manager;
        }
        return $remark;
    }
    
    
    /**
     * 删除备注
     * 
     * @param type $id 备注id
     * @param type $mid 管理员id
     */
    public function delM($id, $mid)
    {
        $model = $this->findByPk($id);
        if (empty($model)) {
            return TRUE;
        }
        if ($model->m_id != $mid) {
            return FALSE;
        }
        $model->status = ConstStatus::DELETE;
        return $model->update();
    }
    
}
