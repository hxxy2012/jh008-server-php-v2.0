<?php

class CityInfoM extends CityInfo {
    
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
     * 城市
     * 
     * @param type $cityId 城市id
     */
    public function city($cityId)
    {
        $model = $this->findByPk($cityId);
        //if (empty($model) || ConstStatus::DELETE == $model->status) {
        //    return NULL;
        //}
        if (empty($model)) {
            return NULL;
        }
        return array(
            'id' => $model->id,
            'name' => $model->name,
            'status' => $model->status,
        );
    }
    
    
    /**
     * 获取城市列表
     * 
     * @param type $page 分页：页码
     * @param type $size 分页：每页条数
     * @param type $isDel 是否已删除的
     */
    public function cities($page, $size)
    {
        $cr = new CDbCriteria();
        if (!empty($page) && !empty($size)) {
            $cr->offset = ($page - 1) * $size;
            $cr->limit = $size;
        }
        $cr->compare('t.status', 1);
        //if ($isDel) {
        //    $cr->compare('status', ConstStatus::DELETE);
        //}  else {
        //    $cr->compare('status', '<>' . ConstStatus::DELETE);
        //}
        $count = $this->count($cr);
        $rst = $this->findAll($cr);
        
        $cities = array();
        foreach ($rst as $v) {
            $city = array();
            $city['id'] = $v->id;
            $city['name'] = $v->name;
            $city['status'] = $v->status;
            array_push($cities, $city);
        }
        
        return array(
            'total_num' => $count,
            'cities' => $cities
        );
    }
    
}

?>
