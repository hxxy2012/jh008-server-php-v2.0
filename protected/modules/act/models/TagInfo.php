<?php

/**
 * This is the model class for table "tag_info".
 *
 * The followings are the available columns in table 'tag_info':
 * @property string $id
 * @property string $name
 * @property integer $status
 * @property string $count
 *
 * The followings are the available model relations:
 * @property ActTagMap[] $actTagMaps
 * @property TagUserMap[] $tagUserMaps
 */
class TagInfo extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tag_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, count', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>16),
			array('count', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, status, count', 'safe', 'on'=>'search'),
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
			//'actTagMaps' => array(self::HAS_MANY, 'ActTagMap', 'tag_id'),
			//'tagUserMaps' => array(self::HAS_MANY, 'TagUserMap', 'tag_id'),
            
            'fkActs' => array(self::HAS_MANY, 'ActTagMap', 'tag_id', 'on' => 'fkActs.status=0'),
            'fkAllActs' => array(self::HAS_MANY, 'ActTagMap', 'tag_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '标签id',
			'name' => '名称',
			'status' => '状态：-1删除，0正常',
			'count' => '所属活动数量（不包括已结束）',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('count',$this->count,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TagInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 获取可供选择的标签（没有结束的活动数大于0）
     * @return array
     */
    public function getSltTags($isMarkLoved, $uid = NULL)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'status='. ConstStatus::NORMAL .' and count>0';
        $criteria->order = 'count desc, id asc';
        $rst = $this->findAll($criteria);
        
        $lovedTagIds = array();
        if ($isMarkLoved && !empty($uid)) {
            $lovedTagIds = TagUserMap::model()->getTagIds($uid);
        }
        $tags = array();
        foreach ($rst as $k => $v) {
            $tag = array();
            $tag['id'] = $v->id;
            $tag['name'] = $v->name;
            $tag['count'] = $v->count;
            if ($isMarkLoved) {
                $tag['isLoved'] = in_array($v->id, $lovedTagIds) ? 1 : 0;
            }
            array_push($tags, $tag);
        }
        return $tags;
    }
    
    
    /**
     * 获取所有的标签（包括所包含的活动全都结束了的）
     * @return array
     */
    public function getAllTags($isMarkLoved, $uid = NULL)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'status=' . ConstStatus::NORMAL;
        $criteria->order = 'count desc, id asc';
        $rst = $this->findAll($criteria);
        
        $lovedTagIds = array();
        if ($isMarkLoved) {
            $lovedTagIds = TagUserMap::model()->getTagIds($uid);
        }
        $tags = array();
        foreach ($rst as $k => $v) {
            $tag = array();
            $tag['id'] = $v->id;
            $tag['name'] = $v->name;
            $tag['count'] = $v->count;
            if ($isMarkLoved) {
                $tag['isLoved'] = in_array($v->id, $lovedTagIds) ? 1 : 0;
            }
            array_push($tags, $tag);
        }
        return $tags;
    }
    
    
    /**
     * 刷新标签所有的未结束的活动数
     */
    public function refreshCount()
    {
        $criteria = new CDbCriteria();
        $criteria->with = 'fkActs.fkAct';
        $rst = $this->findAll($criteria);
        
        $transaction = Yii::app()->dbAct->beginTransaction();
        try {
            foreach ($rst as $v) {
                $count = 0;
                foreach ($v->fkActs as $value) {
                    if (strtotime($value->fkAct->e_time) > time() && $value->fkAct->status == ConstActStatus::PUBLISHING) {
                        $count++;
                    }
                }
                if ($count == $v->count) {
                    continue;
                }
                $v->count = $count;
                $v->update_time = date("Y-m-d H:i:s", time());
                $v->update();
            }
            $transaction->commit();
        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
            $transaction->rollBack();
        }
    }
    
    
    /**
     * 添加标签
     * @param type $model
     * @return boolean
     */
    public function add($model)
    {
        $tag = TagInfo::model()->find('name=:name', array(':name' => $model->name));
        if (!empty($tag)) {
            return FALSE;
        }
        $model->status = ConstStatus::NORMAL;
        $model->count = 0;
        $model->update_time = date('Y-m-d H:i:s', time());
        return $model->save();
    }
    
    
    /**
     * 修改标签
     * @param type $model
     */
    public function updateTag($model)
    {
        $model->update_time = date('Y-m-d H:i:s', time());
        return $model->update();
    }
    
    
    /**
     * 删除标签
     * @param type $id
     */
    public function del($id)
    {
        $model = $this->findByPk($id);
        if (empty($model)) {
            return FALSE;
        }
        $model->status = ConstStatus::DELETE;
        return $model->update();
    }
    
}
