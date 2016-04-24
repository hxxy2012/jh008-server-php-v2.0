<?php

class VipApplyUserTagMapM extends VipApplyUserTagMap {
    
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
     * 达人申请的用户标签
     * 
     * @param type $applyId 申请id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function tagsM($applyId, $page, $size) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.apply_id', $applyId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $tags = array();
        foreach ($rst as $v) {
            $tag = UserTagM::model()->profileM(NULL, $v->tag_id);
            if (empty($tag)) {
                continue;
            }
            array_push($tags, $tag);
        }
        
        return array(
            'total_num' => $count,
            'tags' => $tags,
        );
    }
    
    
    public function setToVipInfo($applyId, $vipId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.apply_id', $applyId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $rst = $this->findAll($cr);
        
        foreach ($rst as $v) {
            UserTagMapM::model()->addM($vipId, $v->tag_id);
        }
        return TRUE;
    }
    
}

?>
