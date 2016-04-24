<?php

class ActInfoO extends ActInfo {
    
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
     * 社团活动列表
     * 
     * @param type $orgId 社团id
     * @param type $isOver 是否已结束
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function actsO($orgId, $isOver = FALSE, $page, $size) 
    {
        $cr = new CDbCriteria();
        if (1 == $orgId) {
            //官方社团后台看所有数据
            if ($isOver) {
                $cr->compare('t.e_time', '<' . date('Y-m-d H:i:s'));
            }  else {
                $cr->addCondition('t.org_id is not null');
            }
        }  else {
            $cr->compare('t.org_id', $orgId);
            if ($isOver) {
                $cr->compare('t.e_time', '<' . date('Y-m-d H:i:s'));
            }  else {
                $cr->compare('t.e_time', '>' . date('Y-m-d H:i:s'));
            }
        }
        $cr->compare('t.status', '<>' . ConstActStatus::DELETE);
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        $acts = array();
        foreach ($rst as $v) {
            $act = $this->profile0(NULL, $v);
            array_push($acts, $act);
        }
        return array(
            'total_num' => $count,
            'acts' => $acts,
        );
    }
    
    
    /**
     * 创建活动
     * 
     * @param type $model
     * @param type $cityId 城市id
     * @param type $orgId 社团id
     */
    public function createActO($model, $cityId, $orgId) 
    {
        $model->city_id = $cityId;
        $model->org_id = $orgId;
        $model->intro = $model->title;
        $model->cost = 0;
        $model->status = ConstActStatus::NOT_COMMIT;
        $model->create_time = date('Y-m-d H:i:s');
        $model->update_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
    
    /**
     * 修改活动
     * 
     * @param type $model
     */
    public function modifyActO($model, $status =  ConstActStatus::NOT_COMMIT)
    {
        $model->status = $status;
        $model->update_time = date('Y-m-d H:i:s');
        return $model->update();
    }
    
    
    /**
     * 修改活动状态
     * 
     * @param type $actId 活动id
     * @param type $status 状态
     * @param type $model
     */
    public function modifyActStatus($actId, $status, $model = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($actId);
        }
        if (empty($model)) {
            return FALSE;
        }
        $model->status = $status;
        $model->update_time = date('Y-m-d H:i:s');
        if (ConstActStatus::PUBLISHING == $status) {
            $model->publish_time = date('Y-m-d H:i:s');
        }
        return $model->update();
    }
    
    
    /**
     * 基本信息
     * 
     * @param type $id 活动id
     * @param type $model
     */
    public function profile0($id, $model = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($id);
        }
        $act['id'] = $model->id;
        $act['title'] = $model->title;
        $act['h_img_id'] = empty($model->h_img_id) ? NULL : $model->h_img_id;
        $img = ImgInfo::model()->profile($model->h_img_id);
        $act['h_img_url'] = empty($img) ? NULL : $img['img_url'];
        $act['b_time'] = $model->b_time;
        $act['e_time'] = $model->e_time;
        $act['share_url'] = Yii::app()->webPage->getViewUrl('act/activity/shareweb', array('actId' => $model->id));
        $act['publish_time'] = $model->publish_time;
        $act['status'] = $model->status;
        $act['enroll_num'] = ActEnrollO::model()->countMembersO($model->id);
        $act['checkin_num'] = ActCheckinStepO::model()->countCheckinUsersO($model->id);
        return $act;
    }
    
    
    /**
     * 详情信息
     * 
     * @param type $id 活动id
     * @param type $model
     */
    public function fullProfile0($id, $model = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($id);
        }
        $act['id'] = $model->id;
        $act['title'] = $model->title;
        $act['detail'] = $model->detail;
        $act['h_img_id'] = $model->h_img_id;
        if (!empty($model->h_img_id)) {
            $img = ImgInfo::model()->profile($model->h_img_id);
            if (!empty($img)) {
                $act['h_img_url'] = $img['img_url'];
            }
        }
        $act['b_time'] = $model->b_time;
        $act['e_time'] = $model->e_time;
        $act['share_url'] = Yii::app()->webPage->getViewUrl('act/activity/shareweb', array('actId' => $model->id));
        $act['publish_time'] = $model->publish_time;
        $act['status'] = $model->status;
        $act['lon'] = $model->lon;
        $act['lat'] = $model->lat;
        $act['addr_city'] = $model->addr_city;
        $act['addr_area']= $model->addr_area;
        $act['addr_road'] = $model->addr_road;
        $act['addr_num'] = $model->addr_num;
        $act['addr_name'] = $model->addr_name;
        $act['addr_route'] = $model->addr_route;
        $act['contact_way'] = $model->contact_way;
        
        $act['enroll_num'] = ActEnrollO::model()->countMembersO($model->id);
        $act['checkin_num'] = ActCheckinStepO::model()->countCheckinUsersO($model->id);
        $act['enroll_b_time'] = NULL;
        $act['enroll_e_time'] = NULL;
        $act['enroll_limit'] = -1;
        $act['enroll_limit_num'] = -1;
        $act['show_pay'] = -1;
        $act['total_fee'] = -1;
        $act['show_verify'] = -1;
        
        
        $act['route_maps'] = array_map(function($record) {
                   $info =  $record->attributes;
                   $info['act_route_points'] = unserialize( $info['act_route_points']);
                   return $info;
                }, ActRoute::model()->getActRoutes($model->id));
        
        //扩展字段
       $extend = ActInfoExtendO::model()->getByActIdO($model->id);
       if($extend){
            $act['enroll_b_time'] = $extend->enroll_b_time;
            $act['enroll_e_time'] = $extend->enroll_e_time;
            $act['enroll_limit'] = $extend->enroll_limit;
            $act['enroll_limit_num'] = $extend->enroll_limit_num;
            $act['show_pay'] = $extend->show_pay;
            
            $act['show_verify'] = $extend->show_verify;
            //$act['extend'] = $extend->attributes;
            $product = Product::model()->profile($extend->product_id);
            //if($product){
            //    $act['total_fee'] = $product['unit_price'];
            //}
            //$act['total_fee'] = 0;
            if (!empty($product)) {
                $act['total_fee'] = $product['unit_price'];
            }
       }

        //图片集
        $act['imgs'] = ActImgMap::model()->getImgs($model->id);
        
        //custom_fields
        $custom_fields = CustomExtActMap::model()->allCustomKeys($model->id);
        if($custom_fields){
            foreach ($custom_fields as $custom_field){
                $act['custom_fields'][] = $custom_field['subject'];
            }
        }
        return $act;
    }
    
    
    public function  getActDA($orgId){
        $cr = new CDbCriteria();
        $cr->compare('t.org_id', $orgId);
        $cr->compare('t.e_time', '<' . date('Y-m-d H:i:s'));
        $cr->compare('t.status', '<>' . ConstActStatus::DELETE);
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $rst = $this->findAll($cr);
        $acts = array();
        foreach ($rst as $v) {
            $act = $this->profile0(NULL, $v);
            $act['male'] = ActCheckinStepO::model()->countActSex($act['id'], 1);
            $act['female'] = ActCheckinStepO::model()->countActSex($act['id'], 2);
            $act['checked_info'] = ActCheckinStepO::model()->actCheckinDA($act['id']);
            
//            var_dump(ActCheckinStepO::model()->countActSex($act['id'], 1));
//            echo '----';
//            var_dump(ActCheckinStepO::model()->countActSex($act['id'], 2));
//            echo '<br/>---------------------------------------<br/>';
            
            array_push($acts, $act);
        }
        return array(
            'total_num' => $count,
            'acts' => $acts,
        );
    }
    
    
    /**
     * 社团进行中的活动数，已发布并未结束的
     * 
     * @param type $orgId 社团id
     */
    public function progressActNumO($orgId) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.org_id', $orgId);
        $cr->compare('t.status', ConstActStatus::PUBLISHING);
        $cr->compare('t.e_time', '>' . date('Y-m-d H:i:s'));
        return $this->count($cr);
    }
    
}

?>
