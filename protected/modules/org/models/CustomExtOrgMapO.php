<?php

class CustomExtOrgMapO extends CustomExtOrgMap {
    
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
     * 处理社团自定义字段
     * 
     * @param type $orgId 社团id
     * @param array $ids 自定义字段id数组
     */
    public function dealCusIdsO($orgId, array $ids)
    {
        foreach ($ids as $v) {
            $model = $this->getO($orgId, $v);
            if (empty($model)) {
                $this->createO($orgId, $v);
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
        'c_id not in (' . implode(',', $ids) . ') and o_id=:orgId',
        array(
            ':orgId' => $orgId,
            )
        );
    }
    
    
    /**
     * 获取社团自定义字段关联
     * 
     * @param type $orgId 社团id
     * @param type $cid 自定义字段id
     */
    public function getO($orgId, $cid)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.o_id', $orgId);
        $cr->compare('t.c_id', $cid);
        return $this->find($cr);
    }
    
    
    /**
     * 创建社团自定义字段关联
     * 
     * @param type $actId 社团id
     * @param type $cid 自定义字段id
     */
    public function createO($orgId, $cid)
    {
        $model = new CustomExtOrgMap();
        $model->o_id = $orgId;
        $model->c_id = $cid;
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        $model->modify_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
    
}

?>
