<?php

class ActInfoM extends ActInfo {
    
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
     * 基本资料
     * 
     * @param type $model 活动数据
     * @param type $actId 活动id
     */
    public function profileM($model = NULL, $actId = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($actId);
        }
        if (empty($model)) {
            return NULL;
        }
        $act = array();
        $act['id'] = $model->id;
        $act['title'] = $model->title;
        $act['t_status'] = $model->t_status;
        $act['status'] = $model->status;
        $act['publish_time'] = $model->publish_time;
        $act['qr_code_str'] = Yii::app()->qrCode->makeQrJson('act_id', $model->id);
        
        $tag = ActTagM::model()->profileM(NULL, $model->tag_id);
        if (!empty($tag)) {
            $act['tag_name'] = $tag['name'];
        }
        $act['shared_num'] = ActShareM::model()->shareNumM($model->id);
        $act['loved_num'] = ActLovUserMapM::model()->lovNumM($model->id);
        $act['enroll_num'] = ActEnrollM::model()->enrollNumM($model->id);
        $act['checkin_num'] = ActCheckinM::model()->checkinNumM($model->id);
        $act['comment_num'] = ActCommentM::model()->commentNumM($model->id);
        return $act;
    }

    
    /**
     * 完整资料
     * 
     * @param type $model 活动数据
     * @param type $actId 活动id
     */
    public function fullProfileM($model = NULL, $actId = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($actId);
        }
        if (empty($model)) {
            return NULL;
        }
        
        $weekRules = NULL;
        if (1 == $model->t_status_rule) {
            $timeRuleM = ActTimeStatusRule::model()->findWeek($model->id);
            $weekRules = (empty($timeRuleM) || empty($timeRuleM->filter)) ? NULL : json_encode($timeRuleM->filter);
        }
        
        $act = array();
        $act['id'] = $model->id;
        $act['title'] = $model->title;
        $act['intro'] = $model->intro;
        $act['cost'] = $model->cost;
        $act['lon'] = $model->lon;
        $act['lat'] = $model->lat;
        $act['addr_city'] = $model->addr_city;
        $act['addr_area'] = $model->addr_area;
        $act['addr_road'] = $model->addr_road;
        $act['addr_num'] = $model->addr_num;
        $act['addr_name'] = $model->addr_name;
        $act['addr_route'] = $model->addr_route;
        $act['contact_way'] = $model->contact_way;
        $act['b_time'] = $model->b_time;
        $act['e_time'] = $model->e_time;
        $act['t_status'] = $model->t_status;
        $act['t_status_rule'] = $model->t_status_rule;
        $act['week_rules'] = $weekRules;
        $act['detail'] = $model->detail;
        $act['share_url'] = Yii::app()->webPage->getViewUrl('act/activity/shareweb', array('actId' => $model->id));
        //$act['head_img_url'] = ActHeadImgMap::model()->getCurImgUrl($model->id);
        if (!empty($model->h_img_id)) {
            $img = ImgInfo::model()->profile($model->h_img_id);
            if (!empty($img)) {
                $act['head_img_url'] = $img['img_url'];
            }
        }
        $act['can_enroll'] = $model->can_enroll;
        $act['status'] = $model->status;
        $act['publish_time'] = $model->publish_time;
        $act['qr_code_str'] = Yii::app()->qrCode->makeQrJson('act_id', $model->id);
        
        $tag = ActTagM::model()->profileM(NULL, $model->tag_id);
        if (!empty($tag)) {
            $act['tag_name'] = $tag['name'];
        }
        $act['shared_num'] = ActShareM::model()->shareNumM($model->id);
        $act['loved_num'] = ActLovUserMapM::model()->lovNumM($model->id);
        $act['enroll_num'] = ActEnrollM::model()->enrollNumM($model->id);
        $act['checkin_num'] = ActCheckinM::model()->checkinNumM($model->id);
        $act['comment_num'] = ActCommentM::model()->commentNumM($model->id);
        $act['act_imgs'] = ActImgMap::model()->getImgs($model->id);
        return $act;
    }
    

    /**
     * 活动列表
     * 
     * @param type $cityId 城市id
     * @param type $tagId 分类id
     * @param type $keyWords 关键字
     * @param type $timeStatus 时间状态
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function actsM($cityId, $tagId, $keyWords = NULL, $timeStatus = NULL, $page, $size, $actIds = NULL) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.city_id', $cityId);
        $cr->compare('t.tag_id', $tagId);
        $cr->compare('t.status', '<>' . ConstStatus::DELETE);
        if (!empty($actIds)) {
            $cr->compare('t.id', $actIds);
        }
        if (!empty($keyWords)) {
            $cr->compare('t.title', $keyWords, TRUE);
        }
        if (!empty($timeStatus)) {
            $cr->compare('t.t_status', $timeStatus);
        }
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $acts = array();
        foreach ($rst as $v) {
            $act = $this->profileM($v);
            if (empty($act)) {
                continue;
            }
            array_push($acts, $act);
        }
        
        return array(
            'total_num' => $count,
            'acts' => $acts,
        );
    }
 
    
    /**
     * 添加活动
     * 
     * @param type $model 活动数据
     * @param type $weekRules 按周定时规则
     * @param type $imgIds 图片id
     */
    public function addM($model = NULL, $weekRules = NULL, $imgIds = NULL)
    {
        //验证按周定时规则格式
        if (1 == $model->t_status_rule) {
            $bW = date('w', strtotime($model->b_time));
            $eW = date('w', strtotime($model->e_time));
            if (empty($weekRules) || !in_array($bW, $weekRules) || !in_array($eW, $weekRules)) {
                return FALSE;
            }
        }
        
        if (empty($model)) {
            return FALSE;
        }
        
        $model->create_time = date('Y-m-d H:i:s', time());
        $model->update_time = date('Y-m-d H:i:s', time());
        $model->status = ConstActStatus::NOT_COMMIT;
        $r = $model->save(); 
        if (!$r) {
            return FALSE;
        }
        
        //设置活动图片
        if (!empty($imgIds)) {
            ActImgMap::model()->setActImgs($imgIds, $model->id);
        }
        
        //设置活动按周定时规则
        if (1 == $model->t_status_rule) {
            $rstArr = ArrTool::uniqueAscStr($weekRules);
            ActTimeStatusRule::model()->addWeek($model->id, $rstArr);
        }  else {
            ActTimeStatusRule::model()->delWeek($model->id);
        }
        
        return TRUE;
    }
    
    
    /**
     * 修改活动
     * 
     * @param type $model 活动数据
     * @param type $weekRules 按周定时规则
     * @param type $imgIds 图片id
     */
    public function updateM($model = NULL, $weekRules = NULL, $imgIds = NULL)
    {
        //验证按周定时规则格式
        if (1 == $model->t_status_rule) {
            $bW = date('w', strtotime($model->b_time));
            $eW = date('w', strtotime($model->e_time));
            if (empty($weekRules) || !in_array($bW, $weekRules) || !in_array($eW, $weekRules)) {
                return FALSE;
            }
        }
        
        if (empty($model)) {
            return FALSE;
        }
        
        $model->update_time = date('Y-m-d H:i:s', time());
        $r = $model->update(); 
        if (!$r) {
            return FALSE;
        }
        
        //设置活动图片
        if (!empty($imgIds)) {
            ActImgMap::model()->setActImgs($imgIds, $model->id);
        }
        
        //设置活动按周定时规则
        if (1 == $model->t_status_rule) {
            $rstArr = ArrTool::uniqueAscStr($weekRules);
            ActTimeStatusRule::model()->addWeek($model->id, $rstArr);
        }  else {
            ActTimeStatusRule::model()->delWeek($model->id);
        }
        
        $this->refreshTimeStatus(array($model->id));
        
        return TRUE;
    }
    
    
    /**
     * 修改活动状态
     * 
     * @param type $model 活动数据
     * @param type $actId 活动id
     * @param type $status 活动状态
     */
    public function upActStatusM($model = NULL, $actId = NULL, $status = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($actId);
        }
        if (empty($model)) {
            return FALSE;
        }
        if (!empty($status)) {
            $model->status = $status;
        }
        if (ConstActStatus::PUBLISHING == $model->status) {
            $model->publish_time = date('Y-m-d H:i:s');
        }
        $r = $model->update();

        $this->refreshTimeStatus(array($model->id));
        
        return $r;
    }
    
}

?>
