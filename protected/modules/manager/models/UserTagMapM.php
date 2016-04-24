<?php

class UserTagMapM extends UserTagMap {
    
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
     * 添加用户标签
     * 
     * @param type $uid 用户id
     * @param type $tagId 标签id
     */
    public function addM($uid, $tagId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $uid);
        $cr->compare('t.tag_id', $tagId);
        $model = $this->find($cr);
        if (empty($model)) {
            $model = new UserTagMap();
            $model->u_id = $uid;
            $model->tag_id = $tagId;
            $model->status = ConstStatus::NORMAL;
            $model->create_time = date('Y-m-d H:i:s');
            return $model->save();
        }
        if (ConstStatus::DELETE == $model->status) {
            $model->status = ConstStatus::NORMAL;
            return $model->update();
        }
        return TRUE;
    }
    
}

?>
