<?php

class ActMoreController extends ActController 
{

    public function filters() 
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + sendMsg_v2, createAlbum_v2, addPhoto_v2, delPhoto_v2, enroll_v2, checkin_v2', // we only allow deletion via POST request
        );
    }

    
    public function accessRules() 
    {
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('info_v2', 'modules_v2', 'moreInfo_v2', 'process_v2', 'menus_v2', 'attentions_v2', 'notices_v2', 'addrs_v2', 'messages_v2', 'albums_v2', 'photoes_v2', 'videos_v2', 'members_v2', 'enrollCusKeys_v2', 'userCusKeys_v2', 'enrollActs_v2'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    
    /**
     * 活动详情
     */
    public function actionInfo_v2() 
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
            ), 
            array(
                array('actId', 'required'),
                array('actId', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $act = ActInfo::model()->fullProfile(NULL, $ck->actId, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        //$act['routes'] = ActRoute::model()->getActRoutesBasic($ck->actId);
        Yii::app()->res->output(Error::NONE, 'act info success', array('act' => $act));
    }

    
    /**
     * 活动板块
     */
    public function actionModules_v2() 
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
            ), array(
                array('actId', 'required'),
                array('actId', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $rst = ActInfoExtend::model()->modules($ck->actId);
        $rst['id'] = $ck->actId;
        Yii::app()->res->output(Error::NONE, 'act modules success', array('act_modules' => $rst));
    }

    
    /**
     * 活动信息
     */
    public function actionMoreInfo_v2() 
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
            ), array(
                array('actId', 'required'),
                array('actId', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $act = ActInfo::model()->findByPk($ck->actId);
        $rst = ActInfoExtend::model()->moreInfo($ck->actId);
        $rst['id'] = $ck->actId;
        
        if (!Yii::app()->user->isGuest) {
            $albumId = ActAlbumUserMap::model()->albumId($ck->actId, Yii::app()->user->id);
            $rst['album_id'] = empty($albumId) ? -1 : $albumId;
            
            $statusRst = ActEnroll::model()->userStatus($ck->actId, Yii::app()->user->id);
            $rst = array_merge($rst, $statusRst);
            if (empty($act) || empty($act->org_id)) {
                $rst['is_manager'] = 0;
            }  else {
                $rst['is_manager'] = OrgManagerMap::model()->isManager($act->org_id, Yii::app()->user->id) ? 1 : 0;
            }
        }

        $rst['enroll_num'] = ActEnroll::model()->enrollPeopleNum($ck->actId);
        $rst['enroll_male_num'] = ActEnroll::model()->enrollPeopleNum($ck->actId, 1);
        $rst['enroll_female_num'] = ActEnroll::model()->enrollPeopleNum($ck->actId, 2);
        
        Yii::app()->res->output(Error::NONE, 'act more info success', array('act_info' => $rst));
    }

    
    /**
     * 活动流程
     */
    public function actionProcess_v2() 
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ), array(
                array('actId, page, size', 'required'),
                array('actId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $rst = ActProcess::model()->process($ck->actId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'act process success', $rst);
    }

    
    /**
     * 活动菜单
     */
    public function actionMenus_v2() 
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'type' => Yii::app()->request->getParam('type'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ), array(
                array('actId, type, page, size', 'required'),
                array('actId, page, size', 'numerical', 'integerOnly' => true),
                array('type', 'in', 'range' => array(1, 2)),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $rst = ActMenu::model()->menus($ck->actId, $ck->type, $ck->page, $ck->size);
        $info = ActInfoExtend::model()->getMenuInfo($ck->actId);
        if (1 == $ck->type) {
            $rst['time'] = empty($info) ? NULL : $info['lunch_time'];
            $rst['addr'] = empty($info) ? NULL : $info['lunch_addr'];
        }  else {
            $rst['time'] = empty($info) ? NULL : $info['supper_time'];
            $rst['addr'] = empty($info) ? NULL : $info['supper_addr'];
        }
        
        Yii::app()->res->output(Error::NONE, 'act menus success', $rst);
    }

    
    /**
     * 活动注意事项
     */
    public function actionAttentions_v2() 
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ), array(
                array('actId, page, size', 'required'),
                array('actId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $rst = ActAttention::model()->attentions($ck->actId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'act attentions success', $rst);
    }

    
    /**
     * 最新通知
     */
    public function actionNotices_v2() 
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ), array(
                array('actId, page, size', 'required'),
                array('actId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $rst = ActNotice::model()->notices($ck->actId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'act notices success', $rst);
    }

    
    /**
     * 活动地点
     */
    public function actionAddrs_v2() 
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ), array(
                array('actId, page, size', 'required'),
                array('actId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $rst = ActLocation::model()->locations($ck->actId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'act addrs success', $rst);
    }

    
    /**
     * 活动的参与者留言
     */
    public function actionMessages_v2() 
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ), array(
                array('actId, page, size', 'required'),
                array('actId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $rst = ActLeaveMsg::model()->messages($ck->actId, $ck->page, $ck->size, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'act leave msgs success', $rst);
    }

    
    /**
     * 发活动留言
     */
    public function actionSendMsg_v2() 
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getPost('actId'),
                'content' => Yii::app()->request->getPost('content'),
            ), array(
                array('actId', 'required'),
                array('actId', 'numerical', 'integerOnly' => true),
                array('content', 'CZhEnV', 'max' => 512, 'isDiff' => TRUE),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $act = ActInfo::model()->findByPk($ck->actId);

        //验证自己是否为管理员
        $isManager = 0;
        if (!empty($act) && !empty($act->org_id)) {
            $isManager = OrgManagerMap::model()->isManager($act->org_id, Yii::app()->user->id) ? 1 : 0;
        }
       
        if (!$isManager) {
            $r = ActEnroll::model()->isMember($ck->actId, Yii::app()->user->id);
            if (!$r) {
                return Yii::app()->res->output(Error::PERMISSION_DENIED, 'act user not member');
            }
        }

        $rst = ActLeaveMsg::model()->add($ck->actId, Yii::app()->user->id, $ck->content, $isManager);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'act send msg success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act send msg fail');
    }

    
    /**
     * 活动相册
     */
    public function actionAlbums_v2() 
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'cityId' => Yii::app()->request->getParam('cityId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ), array(
                array('actId, cityId, page, size', 'required'),
                array('actId, cityId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $busiAlbum = ActAlbum::model()->busiImgAlbum($ck->actId);
        $busiAlbumV = ActAlbum::model()->busiVideoAlbum($ck->actId);
        $rst = ActAlbum::model()->userImgAlbums($ck->actId, $ck->page, $ck->size, $ck->cityId, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);

        if (!Yii::app()->user->isGuest) {
            $albumId = ActAlbumUserMap::model()->albumId($ck->actId, Yii::app()->user->id);
            $rst['my_album_id'] = empty($albumId) ? -1 : $albumId;
        }
        
        $rst['busi_photo'] = $busiAlbum;
        $rst['busi_video'] = $busiAlbumV;
        Yii::app()->res->output(Error::NONE, 'act albums success', $rst);
    }

    
    /**
     * 相册的图片
     */
    public function actionPhotoes_v2() 
    {
        $ck = Rules::instance(
            array(
                'albumId' => Yii::app()->request->getParam('albumId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ), array(
                array('albumId, page, size', 'required'),
                array('albumId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $ownerId = ActAlbumUserMap::model()->ownerId($ck->albumId);
        $isSelf = FALSE;
        if (!Yii::app()->user->isGuest && $ownerId == Yii::app()->user->id) {
            $isSelf = TRUE;
        }

        $rst = ActAlbumImgMap::model()->photos($ck->albumId, $ck->page, $ck->size, $isSelf);
        Yii::app()->res->output(Error::NONE, 'act album photoes success', $rst);
    }
    
    
    /**
     * 相册的视频
     */
    public function actionVideos_v2() 
    {
        $ck = Rules::instance(
            array(
                'albumId' => Yii::app()->request->getParam('albumId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ), array(
                array('albumId, page, size', 'required'),
                array('albumId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $rst = ActAlbumVideoMap::model()->videos($ck->albumId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'act album videos success', $rst);
    }
    

    /**
     * 创建相册
     */
    public function actionCreateAlbum_v2() 
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getPost('actId'),
                'subject' => Yii::app()->request->getPost('subject'),
            ), array(
                array('actId', 'required'),
                array('actId', 'numerical', 'integerOnly' => true),
                array('subject', 'CZhEnV', 'max' => 64, 'isDiff' => TRUE),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        //是否是活动成员
        $r = ActEnroll::model()->isMember($ck->actId, Yii::app()->user->id);
        if (!$r) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'act user not member');
        }
        
        //是否已创建过相册
        $albumId = ActAlbumUserMap::model()->albumId($ck->actId, Yii::app()->user->id);
        if (!empty($albumId)) {
            return Yii::app()->res->output(Error::RECORD_HAS_EXIST, 'act user album has exists', array('id' => $albumId));
        }
        
        $model = new ActAlbumUserMap();
        $rst = ActAlbumUserMap::model()->createUserAlbum($ck->actId, Yii::app()->user->id, $ck->subject, $model);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'act create album success', array('id' => $model->album_id));
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act create album fail');
    }

    
    /**
     * 添加相册图片
     */
    public function actionAddPhoto_v2() 
    {
        $ck = Rules::instance(
            array(
                'albumId' => Yii::app()->request->getPost('albumId'),
                'imgIds' => Yii::app()->request->getPost('imgIds', array()),
            ), array(
                array('albumId', 'required'),
                array('albumId', 'numerical', 'integerOnly' => true),
                array('imgIds', 'CArrNumV', 'maxLen' => 9),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $actId = ActAlbum::model()->actId($ck->albumId);
        
        //是否是活动成员
        $r = ActEnroll::model()->isMember($actId, Yii::app()->user->id);
        if (!$r) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'act user not member');
        }
        
        //是否创建过相册且操作的是自己的相册
        $albumId = ActAlbumUserMap::model()->albumId($actId, Yii::app()->user->id);
        if (empty($albumId) || $ck->albumId != $albumId) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'act not album of user', array('id' => $albumId));
        }
        
        //$model = new ActAlbumImgMap();
        //$rst = ActAlbumImgMap::model()->add($ck->albumId, $ck->imgId, $model);
        //if ($rst) {
        //    return Yii::app()->res->output(Error::NONE, 'act add album photo success');
        //}
        //Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act add album photo fail');
        foreach ($ck->imgIds as $v) {
            ActAlbumImgMap::model()->add($ck->albumId, $v);
        }
        Yii::app()->res->output(Error::NONE, 'act add album photoes success');
    }
    

    /**
     * 删除相册图片
     */
    public function actionDelPhoto_v2() 
    {
        $ck = Rules::instance(
            array(
                'albumId' => Yii::app()->request->getPost('albumId'),
                'imgIds' => Yii::app()->request->getPost('imgIds', array()),
            ), array(
                array('albumId', 'required'),
                array('albumId', 'numerical', 'integerOnly' => true),
                array('imgIds', 'CArrNumV', 'maxLen' => 9),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $actId = ActAlbum::model()->actId($ck->albumId);
                
        //是否是活动成员
        $r = ActEnroll::model()->isMember($actId, Yii::app()->user->id);
        if (!$r) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'act user not member');
        }
        
        //是否创建过相册且操作的是自己的相册
        $albumId = ActAlbumUserMap::model()->albumId($actId, Yii::app()->user->id);
        if (empty($albumId) || $ck->albumId != $albumId) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'act not album of user', array('id' => $albumId));
        }
        
        //$rst = ActAlbumImgMap::model()->del($ck->albumId, $ck->imgId);
        //if ($rst) {
        //    return Yii::app()->res->output(Error::NONE, 'act album del photo success');
        //}
        //Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act album del photo fail');
        foreach ($ck->imgIds as $v) {
            ActAlbumImgMap::model()->del($ck->albumId, $v);
        }
        Yii::app()->res->output(Error::NONE, 'act album del photoes success');
    }

    
    /**
     * 成员列表
     */
    public function actionMembers_v2() 
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'cityId' => Yii::app()->request->getParam('cityId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ), array(
                array('actId, cityId, page, size', 'required'),
                array('actId, cityId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $rst = ActEnroll::model()->members($ck->actId, $ck->page, $ck->size, $ck->cityId, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'act members success', $rst);
    }

    
    /**
     * 分组信息
     */
    public function actionGroup_v2() 
    {
        $ck = Rules::instance(
            array(
                'groupId' => Yii::app()->request->getParam('groupId'),
            ), array(
                array('groupId', 'required'),
                array('groupId', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $group = ActGroup::model()->profile($ck->groupId);
        Yii::app()->res->output(Error::NONE, 'act group success', array('group' => $group));
    }

    
    /**
     * 分组成员列表
     */
    public function actionGroupUsers_v2() 
    {
        $ck = Rules::instance(
            array(
                'groupId' => Yii::app()->request->getParam('groupId'),
                'cityId' => Yii::app()->request->getParam('cityId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ), array(
                array('groupId, cityId, page, size', 'required'),
                array('groupId, cityId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $rst = ActEnroll::model()->groupUsers($ck->groupId, $ck->page, $ck->size, $ck->cityId, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'act group users success', $rst);
    }


    /**
     * 查看活动报名自定义字段
     */
    public function actionEnrollCusKeys_v2() 
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ), array(
                array('actId, page, size', 'required'),
                array('actId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $rst = CustomExtActMap::model()->customKeys($ck->actId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'act enroll custom keys success', $rst);
    }
    
    
    /**
     * 用户查看自己的自定义字段和值
     */
    public function actionUserCusKeys_v2() 
    {
        $ck = Rules::instance(
            array(
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ), array(
                array('page, size', 'required'),
                array('page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $rst = CustomExtUserVal::model()->userCustomKeys(Yii::app()->user->id, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'user custom keys success', $rst);
    }
    
    
    /**
     * 活动报名
     */
    public function actionEnroll_v2()
    {
        $ck = Rules::instance(
            array(
                'act_id' => Yii::app()->request->getPost('actId'),
                'name' => Yii::app()->request->getPost('name'),
                'sex' => Yii::app()->request->getPost('sex'),
                'birth' => Yii::app()->request->getPost('birth'),
                'phone' => Yii::app()->request->getPost('phone'),
                'with_people_num' => Yii::app()->request->getPost('withPeopleNum'),
                'lon' => Yii::app()->request->getPost('lon'),
                'lat' => Yii::app()->request->getPost('lat'),
                'address' => Yii::app()->request->getPost('address'),
                'customKeys' => Yii::app()->request->getPost('customKeys'),
            ), array(
                array('act_id', 'required'),
                array('act_id, with_people_num', 'numerical', 'integerOnly' => true),
                array('name', 'CZhEnV', 'max' => 32, 'isDiff' => TRUE),
                array('sex', 'in', 'range' => array(1, 2)),
                array('birth', 'date', 'format' => 'yyyy-mm-dd'),
                array('phone', 'length', 'max' => 32),
                array('lon, lat', 'numerical', 'integerOnly' => FALSE),
                array('address', 'CZhEnV', 'max' => 64, 'isDiff' => TRUE),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        if (!empty($ck->customKeys)) {
            if (!is_array($ck->customKeys)) {
                return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
            }
            foreach ($ck->customKeys as $v) {
                if (!empty($v->id) || !empty($v->value)) {
                    return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
                }
            }
        }
        
        //活动是否存在且报名有效
        $act = ActInfo::model()->findByPk($ck->act_id);
        if (empty($act)) {
            return Yii::app()->res->output(Error::RECORD_NOT_EXIST, 'act not exist');
        }
        if (ConstActStatus::PUBLISHING != $act->status) {
            return Yii::app()->res->output(Error::RECORD_NOT_EXIST, 'act not allow to enroll');
        }
        
        $actExtend = ActInfoExtend::model()->find('t.act_id=:actId', array(':actId' => $act->id));
        if (empty($actExtend)) {
            return Yii::app()->res->output(Error::RECORD_NOT_EXIST, 'act extend not exist');
        }
        
        //报名人数是否有效
        if (1 == $actExtend->enroll_limit) {
            $commitingAndHasNum = ActEnroll::model()->commitingNum($act->id, NULL);
            if ($commitingAndHasNum >= $actExtend->enroll_limit_num) {
                return Yii::app()->res->output(Error::ACT_ENROLL_TOO_NUM, 'act extend member num not allow to enroll');
            }
        }
        
        //报名时间是否有效
        if (time() < strtotime($actExtend->enroll_b_time) || time() > strtotime($actExtend->enroll_e_time)) {
            return Yii::app()->res->output(Error::ACT_ENROLL_TIME_ERROR, 'act extend not allow to enroll');
        }
        
        //活动对应的社团所有者不能参加
        if (!empty($act->org_id)) {
            $org = OrgInfo::model()->findByPk($act->org_id);
            if (!empty($org) && $org->own_id == Yii::app()->user->id) {
                return Yii::app()->res->output(Error::PERMISSION_DENIED, 'you can not allow to enroll this act');
            }
        }
        
        $isNew = FALSE;
        $model = ActEnroll::model()->get($ck->act_id, Yii::app()->user->id);
        if (empty($model)) {
            $model = new ActEnroll();
            $isNew = TRUE;
        }  else {
            //已报名且提交审核或已处理的不能重复报名
            if (ConstCheckStatus::DELETE != $model->status && ConstCheckStatus::NOT_COMMIT != $model->status) {
                return Yii::app()->res->output(Error::RECORD_HAS_EXIST, 'enroll has exist');
            }
        }
        
        $ck->setModelAttris($model);
        $model->u_id = Yii::app()->user->id;
        
        //查看该活动报名是否需要审核 需要：提交审核，不需要：直接通过
        //查看该活动为付费类型还是非付费类型
        //付费类型：报名不提交审核；非付费类型：按报名审核要求提交审核或直接通过
        $actMore = ActInfoExtend::model()->get($ck->act_id);
        if (1 == $actMore->show_pay) {
            //需要支付的
            $model->status = ConstCheckStatus::NOT_COMMIT;
        }  else {
            if (1 == $actMore->show_verify) {
                //无需支付，需审核
                $model->status = ConstCheckStatus::COMMIT;
            }  else {
                //无需支付且无需审核
                $model->status = ConstCheckStatus::PASS;
            }
        }
        
        $r = FALSE;
        if ($isNew) {
            $r = $model->add();
        }  else {
            $r = $model->up();
        }
        
        if (!$r) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act enroll fail');
        }
        
        if (!empty($ck->customKeys)) {
            foreach ($ck->customKeys as $v) {
                CustomExtActUserVal::model()->up($v['id'], $ck->act_id, Yii::app()->user->id, $v['value']);
                CustomExtUserVal::model()->up($v['id'], Yii::app()->user->id, $v['value']);
            }
        }
        
        Yii::app()->res->output(Error::NONE, 'act enroll success');
    }

    
    /**
     * 获取某活动已签到列表（可用于生成通行证）
     */
    public function actionCheckins_v2() 
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ), array(
                array('actId, page, size', 'required'),
                array('actId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $rst = ActCheckinUserMap::model()->userCheckins($ck->actId, Yii::app()->user->id, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'act user checkins success', $rst);
    }
    
    
    /**
     * 活动签到
     */
    public function actionCheckin_v2()
    {
        $ck = Rules::instance(
            array(
                'checkinId' => Yii::app()->request->getPost('checkinId'),
                'lon' => Yii::app()->request->getPost('lon'),
                'lat' => Yii::app()->request->getPost('lat'),
                'address' => Yii::app()->request->getPost('address'),
            ), array(
                array('checkinId', 'required'),
                array('checkinId', 'numerical', 'integerOnly' => true),
                array('lon, lat', 'numerical', 'integerOnly' => FALSE),
                array('address', 'CZhEnV', 'max' => 64, 'isDiff' => TRUE),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        //得到此签到码的活动id
        $actId = ActCheckinStep::model()->actId($ck->checkinId);
        if (empty($actId)) {
            return Yii::app()->res->output(Error::RECORD_NOT_EXIST, 'act not exist');
        }
        
        //是否是活动成员
        $r = ActEnroll::model()->isMember($actId, Yii::app()->user->id);
        if (!$r) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'act user not member');
        }
        
        $valid = ActCheckinUserMap::model()->valid($actId, Yii::app()->user->id, $ck->checkinId);
        if (!$valid) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'not allow to checkin');
        }
        
        $rst = ActCheckinUserMap::model()->checkin($ck->checkinId, Yii::app()->user->id, $ck->lon, $ck->lat, $ck->address);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'act checkin success', array('checkin' => $rst));
        }
        return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act checkin fail');
    }
    
    
    /**
     * 活动签到确认
     */
    public function actionSureCheckin_v2()
    {
        $ck = Rules::instance(
            array(
                'checkinId' => Yii::app()->request->getPost('checkinId'),
            ), array(
                array('checkinId', 'required'),
                array('checkinId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = ActCheckinUserMap::model()->get($ck->checkinId, Yii::app()->user->id);
        if (empty($model) || ConstStatus::DELETE == $model->status) {
            return Yii::app()->res->output(Error::RECORD_NOT_EXIST, 'act checkin not exists');
        }
        
        $rst = ActCheckinUserMap::model()->sureCheckin($ck->checkinId, Yii::app()->user->id);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'act sure checkin success');
        }
        return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act sure checkin fail');
    }
    
    
    /**
     * 场地平面图
     */
    public function actionPlaceImgs_v2() 
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ), array(
                array('actId, page, size', 'required'),
                array('actId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $rst = ActPlaceImg::model()->placeImgs($ck->actId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'act place imgs success', $rst);
    }
    
    
    /**
     * 管理员列表
     */
    public function actionManagers_v2() 
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'cityId' => Yii::app()->request->getParam('cityId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ), array(
                array('actId, cityId, page, size', 'required'),
                array('actId, cityId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $act = ActInfo::model()->findByPk($ck->actId);
        if (empty($act) || empty($act->org_id)) {
            return Yii::app()->res->output(Error::RECORD_NOT_EXIST, 'act not exist');;
        }
        
        $rst = OrgManagerMap::model()->managers($act->org_id, $ck->page, $ck->size, $ck->cityId, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'act org managers success', $rst);
    }
    
    
    /**
     * 报名的活动列表
     */
    public function actionEnrollActs_v2()
    {
        $ck = Rules::instance(
            array(
                'uid' => Yii::app()->request->getParam('uid', Yii::app()->user->id),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ), array(
                array('uid, page, size', 'required'),
                array('uid, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        if (empty($ck->uid)) {
            return Yii::app()->res->output(Error::LOGIN_INVALID, 'not uid');
        }
        
        $rst = ActEnroll::model()->userEnrolls($ck->uid, $ck->page, $ck->size, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'enroll acts success', $rst);
    }
    

    /**
     * 查看主办方介绍的的网页
     */
    public function actionBusiweb() 
    {
        $this->layout = '//layouts/blank';
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
            ), array(
                array('actId', 'required'),
                array('actId', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, "Invalid");
            //throw new CHttpException(404, '未找到活动');
        }

        $model = ActInfo::model()->fullProfile(NULL, $ck->actId);
        if (empty($model)) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, 'Not found');
            //throw new CHttpException(404, '未找到活动内容');
        }

        //$this->render('shareweb', array('act' => $model));
    }

}
