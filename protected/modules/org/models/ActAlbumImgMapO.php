<?php

class ActAlbumImgMapO extends ActAlbumImgMap {
    
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
     * 相册的图片
     * 
     * @param type $albumId 相册id
     */
    public function imgsO($albumId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.album_id', $albumId);
        $cr->compare('t.status', ConstCheckStatus::PASS);
        $count = $this->count($cr);
        $cr->order = 't.id asc';
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
    
    
    /**
     * 添加相册图片
     * 
     * @param type $albumId 相册id
     * @param type $imgId 图片id
     */
    public function addO($albumId, $imgId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.album_id', $albumId);
        $cr->compare('t.img_id', $imgId);
        $model = $this->find($cr);
        
        if (empty($model)) {
            $model = new ActAlbumImgMap();
            $model->album_id = $albumId;
            $model->img_id = $imgId;
            $model->status = ConstCheckStatus::PASS;
            $model->create_time = date('Y-m-d H:i:s');
            $model->modify_time = date('Y-m-d H:i:s');
            return $model->save();
        }
        $model->status = ConstStatus::PASS;
        $model->modify_time = date('Y-m-d H:i:s');
        return $model->update();
    }
    
    
    /**
     * 删除相册图片
     * 
     * @param type $albumId 相册id
     * @param type $imgId 图片id
     */
    public function delO($albumId, $imgId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.album_id', $albumId);
        $cr->compare('t.img_id', $imgId);
        $model = $this->find($cr);
        
        if (empty($model)) {
            return TRUE;
        }
        $model->status = ConstStatus::DELETE;
        $model->modify_time = date('Y-m-d H:i:s');
        return $model->update();
    }
    
}

?>
