<?php

/**
 * 万能hash数据表
 * 
 * 字段与值以“:”隔开
 * 
 * 例：city:0:act:1
 * 
 * 参考：
 * 活动筛选：city:0
 * 
 * This is the model class for table "key_val_info".
 *
 * The followings are the available columns in table 'key_val_info':
 * @property string $id
 * @property string $key
 * @property string $val
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 */
class KeyValInfo extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'key_val_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('key, create_time, update_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('key', 'length', 'max'=>64),
			array('val', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, key, val, status, create_time, update_time', 'safe', 'on'=>'search'),
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
			'id' => 'hash列表id',
			'key' => '键值对key',
			'val' => '键值对val',
			'status' => '状态',
			'create_time' => '创建时间',
			'update_time' => '最后更新时间',
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
		$criteria->compare('key',$this->key,true);
		$criteria->compare('val',$this->val,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_time',$this->update_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return KeyValInfo the static model class
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
    public function getSltActs($cityId, $actTimeStatus, $tagIds, $page, $size, $uid = NULL) 
    {
        $kvArr = array();
        
        $kvArr['key'] = 'sltactsbytags';
        
        //城市
        if (!empty($cityId)) {
            $kvArr['cityid'] = $cityId;
        }
        
        //活动时间状态
        if (!empty($actTimeStatus)) {
            $kvArr['acttimestatus'] = $actTimeStatus;
        }
        
        //对标签id数组去重，升序排序
        if (!empty($tagIds)) {
            $kvArr['tagids'] = ArrTool::uniqueAscStr($tagIds, ',');
        }
        
        //根据标签搜索活动列表key
        $key = ArrTool::implodeColonArr($kvArr);
        $r = $this->findK($key);
        
        $ids = array();
        if (empty($r)) {
            //不存在对应filter的json序列化的活动id，查询后存储
            $keyV = ArrTool::explodeColonKv($key);
            $cityid = array_key_exists('cityid', $keyV) ? $keyV['cityid'] : NULL;
            $timestatus = array_key_exists('acttimestatus', $keyV) ? $keyV['acttimestatus'] : NULL;
            $tagids = array_key_exists('tagids', $keyV) ? explode(',', $keyV['tagids']) : array();
            $ids = ActTagMap::model()->getActIds($tagids, $timestatus, $cityid);
            
            $this->insKv($key, $ids);
        }  else {
            //已存在对应filter的json序列化的活动id
            $ids = json_decode($r->val);
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
     * 插入键值对
     * 
     * @param type $key
     * @param array $val
     * @return type
     */
    public function insKv($key, array $val)
    {
        $model = new KeyValInfo();
        $model->key = $key;
        $model->val = json_encode($val);
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        $model->update_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
    
    /**
     * 查找对应key
     * 
     * @param type $key
     * @return type
     */
    public function findK($key)
    {
        return $this->find('t.key=:key and t.status=:status', 
                array(
                    ':key' => $key, 
                    ':status' => ConstStatus::NORMAL
                ));
    }
    
    
    /**
     * 刷新所有的filter对应的活动id
     */
    public function refreshAll() 
    {
        $rst = $this->findAll();
        
        $transaction = Yii::app()->dbAct->beginTransaction();
        try {
            foreach ($rst as $v) {
                $kvs = ArrTool::explodeColonKv($v->key);
                switch ($kvs['key']) {
                    case 'sltactsbytags':
                        $cityid = array_key_exists('cityid', $kvs) ? $kvs['cityid'] : NULL;
                        $timestatus = array_key_exists('acttimestatus', $kvs) ? $kvs['acttimestatus'] : NULL;
                        $tagids = array_key_exists('tagids', $kvs) ? explode(',', $kvs['tagids']) : array();
                        $ids = ActTagMap::model()->getActIds($tagids, $timestatus, $cityid);
                        $act_list_json = json_encode($ids);
                        if ($act_list_json == $v->val) {
                            continue;
                        }
                        $v->val = $act_list_json;
                        $v->update_time = date("Y-m-d H:i:s");
                        $v->update();
                        break;
                    default:
                        break;
                }
            }
            $transaction->commit();
        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
            $transaction->rollBack();
        }
    }
    
    
    /**
     * 获取推荐活动
     * 
     * @param type $cityId 城市id
     * @param type $page 页数
     * @param type $size 每页活动数
     */
    public function getRecommendActs($cityId, $page ,$size, $uid = NULL)
    {
        $kvArr = array();
        $kvArr['key'] = 'cityrecommendacts';
        $kvArr['cityid'] = $cityId;
        //根据标签搜索活动列表key
        $key = ArrTool::implodeColonArr($kvArr);
        $model = $this->findK($key);
        
        if (empty($model)) {
            return array(
                'total_num' => 0,
                'acts' => array(),
            );
        }
        $ids = json_decode($model->val);
        $totalNum = count($ids);
        //按分页取出需要的活动id
        $needIds = ArrTool::sliceByPageAndSize($ids, $page, $size);
        $acts = array();
        foreach ($needIds as $v) {
            $act = ActInfo::model()->profile(NULL, $v, $uid, FALSE);
            if (empty($act)) {
                continue;
            }
            array_push($acts, $act);
        }
        
        return array(
            'total_num' => $totalNum,
            'acts' => $acts,
            );
    }
    
    
    /**
     * 获取首页轮播
     * 
     * @param type $cityId 城市id
     * @param type $page 页数
     * @param type $size 每页活动数
     */
    public function getHomeAdverts($cityId, $page ,$size, $uid = NULL)
    {
        $kvArr = array();
        $kvArr['key'] = 'cityhomeadverts';
        $kvArr['cityid'] = $cityId;
        //根据标签搜索轮播列表key
        $key = ArrTool::implodeColonArr($kvArr);
        $model = $this->findK($key);
        
        if (empty($model)) {
            return array(
                'total_num' => 0,
                'news' => array(),
            );
        }
        $ids = json_decode($model->val);
        $totalNum = count($ids);
        //按分页取出需要的轮播id
        $needIds = ArrTool::sliceByPageAndSize($ids, $page, $size);
        
        $news = array();
        foreach ($needIds as $v) {
            $newsInfo = NewsInfo::model()->profile(NULL, $v, $uid);
            if (empty($newsInfo)) {
                continue;
            }
            array_push($news, $newsInfo);
        }
        
        return array(
            'total_num' => $totalNum,
            'news' => $news,
            );
    }
    
    
    /**
     * 获取推荐的达人
     * 
     * @param type $cityId 城市id
     * @param type $tagId 活动标签分类id
     * @param type $page 页数
     * @param type $size 每页活动数
     * @param type $currUid 当前用户id
     */
    public function getRecommendUsers($cityId, $tagId, $page, $size, $currUid = NULL)
    {
        $kvArr = array();
        $kvArr['key'] = 'citytagrecommendusers';
        $kvArr['cityid'] = $cityId;
        $kvArr['tagid'] = $tagId;
        //根据标签搜索活动列表key
        $key = ArrTool::implodeColonArr($kvArr);
        $model = $this->findK($key);
        
        if (empty($model)) {
            return array(
                'total_num' => 0,
                'users' => array(),
            );
        }
        $ids = json_decode($model->val);
        $totalNum = count($ids);
        //按分页取出需要的id
        $needIds = ArrTool::sliceByPageAndSize($ids, $page, $size);
        
        $users = array();
        foreach ($needIds as $v) {
            $user = UserInfo::model()->profile(NULL, $v, $cityId, $currUid, NULL, TRUE, FALSE);
            if (empty($user)) {
                continue;
            }
            array_push($users, $user);
        }
        
        return array(
            'total_num' => $totalNum,
            'users' => $users,
        );
    }
    
    
    /**
     * 获取城市达人随机动态
     * 
     * @param type $cityId 城市id
     * @param type $page 页数
     * @param type $size 每页活动数
     */
    public function getCityVipRandomDynamics($cityId, $page ,$size, $uid)
    {
        $kvArr = array();
        $kvArr['key'] = 'cityviprandomdynamics';
        $kvArr['cityid'] = $cityId;
        $index = rand(0, ConstKeyVal::CITY_VIP_RANDOM_DYNAMIC_MAX - 1);
        $kvArr['index'] = $index;
        
        //根据标签搜索列表key
        $key = ArrTool::implodeColonArr($kvArr);
        $model = $this->findK($key);
        
        if (empty($model)) {
            return array(
                'total_num' => 0,
                'news' => array(),
            );
        }
        $ids = json_decode($model->val);
        $totalNum = count($ids);
        //按分页取出需要的轮播id
        $needIds = ArrTool::sliceByPageAndSize($ids, $page, $size);
        
        $dynamics = array();
        foreach ($needIds as $v) {
            $dynamic = UserDynamic::model()->dynamic($v);
            if (empty($dynamic)) {
                continue;
            }
            $dynamic['user'] = UserInfo::model()->profile(NULL, $dynamic['author_id'], $cityId, $uid, NULL, FALSE);
            array_push($dynamics, $dynamic);
        }
        
        return array(
            'total_num' => $totalNum,
            'dynamics' => $dynamics,
            );
    }
    
    
    /**
     * 更新城市达人随机动态
     * 
     * @param type $cityId 城市id
     * @param type $dynamicIds 动态id数组
     */
    public function upCityVipRandomDynamics($cityId, $index, array $dynamicIds)
    {
        $kvArr = array();
        $kvArr['key'] = 'cityviprandomdynamics';
        $kvArr['cityid'] = $cityId;
        $kvArr['index'] = $index;
        //根据标签搜索列表key
        $key = ArrTool::implodeColonArr($kvArr);
        $model = $this->findK($key);
        
        if (empty($model)) {
            return $this->insKv($key, $dynamicIds);
        }
        
        $idsStr = json_encode(ArrTool::toNumArr($dynamicIds));
        if (!empty($model->val) && $idsStr == $model->val) {
            return TRUE;
        }
        
        $model->val = $idsStr;
        $model->update_time = date('Y-m-d H:i:s');
        return $model->update();
    }
    
}
