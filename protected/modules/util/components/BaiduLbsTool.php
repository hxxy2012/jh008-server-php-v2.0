<?php

class BaiduLbsTool extends CComponent
{
    //API控制台申请得到的ak（此处ak值仅供验证参考使用）
    public $ak;
    
    //应用类型为for server, 请求校验方式为sn校验方式时，系统会自动生成sk，可以在应用配置-设置中选择Security Key显示进行查看（此处sk值仅供验证参考使用）
    public $sk;
    
    public $geotable_id;

    public function init() 
    {
        
    }
    
    
    /**
     * 获取列
     */
    public function listColumn() 
    {
        $url = "http://api.map.baidu.com/geodata/v3/column/list?geotable_id=%s&ak=%s&sn=%s";
        $uri = '/geodata/v3/column/list';
        $geotable_id = $this->geotable_id;
        $querystring_arrays = array (
            'geotable_id' => $this->geotable_id,
            'ak' => $this->ak,
        );
        //调用sn计算函数，默认get请求
        $sn = $this->caculateAKSN($this->ak, $this->sk, $uri, $querystring_arrays, 'GET');
        //请求参数中有中文、特殊字符等需要进行urlencode，确保请求串与sn对应
        $target = sprintf($url, $geotable_id, $this->ak, $sn);
        $result_str = $this->request($target);
        echo $result_str;
    }
    
    
    /**
     * 初始化数据字段
     */
    public function initColumn()
    {
        //名称、列名、类型（1 int,2 double, 3 string, 4 图片url）
        //最大长度、默认值、是否排序（最多15个）
        //是否文本检索、是否存储引擎的索引、是否唯一索引
        $this->createColumn('活动id', 'act_id', 1, NULL, NULL, 1, 0, 1, 1);
        $this->createColumn('标题', 'act_title', 3, 256, NULL, 0, 1, 0, 0);
        $this->createColumn('活动简述（详情内标题）', 'act_intro', 3, 512, NULL, 0, 0, 0, 0);
        $this->createColumn('城市id', 'act_city_id', 1, NULL, NULL, 1, 0, 1, 0);
        $this->createColumn('标签id', 'act_tag_id', 1, NULL, NULL, 1, 0, 1, 0);
        $this->createColumn('花费', 'act_cost', 2, NULL, NULL, 1, 0, 0, 0);
        $this->createColumn('经度', 'act_lon', 2, NULL, NULL, 0, 0, 0, 0);
        $this->createColumn('纬度', 'act_lat', 2, NULL, NULL, 0, 0, 0, 0);
        $this->createColumn('地址（城市）', 'act_addr_city', 3, 256, NULL, 0, 0, 0, 0);
        $this->createColumn('地址（区）', 'act_addr_area', 3, 256, NULL, 0, 0, 0, 0);
        $this->createColumn('地址（路）', 'act_addr_road', 3, 256, NULL, 0, 0, 0, 0);
        $this->createColumn('地址（号）', 'act_addr_num', 3, 256, NULL, 0, 0, 0, 0);
        $this->createColumn('地址（名称）', 'act_addr_name', 3, 256, NULL, 0, 0, 0, 0);
        $this->createColumn('地址（路线）', 'act_addr_route', 3, 256, NULL, 0, 0, 0, 0);
        $this->createColumn('联系方式', 'act_contact_way', 3, 256, NULL, 0, 0, 0, 0);
        $this->createColumn('开始时间', 'act_b_time', 3, 256, NULL, 0, 0, 0, 0);
        $this->createColumn('结束时间', 'act_e_time', 3, 256, NULL, 0, 0, 0, 0);
        $this->createColumn('时间状态', 'act_t_status', 1, NULL, NULL, 0, 0, 0, 0);
        $this->createColumn('时间状态规则', 'act_t_status_rule', 1, NULL, NULL, 0, 0, 0, 0);
        $this->createColumn('活动详情', 'act_detail', 3, 512, NULL, 0, 0, 0, 0);
        $this->createColumn('活动图文详情', 'act_detail_all', 3, 512, NULL, 0, 0, 0, 0);
        $this->createColumn('是否可以报名', 'act_can_enroll', 1, NULL, NULL, 0, 0, 0, 0);
        $this->createColumn('首图id', 'act_h_img_id', 1, NULL, NULL, 0, 0, 0, 0);
        $this->createColumn('状态', 'act_status', 1, NULL, NULL, 0, 0, 0, 0);
        $this->createColumn('创建时间', 'act_create_time', 3, 256, NULL, 0, 0, 0, 0);
        $this->createColumn('更新时间', 'act_update_time', 3, 256, NULL, 0, 0, 0, 0);
        $this->createColumn('发布时间', 'act_publish_time', 3, 256, NULL, 0, 0, 0, 0);
        $this->createColumn('感兴趣数的基数', 'act_lov_base_num', 1, NULL, NULL, 0, 0, 0, 0);
        $this->createColumn('分享数的基数', 'act_share_base_num', 1, NULL, NULL, 0, 0, 0, 0);
        $this->createColumn('每周循环的数组', 'act_week_rules', 3, 256, NULL, 0, 0, 0, 0);
        //ios用于检索的字段（ios特供：针对ios的sdk的bug）
        //$this->createColumn('ios检索字段', 'ios_search', 3, 256, 'act', 0, 1, 0, 0);
    }

    
    /**
     * 创建列
     */
    public function createColumn(
            $name, 
            $key, 
            $type, 
            $max_length, 
            $default_value, 
            $is_sortfilter_field, 
            $is_search_field, 
            $is_index_field, 
            $is_unique_field)
    {
        $url = "http://api.map.baidu.com/geodata/v3/column/create";
        $uri = '/geodata/v3/column/create';
        $post_arrays = array (
            'name' => $name,
            'key' => $key,
            'type' => $type,
            //'max_length' => $max_length,
            //'default_value' => $default_value,
            'is_sortfilter_field' => $is_sortfilter_field,
            'is_search_field' => $is_search_field,
            'is_index_field' => $is_index_field,
            'is_unique_field' => $is_unique_field,
            'geotable_id' => $this->geotable_id,
            'ak' => $this->ak,
        );
        if (!empty($default_value)) {
            $post_arrays['default_value'] = $default_value;
        }
        if (!empty($max_length)) {
            $post_arrays['max_length'] = $max_length;
        }
        $sn = $this->caculateAKSN($this->ak, $this->sk, $uri, $post_arrays, 'POST');
        $post_arrays['sn'] = $sn;
        $result_str = $this->postrequest($url, $post_arrays);
        $rst = json_decode($result_str, TRUE);
        if (isset($rst['status']) && 0 == $rst['status']) {
            echo $key . ' create success <br>';
            return TRUE;
        }
        echo $key . ' create fail <br>';
        return FALSE;
    }
    
    
    /**
     * 修改列
     */
    public function updateColumn()
    {
        $url = "http://api.map.baidu.com/geodata/v3/column/update";
        $uri = '/geodata/v3/column/update';
        $post_arrays = array (
            'id' => 0,
            //'is_sortfilter_field' => 1,
            //'is_index_field' => 1,
            //'is_unique_field' => 1,
            'geotable_id' => $this->geotable_id,
            'ak' => $this->ak,
        );
        $sn = $this->caculateAKSN($this->ak, $this->sk, $uri, $post_arrays, 'POST');
        $post_arrays['sn'] = $sn;
        $result_str = $this->postrequest($url, $post_arrays);
        echo $result_str;
        //$rst = json_decode($result_str, TRUE);
        //if (isset($rst['status']) && 0 == $rst['status']) {
        //    echo 'column update success <br>';
        //    return TRUE;
        //}
        //echo 'column update fail <br>';
        //return FALSE;
    }
    
    
    /**
     * 删除列
     */
    public function deleteColumn()
    {
        $url = "http://api.map.baidu.com/geodata/v3/column/delete";
        $uri = '/geodata/v3/column/delete';
        $post_arrays = array (
            'id' => 0,
            'geotable_id' => $this->geotable_id,
            'ak' => $this->ak,
        );
        $sn = $this->caculateAKSN($this->ak, $this->sk, $uri, $post_arrays, 'POST');
        $post_arrays['sn'] = $sn;
        $result_str = $this->postrequest($url, $post_arrays);
        echo $result_str;
        //$rst = json_decode($result_str, TRUE);
        //if (isset($rst['status']) && 0 == $rst['status']) {
        //    echo 'column update success <br>';
        //    return TRUE;
        //}
        //echo 'column update fail <br>';
        //return FALSE;
    }




    /**
     * 查看位置数据列表
     */
    public function listPoi($page_index = 0)
    {
        $url = "http://api.map.baidu.com/geodata/v3/poi/list";
        $url .= "?geotable_id=%s";
        $url .= "&page_index=%s";
        $url .= "&page_size=%s";
        $url .= "&ak=%s";
        $url .= "&sn=%s";
        $uri = '/geodata/v3/poi/list';
        $geotable_id = $this->geotable_id;
        $query_arrays = array (
            'geotable_id' => $this->geotable_id,
            'page_index' => $page_index,
            'page_size' => 200,
            'ak' => $this->ak,
        );
        $sn = $this->caculateAKSN($this->ak, $this->sk, $uri, $query_arrays, 'GET');
        $target = sprintf(
                $url, 
                $geotable_id, 
                $page_index, 
                200, 
                $this->ak, 
                $sn
                );
        $result_str = $this->request($target);
        echo $result_str;
    }


    /**
     * 查看指定act_id的位置数据
     */
    public function detailPoi($actId) 
    {
        $url = "http://api.map.baidu.com/geodata/v3/poi/detail";
        $url .= "?act_id=%s";
        $url .= "&geotable_id=%s";
        $url .= "&ak=%s";
        $url .= "&sn=%s";
        $uri = '/geodata/v3/poi/detail';
        //$act_id = $actId . ',' . $actId;
        $act_id = $actId;
        $geotable_id = $this->geotable_id;
        $query_arrays = array (
            'act_id' => $act_id,
            'geotable_id' => $this->geotable_id,
            'ak' => $this->ak,
        );
        $sn = $this->caculateAKSN($this->ak, $this->sk, $uri, $query_arrays, 'GET');
        $target = sprintf(
                $url, 
                //urlencode($act_id), 
                $act_id, 
                $geotable_id, 
                $this->ak, 
                $sn
                );
        $result_str = $this->request($target);
        //echo $result_str . '<br>';
        $rst = json_decode($result_str, TRUE);
        if (isset($rst['status']) && 0 == $rst['status']) {
            return $rst;
        }
        return FALSE;
    }
    

    /**
     * 创建位置数据poi
     */
    public function createPoi(
            $title, 
            $address, 
            $tags, 
            $latitude, 
            $longitude, 
            $coord_type,
            array $act_k_vs)
    {
        $url = "http://api.map.baidu.com/geodata/v3/poi/create";
        $uri = '/geodata/v3/poi/create';
        $post_arrays = array();
        if (!empty($title)) {
            $post_arrays['title'] = $title;
        }
        if (!empty($title)) {
            $post_arrays['address'] = $address;
        }
        if (!empty($title)) {
            $post_arrays['tags'] = $tags;
        }
        if (!empty($title)) {
            $post_arrays['latitude'] = $latitude;
        }
        if (!empty($title)) {
            $post_arrays['longitude'] = $longitude;
        }
        if (!empty($title)) {
            $post_arrays['coord_type'] = $coord_type;
        }
        $post_arrays['geotable_id'] = $this->geotable_id;
        $post_arrays['ak'] = $this->ak;
        if (!empty($act_k_vs)) {
            foreach ($act_k_vs as $k => $v) {
                $post_arrays[$k] = $v;
            }
        }
        $sn = $this->caculateAKSN($this->ak, $this->sk, $uri, $post_arrays, 'POST');
        $post_arrays['sn'] = $sn;
        $result_str = $this->postrequest($url, $post_arrays);
        $rst = json_decode($result_str, TRUE);
        if (isset($rst['status']) && 0 == $rst['status']) {
            //echo 'act_id:' . $act_k_vs['act_id'] . ' create success <br>';
            return TRUE;
        }
        //echo $result_str . '<br>';
        //echo 'act_id:' . $act_k_vs['act_id'] . ' create fail <br>';
        return FALSE;
    }
    
    
    /**
     * 修改位置数据poi
     */
    public function updatePoi(
            $actId,
            $title, 
            $address, 
            $tags, 
            $latitude, 
            $longitude, 
            $coord_type,
            array $act_k_vs)
    {
        $url = "http://api.map.baidu.com/geodata/v3/poi/update";
        $uri = '/geodata/v3/poi/update';
        $post_arrays = array();
        $post_arrays['act_id'] = $actId;
        if (!empty($title)) {
            $post_arrays['title'] = $title;
        }
        if (!empty($title)) {
            $post_arrays['address'] = $address;
        }
        if (!empty($title)) {
            $post_arrays['tags'] = $tags;
        }
        if (!empty($title)) {
            $post_arrays['latitude'] = $latitude;
        }
        if (!empty($title)) {
            $post_arrays['longitude'] = $longitude;
        }
        if (!empty($title)) {
            $post_arrays['coord_type'] = $coord_type;
        }
        $post_arrays['geotable_id'] = $this->geotable_id;
        $post_arrays['ak'] = $this->ak;
        if (!empty($act_k_vs)) {
            foreach ($act_k_vs as $k => $v) {
                $post_arrays[$k] = $v;
            }
        }
        $sn = $this->caculateAKSN($this->ak, $this->sk, $uri, $post_arrays, 'POST');
        $post_arrays['sn'] = $sn;
        $result_str = $this->postrequest($url, $post_arrays);
        $rst = json_decode($result_str, TRUE);
        if (isset($rst['status']) && 0 == $rst['status']) {
            //echo 'act_id:' . $actId . ' update success <br>';
            return TRUE;
        }
        //echo $result_str . '<br>';
        //echo 'act_id:' . $actId . ' update fail <br>';
        return FALSE;
    }
    
    
    /**
     * 删除位置数据poi
     */
    public function deletePoi($actId)
    {
        $url = "http://api.map.baidu.com/geodata/v3/poi/delete";
        $uri = '/geodata/v3/poi/delete';
        $post_arrays = array();
        $post_arrays['act_id'] = $actId;
        $post_arrays['geotable_id'] = $this->geotable_id;
        $post_arrays['ak'] = $this->ak;
        $sn = $this->caculateAKSN($this->ak, $this->sk, $uri, $post_arrays, 'POST');
        $post_arrays['sn'] = $sn;
        $result_str = $this->postrequest($url, $post_arrays);
        $rst = json_decode($result_str, TRUE);
        if (isset($rst['status']) && 0 == $rst['status']) {
            //echo 'act_id:' . $actId . ' delete success <br>';
            return TRUE;
        }
        //echo $result_str . '<br>';
        //echo 'act_id:' . $actId . ' delete fail <br>';
        return FALSE;
    }
    
    
    /**
     * url请求
     * 
     * @param type $target_url
     */
    public function request($target_url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $target_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $result_str = curl_exec($ch);
        curl_close($ch);
        return $result_str;
    }
    
    
    /**
     * post类型url请求
     * 
     * @param type $target_url
     * @param array $postData
     */
    public function postrequest($target_url, array $postData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $target_url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $result_str = curl_exec($ch);
        curl_close($ch);
        return $result_str;
    }


    /**
     * sn计算函数
     * 
     * @param type $ak
     * @param type $sk
     * @param type $url
     * @param type $querystring_arrays
     * @param type $method
     */
    function caculateAKSN($ak, $sk, $url, $querystring_arrays, $method = 'GET')
    {  
        if ($method === 'POST'){  
            ksort($querystring_arrays);  
        }  
        $querystring = http_build_query($querystring_arrays);  
        return md5(urlencode($url.'?'.$querystring.$sk));  
    }
    
}

?>
