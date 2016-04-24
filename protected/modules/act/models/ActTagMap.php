<?php

/**
 * This is the model class for table "act_tag_map".
 *
 * The followings are the available columns in table 'act_tag_map':
 * @property string $id
 * @property string $act_id
 * @property string $tag_id
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property ActInfo $act
 * @property TagInfo $tag
 */
class ActTagMap extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_tag_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('act_id, tag_id', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('act_id, tag_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, act_id, tag_id, status', 'safe', 'on'=>'search'),
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
			//'tag' => array(self::BELONGS_TO, 'TagInfo', 'tag_id'),
            
            'fkTag' => array(self::BELONGS_TO, 'TagInfo', 'tag_id'),
            'fkAct' => array(self::BELONGS_TO, 'ActInfo', 'act_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动标签关联id',
			'act_id' => '活动id',
			'tag_id' => '标签id',
			'status' => '状态：-1删除，0正常',
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
		$criteria->compare('tag_id',$this->tag_id,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActTagMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 获取活动所属的标签
     * @param type $actId
     * @return type
     */
    public function getTags($actId)
    {
        $criteria = new CDbCriteria;
        $criteria->compare('act_id', $actId);
        $criteria->compare('t.status ', ConstStatus::NORMAL);
        $criteria->with = 'fkTag';
        $criteria->compare('fkTag.status', ConstStatus::NORMAL);
        
        $rst = $this->findAll($criteria);
        $tags = array();
        foreach ($rst as $k => $v) {
            $tags[$k]['id'] = $v->fkTag->id;
            $tags[$k]['name'] = $v->fkTag->name;
            $tags[$k]['count'] = $v->fkTag->count;
        }
        
        return $tags;
    }
    
    
    /**
     * 根据标签和活动状态筛选活动
     * @param array $tagIds
     * @param type $actTiSta
     */
    public function getActIds(array $tagIds, $actTiSta, $cityId = 1) 
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.act_id';
        $criteria->group = 't.act_id';
        $criteria->compare('t.status', ConstStatus::NORMAL);
        if (!empty($tagIds)) {
            $criteria->compare('t.tag_id', $tagIds);
        }
        
        $criteria->with = array('fkTag', 'fkAct');
        $criteria->compare('fkTag.status', ConstStatus::NORMAL);
        $criteria->compare('fkAct.status', ConstActStatus::PUBLISHING);
        $criteria->compare('city_id', $cityId);
        if (!empty($actTiSta)) {
            $criteria->compare('fkAct.t_status', $actTiSta);
        }
        
        $criteria->order = 'fkAct.t_status asc, fkAct.publish_time desc';
        
        $rst = $this->findAll($criteria);
        
        $actIds = array();
        foreach ($rst as $k => $v) {
            $actIds[$k] = intval($v->act_id);
        }
        
        return $actIds;
    }
    
    
    /**
     * 设置活动的标签
     * @param type $tagIds
     * @param type $actId
     */
    public function setActTags($tagIds, $actId)
    {
        $allCriteria = new CDbCriteria();
        $allCriteria->compare('act_id', $actId);
        $tags = $this->findAll($allCriteria);
        
        $new = $tagIds;
        
        //db已有tag
        $has = array();
        foreach ($tags as $k => $v) {
            $has[$k] = intval($v->tag_id);
        }
        
        //需要新插入的
        $adds = array_diff($new, $has);
        
        //已有且应添加（status改0）
        $upHas = array_intersect($new, $has);
        
        //已有不需添加（status改-1）
        $delHas = array_diff($has, $new);
        
        $transaction = Yii::app()->dbAct->beginTransaction();
        
        try{
            if (!empty($adds)) {
                foreach ($adds as $k => $v) {
                    $tum = new ActTagMap();
                    $tum->tag_id = $v;
                    $tum->act_id = $actId;
                    $tum->status = ConstStatus::NORMAL;
                    $tum->save();
                }
            }
            if (!empty($upHas)) {
                $this->updateAll(array(
                    'status' => ConstStatus::NORMAL,
                ),
                'tag_id in (' . implode(',', $upHas) . ') and act_id=:actId',
                array(
                    ':actId' => $actId,
                    )
                );
            }
            if (!empty($delHas)) {
                $this->updateAll(array(
                    'status' => ConstStatus::DELETE,
                ),
                'tag_id in (' . implode(',', $delHas) . ') and act_id=:actId',
                array(
                    ':actId' => $actId,
                    )
                );
            }
            $transaction->commit();
            return TRUE;
        } catch(Exception $e){
            print_r($e->getMessage());
            $transaction->rollBack();
        }
        return FALSE;
    }
    
}
