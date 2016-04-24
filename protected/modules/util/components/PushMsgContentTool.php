<?php

class PushMsgContentTool {
    
    public static function makeActEnroll($focusName, $actName)
    {
        return array(
            'title' => '求跟随',
            'descri' => '你关注的' . $focusName . '将在' . $actName . '活动中现身，快来报名',
        );
    }
    
    
    public static function makeActCheckin($focusName)
    {
        return array(
            'title' => '求偶遇',
            'descri' => '你关注的' . $focusName . '已在此活动现场签到，快来寻找TA',
        );
    }
        
    public static function makeOrgActEnroll($actName, $msg)
    {
        return array(
            'title' => $actName,
            'descri' => $msg,
        );
    }
    
    public static function makeUserReply($sendName, $atName, $content)
    {
        $dealContent;
        if (mb_strlen($content) > 5) {
            $dealContent = mb_substr($content , 0 , 5) . '...';
        }  else {
            $dealContent = $content;
        }
        
        return array(
            'title' => '收到了新的回复',
            'descri' => $sendName . '回复' . $atName . '：' . $dealContent,
        );
    }
    
    
    public static function makeFilterForAct($actId)
    {
        return array(
            'filter_k_id' => ConstPushMsgFilterType::ACT,
            'filter_v_id' => $actId,
        );
    }
    
    
    public static function makeFilterForNews($newsId)
    {
        return array(
            'filter_k_id' => ConstPushMsgFilterType::NEWS,
            'filter_v_id' => $newsId,
        );
    }
    
    
    public static function makeFilterForUser($uid)
    {
        return array(
            'filter_k_id' => ConstPushMsgFilterType::USER,
            'filter_v_id' => $uid,
        );
    }
    
    
    public static function makeFilterForDynamic($dynamicId)
    {
        return array(
            'filter_k_id' => ConstPushMsgFilterType::DYNAMIC,
            'filter_v_id' => $dynamicId,
        );
    }
    
}

?>
