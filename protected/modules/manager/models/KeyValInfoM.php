<?php

class KeyValInfoM extends KeyValInfo {
    
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
     * 获取推荐活动
     * 
     * @param type $cityId 城市id
     * @param type $timeStatus 时间状态
     * @param type $tagId 标签分类id
     * @param type $keyWords 关键字
     * @param type $page 页数
     * @param type $size 每页活动数
     */
    public function getRecommendActsM($cityId, $timeStatus = NULL, $tagId = NULL, $keyWords = NULL, $page ,$size)
    {
        $kvArr = array();
        $kvArr['key'] = 'cityrecommendacts';
        $kvArr['cityid'] = $cityId;
        //根据标签搜索活动列表key
        $key = ArrTool::implodeColonArr($kvArr);
        $model = $this->findK($key);
        
        if (empty($model)) {
            return array(
                'total_num' => 0,
                'acts' => array(),
            );
        }
        $ids = json_decode($model->val);
        //$totalNum = count($ids);
        //按分页取出需要的活动id
        //$needIds = ArrTool::sliceByPageAndSize($ids, $page, $size);
        return ActInfoM::model()->actsM($cityId, $tagId, $keyWords = NULL, $timeStatus = NULL, $page, $size, $ids);
    }
    
    
    /**
     * 更新推荐活动
     * 
     * @param type $cityId 城市id
     * @param type $actIds 活动id数组
     */
    public function upRecommmendActsM($cityId, array $actIds)
    {
        $kvArr = array();
        $kvArr['key'] = 'cityrecommendacts';
        $kvArr['cityid'] = $cityId;
        //根据标签搜索活动列表key
        $key = ArrTool::implodeColonArr($kvArr);
        $model = $this->findK($key);
        
        if (empty($model)) {
            return $this->insKv($key, $actIds);
        }
        
        $idsStr = json_encode(ArrTool::toNumArr($actIds));
        if (!empty($model->val) && $idsStr == $model->val) {
            return TRUE;
        }
        
        $model->val = $idsStr;
        $model->update_time = date('Y-m-d H:i:s');
        return $model->update();
    }
    
    
    /**
     * 获取推荐置顶的达人
     * 
     * @param type $cityId 城市id
     * @param type $tagId 活动标签分类id
     * @param type $page 页数
     * @param type $size 每页活动数
     */
    public function getRecommendUsersM($cityId, $tagId, $page, $size)
    {
        $kvArr = array();
        $kvArr['key'] = 'citytagrecommendusers';
        $kvArr['cityid'] = $cityId;
        $kvArr['tagid'] = $tagId;
        //根据标签搜索达人列表key
        $key = ArrTool::implodeColonArr($kvArr);
        $model = $this->findK($key);
        
        if (empty($model)) {
            return array(
                'total_num' => 0,
                'users' => array(),
            );
        }
        $ids = json_decode($model->val);
        $totalNum = count($ids);
        //按分页取出需要的活动id
        $needIds = ArrTool::sliceByPageAndSize($ids, $page, $size);
        
        $users = array();
        foreach ($needIds as $v) {
            $user = UserInfoM::model()->profileM(NULL, $v, $cityId);
            if (empty($user)) {
                continue;
            }
            array_push($users, $user);
        }
        
        return array(
            'total_num' => $totalNum,
            'users' => $users,
        );
    }
    
    
    /**
     * 设置推荐置顶的达人
     * 
     * @param type $cityId 城市id
     * @param type $tagId 活动标签分类id
     * @param type $vipIds 达人id数组
     */
    public function upRecommendUsersM($cityId, $tagId, array $vipIds)
    {
        $kvArr = array();
        $kvArr['key'] = 'citytagrecommendusers';
        $kvArr['cityid'] = $cityId;
        $kvArr['tagid'] = $tagId;
        //根据标签搜索达人列表key
        $key = ArrTool::implodeColonArr($kvArr);
        $model = $this->findK($key);
        
        if (empty($model)) {
            return $this->insKv($key, $vipIds);
        }
        
        $idsStr = json_encode(ArrTool::toNumArr($vipIds));
        if (!empty($model->val) && $idsStr == $model->val) {
            return TRUE;
        }
        
        $model->val = $idsStr;
        $model->update_time = date('Y-m-d H:i:s');
        return $model->update();
    }
    
    
    /**
     * 获取首页轮播
     * 
     * @param type $cityId 城市id
     * @param type $page 页数
     * @param type $size 每页活动数
     */
    public function getHomeAdvertsM($cityId, $page ,$size)
    {
        $kvArr = array();
        $kvArr['key'] = 'cityhomeadverts';
        $kvArr['cityid'] = $cityId;
        //根据标签搜索轮播列表key
        $key = ArrTool::implodeColonArr($kvArr);
        $model = $this->findK($key);
        
        if (empty($model)) {
            return array(
                'total_num' => 0,
                'news' => array(),
            );
        }
        $ids = json_decode($model->val);
        $totalNum = count($ids);
        //按分页取出需要的轮播id
        $needIds = ArrTool::sliceByPageAndSize($ids, $page, $size);
        
        $news = array();
        foreach ($needIds as $v) {
            $newsInfo = NewsInfoM::model()->profileM(NULL, $v);
            if (empty($newsInfo)) {
                continue;
            }
            array_push($news, $newsInfo);
        }
        
        return array(
            'total_num' => $totalNum,
            'news' => $news,
            );
    }
    
    
    /**
     * 更新首页轮播
     * 
     * @param type $cityId 城市id
     * @param type $newsIds 轮播id数组
     */
    public function upHomeAdvertsM($cityId, array $newsIds)
    {
        $kvArr = array();
        $kvArr['key'] = 'cityhomeadverts';
        $kvArr['cityid'] = $cityId;
        //根据标签搜索轮播列表key
        $key = ArrTool::implodeColonArr($kvArr);
        $model = $this->findK($key);
        
        if (empty($model)) {
            return $this->insKv($key, $newsIds);
        }
        
        $idsStr = json_encode(ArrTool::toNumArr($newsIds));
        if (!empty($model->val) && $idsStr == $model->val) {
            return TRUE;
        }
        
        $model->val = $idsStr;
        $model->update_time = date('Y-m-d H:i:s');
        return $model->update();
    }
    
}

?>
