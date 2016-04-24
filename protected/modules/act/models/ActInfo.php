<?php

/**
 * This is the model class for table "act_info".
 *
 * The followings are the available columns in table 'act_info':
 * @property string $id
 * @property string $title
 * @property string $intro
 * @property string $city_id
 * @property string $tag_id
 * @property string $org_id
 * @property double $cost
 * @property double $lon
 * @property double $lat
 * @property string $addr_city
 * @property string $addr_area
 * @property string $addr_road
 * @property string $addr_num
 * @property string $addr_name
 * @property string $addr_route
 * @property string $contact_way
 * @property string $b_time
 * @property string $e_time
 * @property integer $t_status
 * @property integer $t_status_rule
 * @property string $detail
 * @property string $detail_all
 * @property integer $can_enroll
 * @property string $h_img_id
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 * @property string $publish_time
 * @property string $lov_base_num
 * @property string $share_base_num
 *
 * The followings are the available model relations:
 * @property ActAlbum[] $actAlbums
 * @property ActAttention[] $actAttentions
 * @property ActBusinessMap[] $actBusinessMaps
 * @property ActCheckin[] $actCheckins
 * @property ActCheckinStep[] $actCheckinSteps
 * @property ActComment[] $actComments
 * @property ActEnroll[] $actEnrolls
 * @property ActGroup[] $actGroups
 * @property ActHeadImgMap[] $actHeadImgMaps
 * @property ActImgMap[] $actImgMaps
 * @property OrgInfo $org
 * @property CityInfo $city
 * @property ImgInfo $hImg
 * @property ActTag $tag
 * @property ActInfoBaiduSynchroTask[] $actInfoBaiduSynchroTasks
 * @property ActInfoExtend[] $actInfoExtends
 * @property ActLeaveMsg[] $actLeaveMsgs
 * @property ActLocation[] $actLocations
 * @property ActLovUserMap[] $actLovUserMaps
 * @property ActManagerMap[] $actManagerMaps
 * @property ActMenu[] $actMenus
 * @property ActNewsMap[] $actNewsMaps
 * @property ActNotice[] $actNotices
 * @property ActPlaceImg[] $actPlaceImgs
 * @property ActPrizeMap[] $actPrizeMaps
 * @property ActProcess[] $actProcesses
 * @property ActShare[] $actShares
 * @property ActTagMap[] $actTagMaps
 * @property ActTimeStatusRule[] $actTimeStatusRules
 * @property ActVipMap[] $actVipMaps
 * @property CustomExtActMap[] $customExtActMaps
 * @property CustomExtActUserVal[] $customExtActUserVals
 * @property ManagerActRemark[] $managerActRemarks
 * @property NewsActMap[] $newsActMaps
 */
class ActInfo extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, city_id, addr_city, b_time, e_time, create_time, update_time', 'required'),
			array('t_status, t_status_rule, can_enroll, status', 'numerical', 'integerOnly'=>true),
			array('cost, lon, lat', 'numerical'),
			array('title', 'length', 'max'=>128),
			array('intro', 'length', 'max'=>512),
			array('city_id, tag_id, org_id, h_img_id, lov_base_num, share_base_num', 'length', 'max'=>10),
			array('addr_city, addr_area, addr_road, addr_name', 'length', 'max'=>24),
			array('addr_num, contact_way', 'length', 'max'=>48),
			array('addr_route', 'length', 'max'=>240),
			array('detail, detail_all, publish_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, intro, city_id, tag_id, org_id, cost, lon, lat, addr_city, addr_area, addr_road, addr_num, addr_name, addr_route, contact_way, b_time, e_time, t_status, t_status_rule, detail, detail_all, can_enroll, h_img_id, status, create_time, update_time, publish_time, lov_base_num, share_base_num', 'safe', 'on'=>'search'),
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
			//'actBusinessMaps' => array(self::HAS_MANY, 'ActBusinessMap', 'act_id'),
			//'actCheckins' => array(self::HAS_MANY, 'ActCheckin', 'act_id'),
			//'actHeadImgMaps' => array(self::HAS_MANY, 'ActHeadImgMap', 'act_id'),
			//'actImgMaps' => array(self::HAS_MANY, 'ActImgMap', 'act_id'),
			//'actLovUserMaps' => array(self::HAS_MANY, 'ActLovUserMap', 'act_id'),
            
            //'fkHeadImg' => array(self::HAS_ONE, 'ActHeadImgMap', 'act_id', 'on' => 'fkHeadImg.status=1'),
            'fkULov' => array(self::HAS_MANY, 'ActLovUserMap', 'act_id'),
            'fkUShare' => array(self::HAS_MANY, 'ActShare', 'act_id', 'select' => 'fkUShare.id'),
            'fkTags' => array(self::HAS_MANY, 'ActTagMap', 'act_id', 'on' => 'fkTags.status=0', 'limit' => 2),
            'fkCheckin' => array(self::HAS_MANY, 'ActCheckin', 'act_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动id[自增]',
			'title' => '标题',
			'intro' => '活动简述（详情内标题）',
			'city_id' => '城市id',
			'tag_id' => '标签id',
			'org_id' => '社团组织id',
			'cost' => '花费（0免费）',
			'lon' => '经度',
			'lat' => '纬度',
			'addr_city' => '地址（城市）',
			'addr_area' => '地址（区）',
			'addr_road' => '地址（路）',
			'addr_num' => '地址（号）',
			'addr_name' => '地址（名称）',
			'addr_route' => '地址（路线）',
			'contact_way' => '联系方式',
			'b_time' => '开始时间',
			'e_time' => '结束时间',
			't_status' => '时间状态：1即将开始，2进行中，3筹备中，4已结束',
			't_status_rule' => '时间状态规则',
			'detail' => '活动详情',
			'detail_all' => '活动图文详情',
			'can_enroll' => '是否可以报名',
			'h_img_id' => '首图id',
			'status' => '状态：-1删除，0正常',
			'create_time' => '创建时间',
			'update_time' => '更新时间',
			'publish_time' => '发布时间',
			'lov_base_num' => '感兴趣数的基数',
			'share_base_num' => '分享数的基数',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    

    /**
     * 基本信息
     * 
     * @param type $model 活动数据
     * @param type $actId 活动id
     * @param type $uid 用户id
     */
    public function profile($model = NULL, $actId, $uid = NULL, $needCheckin = FALSE)
    {
        if (empty($model)) {
            $model = $this->findByPk($actId);
        }
        if (empty($model) || ConstActStatus::PUBLISHING != $model->status) {
            return NULL;
        }
        $rst =  array(
            'id' => $model->id,
            'title' => $model->title,
            'intro' => $model->intro,
            'cost' => $model->cost,
            'lon' => $model->lon,
            'lat' => $model->lat,
            'addr_city' => $model->addr_city,
            'addr_area' => $model->addr_area,
            'addr_road' => $model->addr_road,
            'addr_num' => $model->addr_num,
            'addr_name' => $model->addr_name,
            'b_time' => $model->b_time,
            //'b_time' => (!empty($model->b_time) && (strtotime($model->b_time) > time())) ? $model->b_time : date('Y-m-d H:i:s'),
            'e_time' => $model->e_time,
            //'t_status' => $model->t_status,
            't_status' => (empty($model->e_time) || time() > strtotime($model->e_time)) ? ConstActTimeStatus::OVER : ConstActTimeStatus::BEGINNING,
            //'head_img_url' => ActHeadImgMap::model()->getCurImgUrl($model->id),
            //'head_img_url' => $model->h_img_id,
            'is_loved' => empty($uid) ? 0 : ActLovUserMap::model()->isLoved($model->id, $uid),
        );
        if (!empty($model->h_img_id)) {
            $img = ImgInfo::model()->profile($model->h_img_id);
            if (!empty($img)) {
                $rst['head_img_url'] = $img['img_url'];
            }
        }
        if ($needCheckin && !empty($uid)) {
            $rst['is_checkin'] = ActCheckin::model()->check($model->id, $uid);
        }
        return $rst;
    }


    /**
     * 完整信息
     * 
     * @param type $model 活动数据
     * @param type $actId 活动id
     * @param type $uid 用户id
     */
    public function fullProfile($model = NULL, $actId, $uid = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($actId);
        }
        if (empty($model) || ConstActStatus::PUBLISHING != $model->status) {
            return NULL;
        }
        //兼容2.0版后台发布的只展示价格不支付的活动
        $cost = empty($model->org_id) ? $model->cost : 0;
        //2.1以上版本发布的带支付的活动
        $productId = ActInfoExtend::model()->productId($model->id);
        if (!empty($productId)) {
            $cost = Product::model()->price($productId);
        }
        $rst =  array(
            'id' => $model->id,
            'title' => $model->title,
            'intro' => $model->intro,
            'cost' => $cost,
            'lon' => $model->lon,
            'lat' => $model->lat,
            'addr_city' => $model->addr_city,
            'addr_area' => $model->addr_area,
            'addr_road' => $model->addr_road,
            'addr_num' => $model->addr_num,
            'addr_name' => $model->addr_name,
            'addr_route' => $model->addr_route,
            'contact_way' => $model->contact_way,
            'b_time' => $model->b_time,
            'e_time' => $model->e_time,
            't_status' => $model->t_status,
            'detail' => $model->detail,
            'share_url' => Yii::app()->webPage->getViewUrl('act/activity/shareweb', array('actId' => $model->id)),
            'can_enroll' => $model->can_enroll,
            //'head_img_url' => ActHeadImgMap::model()->getCurImgUrl($model->id),
            //'head_img_url' => $model->h_img_id,
            'is_loved' => empty($uid) ? 0 : ActLovUserMap::model()->isLoved($model->id, $uid),
            'loved_num' => ActLovUserMap::model()->getLovedNum($model->id) + $model->lov_base_num,
            'shared_num' => ActShare::model()->sharedNum($model->id) + $model->share_base_num,
            'act_imgs' => ActImgMap::model()->getImgs($model->id),
        );
        $img = ImgInfo::model()->profile($model->h_img_id);
        $rst['head_img_url'] = empty($img) ? NULL : $img['img_url'];
        return $rst;
    }
    
    
    /**
     * 活动搜索
     * 
     * @param type $cityId 城市id
     * @param type $tagId 标签id
     * @param type $startTime 时间起点
     * @param type $endTime 时间终点
     * @param type $keyWords 关键字
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function acts($cityId = NULL, $tagId = NULL, $startTime = NULL, $endTime = NULL, $keyWords = NULL, $page = NULL, $size = NULL, $uid = NULL, array $actIds = NULL) 
    {
        $cr = new CDbCriteria();
        if (!empty($actIds)) {
            $cr->compare('t.id', $actIds);
        }
        if (!empty($cityId)) {
            $cr->compare('t.city_id', $cityId);
        }
        if (!empty($tagId)) {
            $cr->compare('t.tag_id', $tagId);
        }
        $cr->compare('t.status', ConstActStatus::PUBLISHING);
        $cr->compare('t.t_status', '<>' . ConstActTimeStatus::OVER);
        
        if (!empty($startTime) && !empty($endTime)) {
            $cr->addBetweenCondition('t.b_time', $startTime, $endTime);
            //$crTime = new CDbCriteria();
            //$crTime->addBetweenCondition('t.b_time', $startTime, $endTime, 'OR');
            //$crTime->addBetweenCondition('t.e_time', $startTime, $endTime, 'OR');
            //$cr->mergeWith($crTime);
        }
        
        if (!empty($keyWords)) {
            $cr->compare('t.title', $keyWords, TRUE);
        }
        
        if (!empty($page)) {
            $count = $this->count($cr);
            //$cr->order = 't.publish_time desc';
            $cr->order = 't.b_time asc';
            $cr->offset = ($page - 1) * $size;
        }
        if (!empty($size)) {
            $cr->limit = $size;
        }
        $rst = $this->findAll($cr);
        
        $acts = array();
        foreach ($rst as $v) {
            $act = $this->profile($v, NULL, $uid, FALSE);
            array_push($acts, $act);
        }
        
        if (empty($page)) {
            return $acts;
        }  else {
            return array(
                'total_num' => $count,
                'acts' => $acts
            );
        }
    }


    /**
     * 获取活动的信息
     * @param type $id
     * @return type
     */
    public function getAct($actId) 
    {
        $act = $this->findByPk($actId);
        $rst = array(
            'id' => $act->id,
            'title' => $act->title,
            'intro' => $act->intro,
            'lon' => $act->lon,
            'lat' => $act->lat,
            'addr_city' => $act->addr_city,
            'addr_area' => $act->addr_area,
            'addr_road' => $act->addr_road,
            'addr_num' => $act->addr_num,
            'addr_route' => $act->addr_route,
            'contact_way' => $act->contact_way,
            'b_time' => $act->b_time,
            'e_time' => $act->e_time,
            'organizer' => ActBusinessMap::model()->getOrganizer($act->id),
            't_status' => $act->t_status,
            'status' => $act->status,
            'detail' => $act->detail,
            'detail_url' => Yii::app()->webPage->getViewUrl('act/actInfo/viewDetailAll', array('actId' => $act->id)),
            'can_enroll' => $act->can_enroll,
            //'head_img_url' => ActHeadImgMap::model()->getCurImgUrl($act->id),
            //'head_img_url' => $act->h_img_id,
            'is_loved' => Yii::app()->user->isGuest ? 0 : ActLovUserMap::model()->isLoved($act->id, Yii::app()->user->id),
            'loved_num' => ActLovUserMap::model()->getLovedNum($act->id) + $act->lov_base_num,
            'shared_num' => ActShare::model()->sharedNum($act->id) + $act->share_base_num,
            'act_tags' => ActTagMap::model()->getTags($act->id),
            //'act_routes' => ActRoute::model()->getActRoutesBasic($actId),
        );
        if (!empty($act->h_img_id)) {
            $img = ImgInfo::model()->profile($act->h_img_id);
            if (!empty($img)) {
                $rst['head_img_url'] = $img['img_url'];
            }
        }
        return $rst;
    }
    
    
    /**
     * 根据id数组获取活动
     * @param type $ids
     */
    public function getActs($ids, $uid) 
    {
        if (empty($ids)) {
            return array();
        }
        $criteria = new CDbCriteria();
        $criteria->compare('t.id', $ids);
        $criteria->compare('t.status', ConstActStatus::PUBLISHING);
        $criteria->with = array('fkULov', 'fkUShare', 'fkTags.fkTag');
        $criteria->order = 't.t_status asc, t.publish_time desc';
        $rst = $this->findAll($criteria);
        
        $acts = array();
        foreach ($rst as $k => $v) {
            $acts[$k]['id'] = $v->id;
            $acts[$k]['title'] = $v->title;
            $acts[$k]['lon'] = $v->lon;
            $acts[$k]['lat'] = $v->lat;
            $acts[$k]['addr_city'] = $v->addr_city;
            $acts[$k]['addr_area'] = $v->addr_area;
            $acts[$k]['addr_road'] = $v->addr_road;
            $acts[$k]['addr_num'] = $v->addr_num;
            $acts[$k]['b_time'] = $v->b_time;
            $acts[$k]['e_time'] = $v->e_time;
            $acts[$k]['t_status'] = $v->t_status;
            $acts[$k]['status'] = $v->status;
            $acts[$k]['can_enroll'] = $v->can_enroll;
            $acts[$k]['detail_url'] = Yii::app()->webPage->getViewUrl('act/actInfo/viewDetailAll', array('actId' => $v->id));
            
            if (!empty($v->h_img_id)) {
                $img = ImgInfo::model()->profile($v->h_img_id);
                if (!empty($img)) {
                    $acts[$k]['head_img_url'] = $img['img_url'];
                }
            }
            
            //if (!empty($v->fkHeadImg)) {
            //    $acts[$k]['head_img_url'] = Yii::app()->imgUpload->getDownUrl($v->fkHeadImg->fkImg->img_url);
            //}
            if (empty($uid) || empty($v->fkULov)) {
                $acts[$k]['is_loved'] = 0;
            }  else {
                $acts[$k]['is_loved'] = 0;
                foreach ($v->fkULov as $value) {
                    if ($value->u_id == $uid) {
                        $acts[$k]['is_loved'] = ($value->status == -1) ? -1 : 1;
                    }
                }
            }
            
            $acts[$k]['loved_num'] = (empty($v->fkULov) ? 0 : count($v->fkULov)) + $v->lov_base_num;
            $acts[$k]['shared_num'] = (empty($v->fkUShare) ? 0 : count($v->fkUShare)) + $v->share_base_num;
            
            $tags = $v->fkTags;
            $acts[$k]['act_tags'] = array();
            foreach ($tags as $key => $value) {
                if ($value->fkTag->status == -1) {
                    continue;
                }
                $tag = array();
                $tag['id'] = $value->fkTag->id;
                $tag['name'] = $value->fkTag->name;
                $tag['count'] = $value->fkTag->count;
                array_push($acts[$k]['act_tags'], $tag);
            }
        }
        return $acts;
    }
    
    
    /**
     * 修改活动资料
     * @param type $model
     */
    public function updateAct($model)
    {
        if (empty($model)) {
            return FALSE;
        }
        if (!in_array($model->status, array(ConstActStatus::NOT_COMMIT, 
            ConstActStatus::NOT_PASS, 
            ConstActStatus::NOT_PUBLISH,
            ConstActStatus::OFF_PUBLISH))) {
            return FALSE;
        }
        $model->update_time = date('Y-m-d H:i:s', time());
        $model->status = ConstActStatus::NOT_COMMIT;
        $r = $model->update();
        return $r;
    }
    
    
    /**
     * 添加活动
     * @param type $model
     */
    public function addAct($model)
    {
        if (empty($model)) {
            return FALSE;
        }
        $model->create_time = date('Y-m-d H:i:s');
        $model->update_time = date('Y-m-d H:i:s');
        $model->status = ConstActStatus::NOT_COMMIT;
        $r = $model->save(); 
        return $r;
    }
    
    
    /**
     * 提交活动
     * @param type $actId
     */
    public function commitAct($actId)
    {
        $model = $this->findByPk($actId);
        if ($model->status != ConstActStatus::NOT_COMMIT) {
            return FALSE;
        }
        $model->status = ConstActStatus::CHECK_WAITING;
        $r = $model->update();
        return $r;
    }
    
    
    /**
     * 发布活动
     */
    public function publishAct()
    {
        if ($this->status != ConstActStatus::NOT_PUBLISH) {
            return FALSE;
        }
        $this->status = ConstActStatus::PUBLISHING;
        $this->publish_time = date('Y-m-d H:i:s');
        $r = $this->update();
        
        $this->refreshTimeStatus(array($this->id));
        //TagInfo::model()->refreshCount();
        //IndexPageActList::model()->refreshAll();
        TimeTask::model()->addTimeTask(ConstTimeTaskType::TAG_ACT_COUNT, date('Y-m-d H:i:s'));
        TimeTask::model()->addTimeTask(ConstTimeTaskType::INDEX_PAGE_ACT_LIST, date('Y-m-d H:i:s'));
        if (time() < strtotime($this->b_time)) {
            TimeTask::model()->addTimeTask(ConstTimeTaskType::ACT_TIME_STATUS, $this->b_time);
            TimeTask::model()->addTimeTask(ConstTimeTaskType::TAG_ACT_COUNT, $this->b_time);
            TimeTask::model()->addTimeTask(ConstTimeTaskType::INDEX_PAGE_ACT_LIST, $this->b_time);
        }
        if (time() < strtotime($this->e_time)) {
            TimeTask::model()->addTimeTask(ConstTimeTaskType::ACT_TIME_STATUS, $this->e_time);
            TimeTask::model()->addTimeTask(ConstTimeTaskType::TAG_ACT_COUNT, $this->e_time);
            TimeTask::model()->addTimeTask(ConstTimeTaskType::INDEX_PAGE_ACT_LIST, $this->e_time);
        }
        return $r;
    }
    
    
    /**
     * 下架活动
     */
    public function offPublishAct()
    {
        if ($this->status != ConstActStatus::PUBLISHING) {
            return FALSE;
        }
        $this->status = ConstActStatus::OFF_PUBLISH;
        $r = $this->update();
        
        $this->refreshTimeStatus(array($this->id));
        //TagInfo::model()->refreshCount();
        //IndexPageActList::model()->refreshAll();
        TimeTask::model()->addTimeTask(ConstTimeTaskType::TAG_ACT_COUNT, date('Y-m-d H:i:s'));
        TimeTask::model()->addTimeTask(ConstTimeTaskType::INDEX_PAGE_ACT_LIST, date('Y-m-d H:i:s'));
        if (time() < strtotime($this->b_time)) {
            TimeTask::model()->addTimeTask(ConstTimeTaskType::ACT_TIME_STATUS, $this->b_time);
            TimeTask::model()->addTimeTask(ConstTimeTaskType::TAG_ACT_COUNT, $this->b_time);
            TimeTask::model()->addTimeTask(ConstTimeTaskType::INDEX_PAGE_ACT_LIST, $this->b_time);
        }
        if (time() < strtotime($this->e_time)) {
            TimeTask::model()->addTimeTask(ConstTimeTaskType::ACT_TIME_STATUS, $this->e_time);
            TimeTask::model()->addTimeTask(ConstTimeTaskType::TAG_ACT_COUNT, $this->e_time);
            TimeTask::model()->addTimeTask(ConstTimeTaskType::INDEX_PAGE_ACT_LIST, $this->e_time);
        }
        return $r;
    }
    
    
    /**
     * 更改活动状态
     * @param type $actId
     */
    public function updateStatus($status)
    {
        switch ($status) {
            case ConstActStatus::NOT_COMMIT:
            case ConstActStatus::CHECK_WAITING:
            case ConstActStatus::CHECK_WAITING:
            case ConstActStatus::CHECKING:
            case ConstActStatus::NOT_PASS:
            case ConstActStatus::NOT_PUBLISH:
                $this->status = $status;
                return $this->update();
            case ConstActStatus::PUBLISHING:
                return $this->publishAct();
            case ConstActStatus::OFF_PUBLISH:
                return $this->offPublishAct();
            default:
                return FALSE;
        }
    }
    
    
    /**
     * 删除活动
     * @param type $actId
     */
    public function delAct($actId)
    {
        $model = $this->findByPk($actId);
        if (!in_array($model->status, array(ConstActStatus::NOT_COMMIT, 
            ConstActStatus::NOT_PASS, 
            ConstActStatus::NOT_PUBLISH,
            ConstActStatus::OFF_PUBLISH))) {
            return FALSE;
        }
        $model->status = ConstActStatus::DELETE;
        $r = $model->update();
        return $r;
    }
    
    
    /**
     * 刷新活动时间状态
     */
    public function refreshTimeStatus($ids = array())
    {
        $rst = array();
        if (empty($ids)) {
            $rst = $this->findAll();
        }  else {
            $rst = $this->findAllByPk($ids);
        }
        
        //$transaction = Yii::app()->dbAct->beginTransaction();
        //try {
            foreach ($rst as $v) {
                //筹备中
                //即将开始
                //进行中
                //已结束
                if (strtotime($v->e_time) <= time()) {
                    if ($v->t_status == ConstActTimeStatus::OVER) {
                        continue;
                    }
                    $v->t_status = ConstActTimeStatus::OVER;
                } elseif (strtotime($v->b_time) <= time()) {
                    //检测是否有自定义时间状态规则(发布时会添加最近的时间节点)
                    if (0 == $v->t_status_rule) {
                        if ($v->t_status == ConstActTimeStatus::BEGINNING) {
                            continue;
                        }
                        $v->t_status = ConstActTimeStatus::BEGINNING;
                    }  else {
                        //每周重复执行（取起点时间的周几，起点时间的小时分钟，结束时间的小时分钟）
                        $weekRuleModel = ActTimeStatusRule::model()->findWeek($v->id);
                        if (empty($weekRuleModel) || empty($weekRuleModel->filter)) {
                            if ($v->t_status == ConstActTimeStatus::BEGINNING) {
                                continue;
                            }
                            $v->t_status = ConstActTimeStatus::BEGINNING;
                        }  else {
                            $weekArr = json_decode($weekRuleModel->filter);
                            $todayWeek = date('w');
                            //当前时间是否在周几之一
                            if (in_array($todayWeek, $weekArr)) {
                                //是否在当天开始时间与结束时间之间
                                $startTime = strtotime(date('Y-m-d') . ' ' . date('H:i:s', strtotime($v->b_time)));
                                $endTime = strtotime(date('Y-m-d') . ' ' . date('H:i:s', strtotime($v->e_time)));
                                if (time() >= $startTime && time() < $endTime) {
                                    if ($v->t_status == ConstActTimeStatus::BEGINNING) {
                                        continue;
                                    }
                                    $v->t_status = ConstActTimeStatus::BEGINNING;
                                }  else {
                                    if ($v->t_status == ConstActTimeStatus::TOBEGIN) {
                                        continue;
                                    }
                                    $v->t_status = ConstActTimeStatus::TOBEGIN;
                                }
                            }  else {
                                if ($v->t_status == ConstActTimeStatus::TOBEGIN) {
                                    continue;
                                }
                                $v->t_status = ConstActTimeStatus::TOBEGIN;
                            }
                        }
                    }
                }  elseif (strtotime($v->b_time) - time() <= 60 * 60 * 24 * 7) {
                    if ($v->t_status == ConstActTimeStatus::TOBEGIN) {
                        continue;
                    }
                    $v->t_status = ConstActTimeStatus::TOBEGIN;
                }  else {
                    if ($v->t_status == ConstActTimeStatus::PREPARE) {
                        continue;
                    }
                    $v->t_status = ConstActTimeStatus::PREPARE;
                }
                $v->update();
            }
            //$transaction->commit();
        //} catch (Exception $exc) {
            // echo $exc->getTraceAsString();
            //$transaction->rollBack();
        //}
    }
    
}
