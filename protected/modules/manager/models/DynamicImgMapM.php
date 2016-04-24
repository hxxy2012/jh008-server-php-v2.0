<?php

class DynamicImgMapM extends DynamicImgMap {
    
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
     * 动态的图片
     * 
     * @param type $dynamicId 动态id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function imgsM($dynamicId, $page, $size) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.dynamic_id', $dynamicId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $imgs = array();
        foreach ($rst as $v) {
            $img = ImgInfo::model()->profile($v->img_id);
            if (empty($img)) {
                continue;
            }
            array_push($imgs, $img);
        }
        
        return array(
            'total_num' => $count,
            'imgs' => $imgs,
        );
    }
    
}

?>
