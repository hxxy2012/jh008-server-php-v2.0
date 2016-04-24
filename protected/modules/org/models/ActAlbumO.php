<?php

class ActAlbumO extends ActAlbum {
    
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
     * 获取活动主办方相册
     * 
     * @param type $actId 活动id
     */
    public function getOrgAlbumO($actId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.type', 1);
        $cr->compare('t.owner_type', 1);
        return $this->find($cr);
    }

    
    /**
     * 创建活动主办方相册
     * 
     * @param type $actId
     * @param ActAlbum $model
     */
    public function createOrgAlbumO($actId, $model = NULL)
    {
        $m = $this->getOrgAlbumO((empty($model) || empty($model->act_id)) ? $actId : $model->act_id);
        if (!empty($m)) {
            return FALSE;
        }
        
        if (empty($model)) {
            $model = new ActAlbum();
        }
        if (empty($model->act_id)) {
            $model->act_id = $actId;
        }
        $model->type = 1;
        $model->owner_type = 1;
        $model->status = ConstCheckStatus::PASS;
        $model->create_time = date('Y-m-d H:i:s');
        $model->modify_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
}

?>
