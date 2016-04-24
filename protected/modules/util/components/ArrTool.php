<?php

class ArrTool {
    
    /**
     * 根据分页参数从数组中取出一部分数组
     * @param type $arr
     * @param type $page
     * @param type $size
     */
    public static function sliceByPageAndSize(array $arr, $page, $size) 
    {
        $start = ($page - 1) * $size;
        $count = count($arr);
        if ($count > $start) {
            return array_slice($arr, $start, $size);
        }  else {
            return array();
        }
    }
    
    
    /**
     * 对整形数组进行去重，升序排序，转化为glue分隔的字符串
     * @param array $arr
     * @param type $glue
     */
    public static function uniqueAscStr(array $arr, $glue = NULL) 
    {
        //去重
        $uniqueArr = array_unique($arr);
        
        if (empty($uniqueArr)) {
            return '';
        }
        //升序排序，glue分隔
        sort($uniqueArr);
        if (empty($glue)) {
            return $uniqueArr;
        }
        return implode($glue, $uniqueArr);
    }
    
    
    /**
     * 分隔冒号间隔的字符串为key、value数组
     * key:名称:参数1:值1:参数2:值2
     * key:sltactsbytags:tag:1,2,3,4
     * 
     * @param type $colonStr
     */
    public static function explodeColonKv($colonStr) 
    {
        $kvArr = array();
        $rst = explode(':', $colonStr);
        for ($i = 0; $i < count($rst); $i++) {
            $kvArr[$rst[$i]] = $rst[$i + 1];
            $i++;
        }
        return $kvArr;
    }
    
    
    /**
     * 将指定key的key、value数组生成冒号隔开的字符串
     * @param type $colonArr
     */
    public static function implodeColonArr(array $arr) 
    {
        $rstArr = array();
        foreach ($arr as $k => $v) {
            array_push($rstArr, $k);
            array_push($rstArr, $v);
        }
        return implode(':', $rstArr);
    }
    
    
    /**
     * 将value为数字的kv数组转化为按k升序排序的索引为数字的数组
     */
    public static function toNumArr(array $kVs)
    {   
        if (empty($kVs)) {
            return array();
        }
        ksort($kVs);
        return array_values($kVs);
    }
    
}

?>
