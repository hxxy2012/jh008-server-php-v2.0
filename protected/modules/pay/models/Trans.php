<?php

/**
 * This is the model class for table "trans".
 *
 * The followings are the available columns in table 'trans':
 * @property string $id
 * @property string $trans_no
 * @property string $trans_type
 * @property string $out_order_no
 * @property string $u_id
 * @property string $target_u_id
 * @property double $amount
 * @property string $descri
 * @property integer $status
 * @property string $create_time
 * @property string $modify_time
 */
class Trans extends PayModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'trans';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('trans_no, trans_type, out_order_no, u_id, target_u_id, amount, status, create_time, modify_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('amount', 'numerical'),
			array('trans_no, out_order_no', 'length', 'max'=>128),
			array('u_id, target_u_id', 'length', 'max'=>10),
			array('trans_type, descri', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, trans_no, trans_type, out_order_no, u_id, target_u_id, amount, descri, status, create_time, modify_time', 'safe', 'on'=>'search'),
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
			'id' => '交易流水id',
			'trans_no' => '交易序列号',
			'trans_type' => '交易类型',
			'out_order_no' => '外部订单号',
			'u_id' => '用户id',
			'target_u_id' => '交易用户id',
			'amount' => '交易金额',
			'descri' => '描述',
			'status' => '状态',
			'create_time' => '创建时间',
			'modify_time' => '更新时间',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Trans the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加交易流水
     * 
     * @param type $trans_type 交易流水类型
     * @param type $out_order_no 外部订单号
     * @param type $u_id 用户id
     * @param type $target_u_id 交易用户id
     * @param type $amount 金额
     * @param type $descri 描述
     */
    public function add($trans_type, $out_order_no, $u_id, $target_u_id, $amount, $descri)
    {
        $model = new Trans();
        $model->trans_no = Yii::app()->pay->buildUniqueNo();
        $model->trans_type = $trans_type;
        $model->out_order_no = $out_order_no;
        $model->u_id = $u_id;
        $model->target_u_id = $target_u_id;
        $model->amount = $amount;
        $model->descri = $descri;
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        $model->modify_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
    
    /**
     * 交易流水
     * 
     * @param type $uid 用户id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function userTrans($uid, $page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $uid);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $trans = array();
        foreach ($rst as $v) {
            $t = array();
            $t['id'] = $v->id;
            $t['trans_no'] = $v->trans_no;
            $t['trans_type'] = $v->trans_type;
            $t['out_order_no'] = $v->out_order_no;
            $t['u_id'] = $v->u_id;
            $t['target_u_id'] = $v->target_u_id;
            $t['amount'] = $v->amount;
            $t['status'] = $v->status;
            $t['create_time'] = $v->create_time;
            $t['modify_time'] = $v->modify_time;
            array_push($trans, $t);
        }
        return array(
            'count' => $count,
            'trans' => $trans,
        );
    }
    
}
