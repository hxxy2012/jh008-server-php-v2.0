<?php

/**
 * This is the model class for table "index_page_act_list".
 *
 * The followings are the available columns in table 'index_page_act_list':
 * @property string $id
 * @property string $filter
 * @property string $act_list
 */
class IndexPageActList extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'index_page_act_list';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('filter', 'required'),
			array('filter', 'length', 'max'=>12),
			array('act_list', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, filter, act_list', 'safe', 'on'=>'search'),
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
			'id' => '首页显示活动关联id',
			'filter' => '搜索关键字（状态_标签id,标签id[升序]）',
			'act_list' => '活动id数组序列化',
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
		$criteria->compare('filter',$this->filter,true);
		$criteria->compare('act_list',$this->act_list,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return IndexPageActList the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 筛选活动
     * @param type $tagIds
     * @param type $actTimeStatus
     */
    public function getSltActs($actTimeStatus, $tagIds, $page, $size, $uid = NULL) 
    {
        //filter格式：活动时间状态_标签id升序逗号分隔
        $filter = '_';
        
        //活动时间状态
        if (!empty($actTimeStatus)) {
            $filter = $actTimeStatus . $filter;
        }
        
        //对标签id数组去重，升序排序
        if (!empty($tagIds)) {
            $filter = $filter . ArrTool::uniqueAscStr($tagIds, ',');
        }
        
        $r = $this->find('filter=:filter', array(':filter' => $filter));
        $ids = array();
        if (empty($r)) {
            //不存在对应filter的json序列化的活动id，查询后存储
            $ids = $this->newActIdsByFilter($filter);
            $this->insFilterActs($filter, $ids);
        }  else {
            //已存在对应filter的json序列化的活动id
            $ids = json_decode($r->act_list);
        }
        
        $totalNum = count($ids);
        //按分页取出需要的活动id
        $needIds = ArrTool::sliceByPageAndSize($ids, $page, $size);
        
        return array(
            'total_num' => $totalNum,
            'acts' => ActInfo::model()->getActs($needIds, $uid),
            );
    }


    /**
     * 根据filter新查找活动id
     * @param type $filter
     */
    public function newActIdsByFilter($filter) 
    {
        $pos = strpos($filter, '_');
        $actTiSt = '';
        if ($pos > 0) {
            $actTiSt = substr($filter, 0, $pos);
        }
        $tagIds = array();
        if (strlen($filter) > $pos + 1) {
            if (strpos($filter, ',') > 0) {
                $tagIds = explode(',', substr($filter, $pos + 1));   
            }  else {
                $tagIds[0] = substr($filter, $pos + 1);
            }
        }
        return ActTagMap::model()->getActIds($tagIds, $actTiSt);
    }
    
    
    /**
     * 将活动id序列化后存入
     * @param type $filter
     * @param array $acts
     */
    public function insFilterActs($filter, array $actIds) 
    {
        $model = new IndexPageActList();
        $model->filter = $filter;
        $model->act_list = json_encode($actIds);
        $model->update_time = date("Y-m-d H:i:s", time());
        return $model->save();
    }
    
    
    /**
     * 刷新所有的filter对应的活动id
     */
    public function refreshAll() 
    {
        $rst = $this->findAll();
        
        $transaction = Yii::app()->dbAct->beginTransaction();
        try {
            foreach ($rst as $k => $v) {
                $ids = $this->newActIdsByFilter($v->filter);
                $act_list_json = json_encode($ids);
                if ($act_list_json == $v->act_list) {
                    continue;
                }
                $v->act_list = $act_list_json;
                $v->update_time = date("Y-m-d H:i:s", time());
                $v->update();
            }
            $transaction->commit();
        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
            $transaction->rollBack();
        }
    }
    
}
