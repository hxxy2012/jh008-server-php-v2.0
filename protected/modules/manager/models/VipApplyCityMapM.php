<?php

class VipApplyCityMapM extends VipApplyCityMap {
    
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
     * 达人申请列表
     * 
     * @param type $cityId 城市id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function applysM($cityId, $page, $size) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.city_id', $cityId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $applys = array();
        foreach ($rst as $v) {
            $apply = VipApplyM::model()->profileM(NULL, $v->apply_id);
            if (empty($apply)) {
                continue;
            }
            array_push($applys, $apply);
        }
        
        return array(
            'total_num' => $count,
            'applys' => $applys,
        );
    }
    
    
    /**
     * 验证达人申请的城市
     * 
     * @param type $applyId 申请id
     * @param type $cityId 城市id
     */
    public function checkVipCityM($applyId, $cityId) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.apply_id', $applyId);
        $cr->compare('t.city_id', $cityId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $model = $this->find($cr);
        return empty($model) ? FALSE : TRUE;
    }
    
}

?>
