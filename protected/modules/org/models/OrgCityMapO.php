<?php

class OrgCityMapO extends OrgCityMap {
    
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
     * 更新社团和城市关联
     * 
     * @param type $orgId 社团id
     * @param type $cityId 城市id
     */
    public function upO($orgId, $cityId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.org_id', $orgId);
        $cr->compare('t.city_id', $cityId);
        $model = $this->find($cr);
        if (empty($model)) {
            $model = new OrgCityMap();
            $model->org_id = $orgId;
            $model->city_id = $cityId;
            $model->status = ConstStatus::NORMAL;
            $model->create_time = date('Y-m-d H:i:s');
            return $model->save();
        }  else {
            $model->status = ConstStatus::NORMAL;
            return $model->update();
        }
    }

    
    /**
     * 根据社团id获取对应城市
     * 
     * @param type $orgId 社团id
     */
    public function getCityByOrgO($orgId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.org_id', $orgId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $model = $this->find($cr);
        if (empty($model)) {
            return NULL;
        }
        return CityInfoO::model()->findByPk($model->city_id);
    }
    
}

?>
