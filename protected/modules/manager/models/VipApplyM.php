<?php

class VipApplyM extends VipApply {
    
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
     * @param type $model 达人申请数据
     * @param type $applyId 申请id
     */
    public function profileM($model = NULL, $applyId = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($applyId);
        }
        if (empty($model)) {
            return NULL;
        }
        $apply = array();
        $apply['id'] = $model->id;
        $apply['author_id'] = $model->author_id;
        $apply['real_name'] = $model->real_name;
        $apply['contact_phone'] = $model->contact_phone;
        $apply['email'] = $model->email;
        $apply['intro'] = $model->intro;
        $actTags = VipApplyTagMapM::model()->tagsM($model->id, 1, 12);
        $apply['act_tags'] = $actTags['tags'];
        $userTags = VipApplyUserTagMapM::model()->tagsM($model->id, 1, 12);
        $apply['user_tags'] = $userTags['tags'];
        $photos = VipApplyImgMapM::model()->imgsM($model->id, 1, 12);
        $apply['photos'] = $photos['imgs'];
        $apply['create_time'] = $model->create_time;
        return $apply;
    }
    
    
    /**
     * 处理达人申请
     * 
     * @param type $applyId 申请id
     * @param type $cityId 城市id
     * 
     */
    public function dealM($applyId, $cityId) 
    {
        $uc = VipApplyCityMapM::model()->checkVipCityM($applyId, $cityId);
        if (empty($uc)) {
            return FALSE;
        }
        $model = $this->findByPk($applyId);
        if (empty($model) || ConstStatus::DELETE == $model->status) {
            return FALSE;
        }
        $vip = UserInfoExtend::model()->find('u_id=:uid', array(':uid' => $model->author_id));
        if (empty($vip)) {
            return FALSE;
        }
        $rst = UserCityMapM::model()->updateM(NULL, $vip->u_id, $cityId);
        if (!$rst) {
            return FALSE;
        }
        $rst = $this->setToVipInfo($model, $vip);
        $rst = VipApplyTagMapM::model()->setToVipInfo($model->id, $vip->u_id);
        $rst = VipApplyUserTagMapM::model()->setToVipInfo($model->id, $vip->u_id);
        return TRUE;
    }
    
    
    function setToVipInfo($apply, $vip) 
    {
        $vip->real_name = $apply->real_name;
        $vip->contact_phone = $apply->contact_phone;
        $vip->email = $apply->email;
        $vip->intro = $apply->intro;
        return $vip->update();
    }
    
}

?>
