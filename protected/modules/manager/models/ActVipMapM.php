<?php

class ActVipMapM extends ActVipMap {
    
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
     * 相关达人
     * 
     * @param type $actId 活动id
     * @param type $cityId 城市id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function vipsM($actId, $cityId, $page, $size) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $count = $this->count($cr);
        
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        $vips = array();
        foreach ($rst as $v) {
            $vip = UserInfoM::model()->suserM(NULL, $v->u_id, $cityId);
            if (empty($vip)) {
                continue;
            }
            array_push($vips, $vip);
        }
        
        return array(
            'total_num' => $count,
            'vips' => $vips,
        );
    }
    
    
    /**
     * 更新活动达人关联
     * 
     * @param type $model 活动达人关联数据
     * @param type $actId 活动id
     * @param type $vipId 达人id
     */
    public function updateM($model = NULL, $actId = NULL, $vipId = NULL)
    {
        if (empty($model)) {
            $model = new ActVipMap();
        }
        if (!empty($actId)) {
            $model->act_id = $actId;
        }
        if (!empty($vipId)) {
            $model->u_id = $vipId;
        }
        
        $m = $this->getM($model->act_id, $model->u_id);
        if (empty($m)) {
            $model->status = ConstStatus::NORMAL;
            $model->create_time = date('Y-m-d H:i:s');
            return $model->save();
        }
        
        $m->status = ConstStatus::NORMAL;
        return $m->update();
    }
    
    
    /**
     * 取得数据模型
     * 
     * @param type $actId 活动id
     * @param type $vipId 达人id
     */
    public function getM($actId, $vipId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.u_id', $vipId);
        return $this->find($cr);
    }
    
    
    /**
     * 删除活动和达人关联
     * 
     * @param type $actId 活动id
     * @param type $vipId 达人id
     */
    public function delM($actId, $vipId)
    {
        $model = $this->getM($actId, $vipId);
        if (empty($model)) {
            return TRUE;
        }
        $model->status = ConstStatus::DELETE;
        return $model->update();
    }
    
}

?>
