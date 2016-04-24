<?php

class TagInfoAdmin extends TagInfo
{
    
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
     * 搜索标签列表
     * @param type $keyWords
     * @param type $isDel
     */
    public function searchTags($keyWords, $isDel = FALSE) 
    {
        $cr = new CDbCriteria();
        if ($isDel) {
            $cr->compare('t.status', ConstStatus::DELETE);
        }  else {
            $cr->compare('t.status', ConstStatus::NORMAL);
        }
        if (!empty($keyWords)) {
            $cr->compare('t.name', $keyWords, TRUE);
        }
        $count = $this->count($cr);
        $cr->with = 'fkAllActs';
        $rst = $this->findAll($cr);
        
        $tags = array();
        foreach ($rst as $v) {
            $tag = array();
            $tag['id'] = $v->id;
            $tag['name'] = $v->name;
            $tag['status'] = $v->status;
            $tag['count'] = $v->count;
            $tag['update_time'] = $v->update_time;
            $tag['act_used_num'] = count($v->fkAllActs);
            array_push($tags, $tag);
        }
        return array(
            'total_num' => $count,
            'tags' => $tags,
        );
    }
    
}

?>
