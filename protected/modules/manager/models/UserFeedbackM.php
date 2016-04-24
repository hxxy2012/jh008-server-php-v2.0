<?php

class UserFeedbackM extends UserFeedback {
    
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
     * 意见反馈基本信息
     * 
     * @param type $model 意见反馈数据
     * @param type $fid 意见反馈id
     */
    public function profileM($model = NULL, $fid = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($fid);
        }
        if (empty($model)) {
            return NULL;
        }
        
        $feedback = array();
        $feedback['id'] = $model->id;
        $feedback['city_id'] = $model->city_id;
        $feedback['u_id'] = $model->u_id;
        $feedback['content'] = $model->content;
        $feedback['lon'] = $model->lon;
        $feedback['lat'] = $model->lat;
        $feedback['address'] = $model->address;
        $feedback['status'] = $model->status;
        $feedback['create_time'] = $model->create_time;
        $imgs = UserFeedbackImgMapM::model()->imgsM($model->id, 1, 3);
        $feedback['imgs'] = $imgs['imgs'];
        return $feedback;
    }


    /**
     * 获取意见反馈
     * 
     * @param type $cityId 城市id
     * @param type $uid 用户id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function feedbacksM($cityId, $uid, $page, $size) 
    {
        $cr = new CDbCriteria();
        if (!empty($cityId)) {
            $cr->compare('t.city_id', $cityId);
        }
        if (!empty($uid)) {
            $cr->compare('t.u_id', $uid);
        }
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $feedbacks = array();
        foreach ($rst as $v) {
            $feedback = $this->profileM($v, NULL);
            if (empty($feedback)) {
                continue;
            }
            array_push($feedbacks, $feedback);
        }
        
        return array(
            'total_num' => $count,
            'feedbacks' => $feedbacks,
        );
    }
    
}

?>
