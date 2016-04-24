<?php

class CustomExtActMapO extends CustomExtActMap {
    
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
     * 处理活动自定义字段
     * 
     * @param type $actId 活动id
     * @param array $ids 自定义字段id数组
     */
    public function dealCusIdsO($actId, array $ids)
    {
        foreach ($ids as $v) {
            $model = $this->getO($actId, $v);
            if (empty($model)) {
                $this->createO($actId, $v);
                continue;
            }
            if (ConstStatus::DELETE == $model->status) {
                $model->status = ConstStatus::NORMAL;
                $model->modify_time = date('Y-m-d H:i:s');
                $model->update();
            }
        }
        
        $this->updateAll(array(
            'status' => ConstStatus::DELETE,
        ),
        'c_id not in (' . implode(',', $ids) . ') and act_id=:actId',
        array(
            ':actId' => $actId,
            )
        );
    }
    
    
    /**
     * 获取活动自定义字段关联
     * 
     * @param type $actId 活动id
     * @param type $cid 自定义字段id
     */
    public function getO($actId, $cid)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.c_id', $cid);
        return $this->find($cr);
    }
    
    
    /**
     * 创建活动自定义字段关联
     * 
     * @param type $actId 活动id
     * @param type $cid 自定义字段id
     */
    public function createO($actId, $cid)
    {
        $model = new CustomExtActMap();
        $model->act_id = $actId;
        $model->c_id = $cid;
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        $model->modify_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
}

?>
