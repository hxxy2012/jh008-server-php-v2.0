<?php

class BrokeNewsM extends BrokeNews {
    
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
     * 爆料基本信息
     * 
     * @param type $model 爆料数据
     * @param type $bid 爆料id
     */
    public function profileM($model = NULL, $bid = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($bid);
        }
        if (empty($model)) {
            return NULL;
        }
        
        $brokeNewsInfo = array();
        $brokeNewsInfo['id'] = $model->id;
        $brokeNewsInfo['city_id'] = $model->city_id;
        $brokeNewsInfo['u_id'] = $model->u_id;
        $brokeNewsInfo['contact_phone'] = $model->contact_phone;
        $brokeNewsInfo['contact_address'] = $model->contact_address;
        $brokeNewsInfo['intro'] = $model->intro;
        $brokeNewsInfo['lon'] = $model->lon;
        $brokeNewsInfo['lat'] = $model->lat;
        $brokeNewsInfo['address'] = $model->address;
        $brokeNewsInfo['status'] = $model->status;
        $brokeNewsInfo['create_time'] = $model->create_time;
        $imgs = BrokeNewsImgMapM::model()->imgsM($model->id, 1, 3);
        $brokeNewsInfo['imgs'] = $imgs['imgs'];
        return $brokeNewsInfo;
    }


    /**
     * 获取爆料
     * 
     * @param type $cityId 城市id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function brokeNewsListM($cityId, $page, $size) 
    {
        $cr = new CDbCriteria();
        if (!empty($cityId)) {
            $cr->compare('t.city_id', $cityId);
        }
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $brokeNews = array();
        foreach ($rst as $v) {
            $brokeNewsInfo = $this->profileM($v, NULL);
            if (empty($brokeNewsInfo)) {
                continue;
            }
            array_push($brokeNews, $brokeNewsInfo);
        }
        
        return array(
            'total_num' => $count,
            'broke_news' => $brokeNews,
        );
    }
    
}

?>
