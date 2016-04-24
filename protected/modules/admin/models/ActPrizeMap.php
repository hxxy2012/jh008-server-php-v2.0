<?php

/**
 * This is the model class for table "act_prize_map".
 *
 * The followings are the available columns in table 'act_prize_map':
 * @property string $id
 * @property string $act_id
 * @property string $prize_id
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property ActInfo $act
 * @property PrizeInfo $prize
 */
class ActPrizeMap extends AdminModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_prize_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('act_id, prize_id', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('act_id, prize_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, act_id, prize_id, status', 'safe', 'on'=>'search'),
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
			//'prize' => array(self::BELONGS_TO, 'PrizeInfo', 'prize_id'),
            
            'fkPrize' => array(self::BELONGS_TO, 'PrizeInfo', 'prize_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动抽奖方案关联id',
			'act_id' => '活动id',
			'prize_id' => '方案id',
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
		$criteria->compare('act_id',$this->act_id,true);
		$criteria->compare('prize_id',$this->prize_id,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActPrizeMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加活动与抽奖方案关联
     * @param type $actId
     * @param type $prizeId
     */
    public function add($actId, $prizeId)
    {
        $model = new ActPrizeMap();
        $model->act_id = $actId;
        $model->prize_id = $prizeId;
        $model->status = ConstStatus::NORMAL;
        return $model->save();
    }
    
    
    /**
     * 获取活动未结束的抽奖方案列表
     * @param type $actId
     * @param type $status -1已删除，0正常（未结束），1已结束
     */
    public function getPrizes($actId, $status)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.status', $status);
        
        $cr->with = 'fkPrize';
        $cr->compare('fkPrize.status', '<>' . ConstStatus::DELETE);
        
        $count = $this->count($cr);
        $rst = $this->findAll($cr);
        
        $prizes = array();
        foreach ($rst as $v) {
            $prize = array();
            $prize['id'] = $v->fkPrize->id;
            $prize['name'] = $v->fkPrize->name;
            $prize['status'] = $v->fkPrize->status;
            $prize['create_time'] = $v->fkPrize->create_time;
            array_push($prizes, $prize);
        }
        return array(
            'total_num' => $count,
            'prizes' => $prizes
        );
    }
    
    
    /**
     * 获取抽奖方案对应的活动
     * @param type $prizeId
     */
    public function getAct($prizeId) 
    {
        $model = $this->find(
                'prize_id=:prizeId and status<>-1', 
                array(
                    ':prizeId' => $prizeId
                ));
        return ActInfo::model()->findByPk($model->act_id);
    }
    
}
