<?php

/**
 * This is the model class for table "act_business_map".
 *
 * The followings are the available columns in table 'act_business_map':
 * @property string $id
 * @property string $act_id
 * @property string $b_id
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property ActInfo $act
 * @property BusinessInfo $b
 */
class ActBusinessMap extends BusinessModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_business_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('act_id, b_id', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('act_id, b_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, act_id, b_id, status', 'safe', 'on'=>'search'),
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
			//'b' => array(self::BELONGS_TO, 'BusinessInfo', 'b_id'),
            
            'fkAct' => array(self::BELONGS_TO, 'ActInfo', 'act_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动所属商家关联id',
			'act_id' => '活动id',
			'b_id' => '商家id',
			'status' => '状态',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActBusinessMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 是否存在商家活动关系
     * @param type $actId
     * @param type $bid
     * @return boolean
     */
    public function isExist($actId, $bid)
    {
        $model = $this->find('act_id=:actId and b_id=:bid',
                array(
                    ':actId' => $actId,
                    ':bid' => $bid
                ));
        if (empty($model) || ConstStatus::DELETE == $model->status) {
            return FALSE;
        }
        return TRUE;
    }


    /**
     * 获取商家的活动
     * @param type $bid
     */
    public function getActs($bid, $tStatus, $keyWords)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.b_id', $bid);
        $cr->compare('t.status', '<>' . ConstStatus::DELETE);
        $cr->with = array('fkAct', 'fkAct.fkULov', 'fkAct.fkUShare');
        $cr->compare('fkAct.status', '<>' . ConstActStatus::DELETE);
        if (!empty($tStatus)) {
            $cr->compare('fkAct.t_status', $tStatus);
        }
        if (!empty($keyWords)) {
            $crs = new CDbCriteria();
            $crs->compare('fkAct.title', $keyWords, TRUE, 'OR');
            $crs->compare('fkAct.intro', $keyWords, TRUE, 'OR');
            $crs->compare('fkAct.detail', $keyWords, TRUE, 'OR');
            $cr->mergeWith($crs);
        }
        $cr->order = 'fkAct.id desc';
        $rst = $this->findAll($cr);
        
        $acts = array();
        foreach ($rst as $k => $v) {
            $act = array();
            $act['id'] = $v->fkAct->id;
            $act['title'] = $v->fkAct->title;
            $act['create_time'] = $v->fkAct->create_time;
            $act['publish_time'] = $v->fkAct->publish_time;
            $act['t_status'] = $v->fkAct->t_status;
            $act['status'] = $v->fkAct->status;
            
            $act['loved_num'] = empty($v->fkAct->fkULov) ? 0 : count($v->fkAct->fkULov);
            $act['lov_base_num'] = $v->fkAct->lov_base_num;
            $act['shared_num'] = empty($v->fkAct->fkUShare) ? 0 : count($v->fkAct->fkUShare);
            $act['share_base_num'] = $v->fkAct->share_base_num;
            
            $act['qr_code_str'] = Yii::app()->qrCode->makeQrJson('act_id', $v->act_id);
            array_push($acts, $act);
        }
        return $acts;
    }
    
    
    /**
     * 获取商家活动的详情
     * @param type $actId
     */
    public function getAct($actId)
    {
        $act = ActInfo::model()->findByPk($actId);
        $weekRules = NULL;
        if (1 == $act->t_status_rule) {
            $model = ActTimeStatusRule::model()->findWeek($act->id);
            $weekRules = empty($model) ? json_encode($model->filter) : NULL;
        }
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
            't_status' => $act->t_status,
            't_status_rule' => $act->t_status_rule,
            'week_rules' => $weekRules,
            'detail' => $act->detail,
            'detail_all' => $act->detail_all,
            'detail_url' => Yii::app()->webPage->getViewUrl('act/actInfo/viewDetailAll', array('actId' => $act->id)),
            //'head_img_url' => ActHeadImgMap::model()->getCurImgUrl($act->id),
            //'head_img_url' => $act->h_img_id,
            'can_enroll' => $act->can_enroll,
            'status' => $act->status,
            'loved_num' => ActLovUserMap::model()->getLovedNum($act->id),
            'lov_base_num' => $act->lov_base_num,
            'shared_num' => ActShare::model()->sharedNum($act->id),
            'share_base_num' => $act->share_base_num,
            'act_tags' => ActTagMap::model()->getTags($act->id),
            'act_imgs' => ActImgMap::model()->getImgs($act->id),
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
     * 修改活动相关信息
     * @param type $actModel
     * @param type $tagIds
     * @param type $imgIds
     */
    public function updateAct($actModel, $tagIds, $imgIds)
    {
        $actModel->status = ConstActStatus::NOT_COMMIT;
        
        ActInfo::model()->updateAct($actModel);
        
        if (isset($tagIds)) {
            ActTagMap::model()->setActTags($tagIds, $actModel->id);
        }
        
        if (!empty($imgIds)) {
            ActImgMap::model()->setActImgs($imgIds, $actModel->id);
        }
        return TRUE;
    }
    
    
    /**
     * 添加活动及相关信息
     * @param type $actModel
     * @param type $tagIds
     * @param type $imgIds
     */
    public function addAct($actModel, $tagIds, $imgIds, $bid)
    {
        //旧版活动添加默认tag为其他5
        $actModel->tag_id = 5;
        if (!ActInfo::model()->addAct($actModel)) {
            return FALSE;
        }
        
        if (isset($tagIds)) {
            ActTagMap::model()->setActTags($tagIds, $actModel->id);
        }
        
        if (!empty($imgIds)) {
            ActImgMap::model()->setActImgs($imgIds, $actModel->id);
        }
        
        return $this->insertOrganizer($actModel->id, $bid);
    }
    
    
    /**
     * 获取商家的活动及其签到信息
     * @param type $bid
     * @param type $keyWords
     */
    public function getActsWithCheckin($bid, $keyWords)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.b_id', $bid);
        $cr->compare('t.status', '<>' . ConstStatus::DELETE);
        $cr->with = array('fkAct', 'fkAct.fkCheckin');
        $cr->compare('fkAct.status', array(
            ConstActStatus::NOT_PUBLISH,
            ConstActStatus::PUBLISHING,
            ConstActStatus::OFF_PUBLISH,
            )
        );
        if (!empty($keyWords)) {
            $crs = new CDbCriteria();
            $crs->compare('fkAct.title', $keyWords, TRUE, 'OR');
            $crs->compare('fkAct.intro', $keyWords, TRUE, 'OR');
            $crs->compare('fkAct.detail', $keyWords, TRUE, 'OR');
            $cr->mergeWith($crs);
        }
        $cr->order = 't.id desc';
        $rst = $this->findAll($cr);
        
        $acts = array();
        foreach ($rst as $k => $v) {
            $act = array();
            $act['id'] = $v->fkAct->id;
            $act['title'] = $v->fkAct->title;
            $act['publish_time'] = $v->fkAct->publish_time;
            //签到数量
            $act['checkin_num'] = empty($v->fkAct->fkCheckin) ? 0 : count($v->fkAct->fkCheckin);
            $act['t_status'] = $v->fkAct->t_status;
            $act['status'] = $v->fkAct->status;
            array_push($acts, $act);
        }
        return $acts;
    }
    
    
    /**
     * 获取活动的组织者
     * @param type $actId
     */
    public function getOrganizer($actId)
    {
        $model = $this->find('act_id=:actId', array('actId' => $actId));
        if (empty($model)) {
            return NULL;
        }
        $busi = BusinessInfo::model()->findByPk($model->b_id);
        if (empty($busi)) {
            return NULL;
        }
        return $busi->name;
    }
    
    
    /**
     * 插入商家与活动归属关系
     * @param type $actId
     * @param type $bid
     * @return boolean
     */
    public function insertOrganizer($actId, $bid)
    {
        $model = new ActBusinessMap();
        $model->act_id = $actId;
        $model->b_id = $bid;
        $model->status = ConstStatus::NORMAL;
        return $model->save();
    }
       
    
}
