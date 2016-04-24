<?php

class ActInfoAdmin extends ActInfo {
    
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
     * 获取商家活动的详情
     * @param type $actId
     */
    public function getActInfo($actId)
    {
        $act = $this->findByPk($actId);
        $weekRules = NULL;
        if (1 == $act->t_status_rule) {
            $model = ActTimeStatusRule::model()->findWeek($act->id);
            $weekRules = (empty($model) || empty($model->filter)) ? NULL : json_encode($model->filter);
        }
        return array(
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
            'head_img_url' => ActHeadImgMap::model()->getCurImgUrl($act->id),
            'can_enroll' => $act->can_enroll,
            'status' => $act->status,
            'loved_num' => ActLovUserMap::model()->getLovedNum($act->id),
            'lov_base_num' => $act->lov_base_num,
            'shared_num' => ActShare::model()->sharedNum($act->id),
            'share_base_num' => $act->share_base_num,
            'act_tags' => ActTagMap::model()->getTags($act->id),
            'act_imgs' => ActImgMap::model()->getImgs($act->id),
        );
    }
    
    
    /**
     * 搜索活动列表
     * @param type $tStatus
     * @param type $status
     * @param type $keyWords
     * @param type $page
     * @param type $size
     * @param type $isDel
     */
    public function searchActs($tStatus, $status, $keyWords, $page, $size, $isDel = FALSE)
    {
        $cr = new CDbCriteria();
        if (!empty($tStatus)) {
            $cr->compare('t.t_status', $tStatus);
        }
        if ($isDel) {
            $cr->compare('t.status', ConstStatus::DELETE);
        }  else {
            if (!empty($status)) {
                $cr->compare('t.status', $status);
            }  else {
                $cr->compare('t.status', '<>' . ConstStatus::DELETE);
            }
        }
        if (!empty($keyWords)) {
            $crs = new CDbCriteria();
            $crs->compare('t.title', $keyWords, TRUE, 'OR');
            $crs->compare('t.intro', $keyWords, TRUE, 'OR');
            $crs->compare('t.detail', $keyWords, TRUE, 'OR');
            $cr->mergeWith($crs);
        }
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $cr->with = array('fkULov', 'fkUShare');
        //$cr->with = array('fkHeadImg.fkImg', 'fkULov', 'fkUShare', 'fkTags.fkTag');
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $rst = $this->findAll($cr);
        
        $acts = array();
        foreach ($rst as $v) {
            $act = array();
            $act['id'] = $v->id;
            $act['title'] = $v->title;
            $act['create_time'] = $v->create_time;
            $act['publish_time'] = $v->publish_time;
            $act['t_status'] = $v->t_status;
            $act['status'] = $v->status;
            
            $act['loved_num'] = empty($v->fkULov) ? 0 : count($v->fkULov);
            $act['lov_base_num'] = $v->lov_base_num;
            $act['shared_num'] = empty($v->fkUShare) ? 0 : count($v->fkUShare);
            $act['share_base_num'] = $v->share_base_num;
            
            $act['qr_code_str'] = Yii::app()->qrCode->makeQrJson('act_id', $v->id);
            array_push($acts, $act);
        }
        return array(
            'total_num' => $count,
            'acts' => $acts,
        );
    }
    
    
    /**
     * 获取商家的活动及其签到信息
     * @param type $bid
     * @param type $keyWords
     */
    public function getActsWithCheckin($keyWords, $page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.status', '<>' . ConstStatus::DELETE);
        $cr->with = array('fkCheckin');
        $cr->compare('t.status', array(
            ConstActStatus::NOT_PUBLISH,
            ConstActStatus::PUBLISHING,
            ConstActStatus::OFF_PUBLISH,
            )
        );
        if (!empty($keyWords)) {
            $crs = new CDbCriteria();
            $crs->compare('t.title', $keyWords, TRUE, 'OR');
            $crs->compare('t.intro', $keyWords, TRUE, 'OR');
            $crs->compare('t.detail', $keyWords, TRUE, 'OR');
            $cr->mergeWith($crs);
        }
        $count = $this->count($cr);
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $cr->order = 't.id desc';
        $rst = $this->findAll($cr);
        
        $acts = array();
        foreach ($rst as $k => $v) {
            $act = array();
            $act['id'] = $v->id;
            $act['title'] = $v->title;
            $act['publish_time'] = $v->publish_time;
            //签到数量
            $act['checkin_num'] = empty($v->fkCheckin) ? 0 : count($v->fkCheckin);
            $act['t_status'] = $v->t_status;
            $act['status'] = $v->status;
            array_push($acts, $act);
        }
        return array(
            'total_num' => $count,
            'acts' => $acts,
        );
    }
    
    
    /**
     * 刷新各增长参数的基数（增长率10-30%）
     */
    public function refreshBaseGrowNums() 
    {
        $cr = new CDbCriteria();
        $cr->compare('status', ConstActStatus::PUBLISHING);
        $crs = new CDbCriteria();
        $crs->compare('lov_base_num', '>' . 0, FALSE, 'OR');
        $crs->compare('share_base_num', '>' . 0, FALSE, 'OR');
        $cr->mergeWith($crs);
        $rst = $this->findAll($cr);
        
        foreach ($rst as $v) {
            $rate = rand(10, 30);
            if ($v->lov_base_num > 0) {
                $last = $v->lov_base_num;
                $v->lov_base_num = $v->lov_base_num * (1 + $rate / 100.0);
                $v->update();
                echo 'act id ' . $v->id . ' \'s lov_base_num is rised ' . $rate / 100.0 . '% (from ' . $last . ' to ' . $v->lov_base_num . ') time at ' . date('Y-m-d H:i:s');
            }
            if ($v->share_base_num > 0) {
                $last = $v->share_base_num;
                $v->share_base_num = $v->share_base_num * (1 + $rate / 100.0);
                $v->update();
                echo 'act id ' . $v->id . ' \'s share_base_num is rised ' . $rate / 100.0 . '% (from ' . $last . ' to ' . $v->share_base_num . ') time at ' . date('Y-m-d H:i:s');
            }
        }
    }
    
}

?>
