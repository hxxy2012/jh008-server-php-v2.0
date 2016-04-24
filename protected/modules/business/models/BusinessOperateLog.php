<?php

/**
 * This is the model class for table "business_operate_log".
 *
 * The followings are the available columns in table 'business_operate_log':
 * @property string $id
 * @property string $op_ter
 * @property string $type
 * @property string $operate
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property BusinessInfo $opTer
 */
class BusinessOperateLog extends BusinessModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'business_operate_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('op_ter, type', 'length', 'max'=>10),
			array('operate', 'length', 'max'=>120),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, op_ter, type, operate, status', 'safe', 'on'=>'search'),
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
			//'opTer' => array(self::BELONGS_TO, 'BusinessInfo', 'op_ter'),
            
            'fkBusiness' => array(self::BELONGS_TO, 'BusinessInfo', 'op_ter'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '商家操作日志id',
			'op_ter' => '操作者id',
			'type' => '日志类型',
			'operate' => '操作内容',
			'status' => '状态',
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
		$criteria->compare('op_ter',$this->op_ter,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('operate',$this->operate,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return BusinessOperateLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    

    /**
     * 记录日志
     * @param type $bid
     * @param type $operate
     * @param type $type
     */
    public function log($bid, $operate, $type = 0) 
    {
        $log = new BusinessOperateLog();
        $log->op_ter = $bid;
        $log->type = $type;
        $log->operate = $operate;
        $log->status = ConstStatus::NORMAL;
        $log->op_time = date('Y-m-d H:i:s');
        return $log->save();
    }


    /**
     * 获取日志列表
     */
    public function getLogs($page, $size, $bid = NULL)
    {
        $cr = new CDbCriteria();
        $cr->with = 'fkBusiness';
        if (!empty($bid)) {
            $cr->compare('op_ter', $bid);
        }
        $count = $this->count($cr);
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $cr->order = 't.id desc';
        $rst = $this->findAll($cr);
        
        $logs = array();
        foreach ($rst as $v) {
            $log = array();
            $log['id'] = $v->id;
            $log['op_ter'] = $v->op_ter;
            if (!empty($v->fkBusiness)) {
                $business = array();
                $business['id'] = $v->fkBusiness->id;
                $business['u_name'] = $v->fkBusiness->u_name;
                $business['status'] = $v->fkBusiness->status;
                $business['name'] = $v->fkBusiness->name;
                $business['address'] = $v->fkBusiness->address;
                $business['contact_phone'] = $v->fkBusiness->contact_phone;
                $business['contact_email'] = $v->fkBusiness->contact_email;
                $business['contact_descri'] = $v->fkBusiness->contact_descri;
                $log['business'] = $business;
            }
            $log['type'] = $v->type;
            $log['operate'] = $v->operate;
            $log['status'] = $v->status;
            $log['op_time'] = $v->op_time;
            array_push($logs, $log);
        }
        return array(
            'total_num' => $count,
            'logs' => $logs
        );
    }
    
}
