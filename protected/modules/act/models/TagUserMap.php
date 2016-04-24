<?php

/**
 * This is the model class for table "tag_user_map".
 *
 * The followings are the available columns in table 'tag_user_map':
 * @property string $id
 * @property string $tag_id
 * @property string $u_id
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property TagInfo $tag
 * @property UserInfo $u
 */
class TagUserMap extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tag_user_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tag_id, u_id', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('tag_id, u_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, tag_id, u_id, status', 'safe', 'on'=>'search'),
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
			//'tag' => array(self::BELONGS_TO, 'TagInfo', 'tag_id'),
			//'u' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
            
            'fkTag' => array(self::BELONGS_TO, 'TagInfo', 'tag_id', 'condition' => 'fkTag.status=0'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '用户感兴趣的标签关联id',
			'tag_id' => '标签id',
			'u_id' => '用户id',
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
		$criteria->compare('tag_id',$this->tag_id,true);
		$criteria->compare('u_id',$this->u_id,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TagUserMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 设置用户感兴趣的标签
     * @param type $tagIds
     * @param type $uid
     */
    public function setLovedTags($tagIds, $uid)
    {
        $allCriteria = new CDbCriteria();
        $allCriteria->compare('t.u_id', $uid);
        $tags = $this->findAll($allCriteria);
        
        $new = $tagIds;
        
        //db已有tag
        $has = array();
        foreach ($tags as $k => $v) {
            $has[$k] = intval($v->tag_id);
        }
        
        //需要新插入的
        $adds = array_diff($new, $has);
        
        //已有且应添加（status改0）（取出has中有且有new中也有的）
        $upHas = array_intersect($new, $has);
        
        //已有不需添加（status改-1）(取出new中没有，has中有的)
        $delHas = array_diff($has, $new);
        
        $transaction = Yii::app()->dbAct->beginTransaction();
        
        try{
            if (!empty($adds)) {
                foreach ($adds as $k => $v) {
                    $tum = new TagUserMap();
                    $tum->tag_id = $v;
                    $tum->u_id = $uid;
                    $tum->status = ConstStatus::NORMAL;
                    $tum->save();
                }
            }
            if (!empty($upHas)) {
                $this->updateAll(array(
                    'status' => ConstStatus::NORMAL,
                ),
                'tag_id in (' . implode(',', $upHas) . ') and u_id=:uid',
                array(
                    ':uid' => $uid,
                    )
                );
            }
            if (!empty($delHas)) {
                $this->updateAll(array(
                    'status' => ConstStatus::DELETE,
                ),
                'tag_id in (' . implode(',', $delHas) . ') and u_id=:uid',
                array(
                    ':uid' => $uid,
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
    
    
    /**
     * 获取用户感兴趣的标签
     * @param type $uid
     */
    public function getTags($uid)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('u_id', $uid);
        $criteria->with = 'fkTag';
        $criteria->compare('t.status', 0);
        $criteria->compare('fkTag.count', '>' . 0);
        $rst = $this->findAll($criteria);

        $tags = array();
        foreach ($rst as $k => $v) {
            $tag = array();
            $tag['id'] = $v->fkTag->id;
            $tag['name'] = $v->fkTag->name;
            $tag['count'] = $v->fkTag->count;
            array_push($tags, $tag);
        }
        return $tags;
    }
    
    
    /**
     * 获取用户感兴趣的标签ids
     * @param type $uid
     */
    public function getTagIds($uid)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('u_id', $uid);
        $criteria->with = 'fkTag';
        $criteria->compare('t.status', 0);
        $criteria->compare('fkTag.count', '>' . 0);
        $rst = $this->findAll($criteria);

        $tagIds = array();
        foreach ($rst as $v) {
            array_push($tagIds, $v->tag_id);
        }
        return $tagIds;
    }
    
}
