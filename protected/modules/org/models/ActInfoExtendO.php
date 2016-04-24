<?php

class ActInfoExtendO extends ActInfoExtend {
    
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
     * 根据活动id获取活动
     * 
     * @param type $actId 活动id
     */
    public function getByActIdO($actId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        return $this->find($cr);
    }
    
    
    /**
     * 创建活动
     * 
     * @param type $model
     * @param type $subject
     * @param type $body
     * @param type $totalFee
     */
    public function createActO($model, $subject, $body, $totalFee = NULL)
    {
        $model->show_enroll = 1;
        
        $product = new Product();
        ProductO::model()->createO($subject, $body, $totalFee, $product);
        $model->product_id = $product->id;
        
        $model->show_enroll_custom = 1;
        $model->show_manager = 1;
        $model->show_navi = 1;
        $model->show_route_map = 1;
        $model->show_location_share = 1;
        $model->show_album = 1;
        $model->can_upload_album = 1;
        //$model->show_video = 1;
        $model->show_message = 1;
        $model->show_group = 1;
        $model->show_notice = 1;
        $model->show_checkin = 1;
        $r = $model->save();
        
        if ($r && isset($model->show_pay) && -1 != $model->show_pay) {
            ActInfoO::model()->modifyActStatus($model->act_id, ConstActStatus::PUBLISHING, NULL);
        }
        return $r;
    }

    
    /**
     * 修改活动
     * 
     * @param type $model
     * @param type $subject
     * @param type $body
     * @param type $totalFee
     */
    public function modifyActO($model, $subject, $body, $totalFee = NULL)
    {
        $model->show_enroll = 1;
        if (empty($model->product_id)) {
            $product = new Product();
            ProductO::model()->createO($subject, $body, $totalFee, $product);
            $model->product_id = $product->id;
        }  else {
            ProductO::model()->modifyO($model->product_id, $subject, $body, $totalFee);
        }
        $model->show_enroll_custom = 1;
        $model->show_manager = 1;
        $model->show_navi = 1;
        $model->show_route_map = 1;
        $model->show_location_share = 1;
        $model->show_album = 1;
        $model->can_upload_album = 1;
        //$model->show_video = 1;
        $model->show_message = 1;
        $model->show_group = 1;
        $model->show_notice = 1;
        $model->show_checkin = 1;
        $r = $model->update();
        
        if ($r && isset($model->show_pay) && -1 != $model->show_pay) {
            ActInfoO::model()->modifyActStatus($model->act_id, ConstActStatus::PUBLISHING, NULL);
        }
        return $r;
    }
    
}

?>
