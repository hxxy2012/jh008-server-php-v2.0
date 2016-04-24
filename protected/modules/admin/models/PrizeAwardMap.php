<?php

/**
 * This is the model class for table "prize_award_map".
 *
 * The followings are the available columns in table 'prize_award_map':
 * @property string $id
 * @property string $prize_id
 * @property string $award_id
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property PrizeInfo $prize
 * @property AwardInfo $award
 */
class PrizeAwardMap extends AdminModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'prize_award_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('prize_id, award_id, status', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('prize_id, award_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, prize_id, award_id, status', 'safe', 'on'=>'search'),
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
			//'prize' => array(self::BELONGS_TO, 'PrizeInfo', 'prize_id'),
			//'award' => array(self::BELONGS_TO, 'AwardInfo', 'award_id'),
            
            'fkAward' => array(self::BELONGS_TO, 'AwardInfo', 'award_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '抽奖方案与奖项关联id',
			'prize_id' => '方案id',
			'award_id' => '奖项id',
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
		$criteria->compare('prize_id',$this->prize_id,true);
		$criteria->compare('award_id',$this->award_id,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PrizeAwardMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加活动抽奖方案和奖项关联
     * @param type $prizeId
     * @param type $awardId
     */
    public function add($prizeId, $awardId) 
    {
        $model = new PrizeAwardMap();
        $model->prize_id = $prizeId;
        $model->award_id = $awardId;
        $model->status = ConstStatus::NORMAL;
        return $model->save();
    }
    
    
    /**
     * 获取抽奖方案的奖项列表
     * @param type $prizeId
     */
    public function getAwards($prizeId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.prize_id', $prizeId);
        $cr->compare('t.status', '<>' . ConstStatus::DELETE);
        
        $cr->with = 'fkAward';
        $cr->compare('fkAward.status', '<>' . ConstStatus::DELETE);
        
        $count = $this->count($cr);
        $rst = $this->findAll($cr);
        
        $awards = array();
        foreach ($rst as $v) {
            $award = array();
            $award['id'] = $v->fkAward->id;
            $award['name'] = $v->fkAward->name;
            $award['status'] = $v->fkAward->status;
            $award['create_time'] = $v->fkAward->create_time;
            array_push($awards, $award);
        }
        return array(
            'total_num' => $count,
            'awards' => $awards
        );
    }
    
    
    /**
     * 获取奖项对应的抽奖方案
     * @param type $awardId
     */
    public function getPrize($awardId)
    {
        $model = $this->find(
                'award_id=:awardId and status<>-1', 
                array(
                    ':awardId' => $awardId
                ));
        return PrizeInfo::model()->findByPk($model->prize_id);
    }
    
}
