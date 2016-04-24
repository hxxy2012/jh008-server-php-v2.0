<?php

class ActMoreController extends OrgController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + addAlbumImg, delAlbumImg, modifyGroup', // we only allow deletion via POST request
		);
	}


	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('Enrolls', 'Verify', 'ManualEnroll', 'SendMsg','Members','CheckinCodes', 'AddCode','ModifyCode','DelCode'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}


    /**
     * 活动报名列表
     */
    public function actionEnrolls()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'type' => Yii::app()->request->getParam('type'),
            ),
            array(
                array('actId, type', 'required'),
                array('actId, type', 'numerical', 'integerOnly' => FALSE),
            )
        );

        if (!$ck->validate()){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $ret = array();
        $list = ActEnroll::model()->getEnrollList($ck->actId, $ck->type);
        if($list){
            $ret['custom_keys'] = $list['custom_keys'];
            $ret['enrolls'] = $list['enroll_list'];
             $actExtInfo = ActInfoExtendO::model()->getByActIdO($ck->actId);
             if($actExtInfo){
                $ret['enroll_b_time'] = $actExtInfo->attributes['enroll_b_time'];
                $ret['enroll_e_time'] = $actExtInfo->attributes['enroll_e_time'];
             }
        }

        Yii::app()->res->output(Error::NONE, 'success', $ret);
    }


    /**
     * 活动报名审核
     * enrollId 活动报名id
     * type 1通过、2拒绝
     */
    public function actionVerify()
    {
        $ck = Rules::instance(
            array(
                'enrollId' => Yii::app()->request->getParam('enrollId'),
                'type' => Yii::app()->request->getParam('type'),
            ),
            array(
                array('enrollId, type', 'required'),
                array('enrollId', 'numerical', 'integerOnly' => FALSE),
                array('type', 'in', 'range' => array(1, 2)),
            )
        );

        if (!$ck->validate()){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $status = ActEnrollO::model()->setEnrollState($ck->enrollId, $ck->type == 1 ? ConstCheckStatus::PASS : ConstCheckStatus::REFUSE);
        //var_dump($status);

        if($status){
            return Yii::app()->res->output(Error::NONE, 'success', $status);
        }

        return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'failed', $status);
    }


    /**
     * 活动手动添加报名
     * actId	int	活动id
     * realName	String	姓名
     * contactPhone	String	联系电话
     */
    public function actionManualEnroll()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'realName' => Yii::app()->request->getParam('realName'),
                'contactPhone' => Yii::app()->request->getParam('contactPhone'),
                'sex' => Yii::app()->request->getParam('sex'),
                'age' => Yii::app()->request->getParam('age'),
                'customFields' => Yii::app()->request->getParam('customFields'),
            ),
            array(
                array('actId,realName,contactPhone,sex,age', 'required'),
                array('actId,sex,age', 'numerical', 'integerOnly' => FALSE),
                array('realName', 'CZhEnV', 'min' => 1, 'max' => 24),
                array('contactPhone', 'CPhoneNumberV'),
                array('sex', 'in', 'range' => array(0, 1, 2)),
            )
        );
        if (!$ck->validate()){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        //临时的uid,为负数
        //$guid = intval(round(microtime(true) * -1000));
        $guid = time()*(-1);
        //var_dump($guid);
        //插入报名表
        $model = new ActEnroll();
        $model->u_id = $guid;
        $model->act_id = $ck->actId;
        $model->name = $ck->realName;
        $model->sex = $ck->sex;
        $model->birth = date('Y-m-d H:i:s', strtotime('-' . $ck->age . ' year'));
        $model->phone = $ck->contactPhone;
        $model->status = ConstCheckStatus::PASS;
        $model->create_time = date('Y-m-d H:i:s');
        $model->modify_time = date('Y-m-d H:i:s');

        if(!$model->save()){
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'failed!');
        }
        //先找出这个活动的自定义字段
        if(count($ck->customFields) > 0){
            $userCustomFields = array();
            foreach ($ck->customFields as $customFields){
                list($key,$value) = explode(',', $customFields);
                $userCustomFields[$key] = $value;
            }

            $customKeys = CustomExtActMap::model()->allCustomKeys($ck->actId);
            if($customKeys){
                foreach ($customKeys as $customKey){
                    if(isset($userCustomFields[$customKey['subject']])){
                        CustomExtActUserVal::model()->up($customKey['id'], $ck->actId, $guid, $userCustomFields[$customKey['subject']]);
                    }
                }
            }
        }
        return Yii::app()->res->output(Error::NONE, 'success');
    }


    /**
     * 活动签到码列表
     */
    public function actionCheckinCodes()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
            ),
            array(
                array('actId', 'required'),
                array('actId', 'numerical', 'integerOnly' => FALSE),
            )
        );
        if (!$ck->validate()){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $actInfo = ActInfoO::model()->profile0($ck->actId);

        $list = ActCheckinStepO::model()->actCheckinList($ck->actId);

        return Yii::app()->res->output(Error::NONE, 'success',
                array('act_info' => $actInfo,
                    'total_num'=>count($list),
                    'checkin_codes' => $list,
                ));
    }


    /**
     * 活动添加签到码
     */
    public function actionAddCode()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'subject' => Yii::app()->request->getParam('subject'),
            ),
            array(
                array('actId,subject', 'required'),
                array('actId', 'numerical', 'integerOnly' => FALSE),
                array('subject', 'CZhEnV', 'min' => 1, 'max' => 24),
            )
        );
        if (!$ck->validate()){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $actInfo = ActInfoO::model()->profile0($ck->actId);

        $checkin_codes = array();
        $info = ActCheckinStepO::model()->addCheckin($ck->actId, $ck->subject);
        if(!$info){
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'failed');
        }
        $checkin_codes[] = $info;

        return Yii::app()->res->output(Error::NONE, 'success',
                array('act_info' => $actInfo,
                    'total_num'=>1,
                    'checkin_codes' => $checkin_codes,
                ));
    }


    /**
     * 活动修改签到码
     */
    public function actionModifyCode()
    {
        $ck = Rules::instance(
            array(
                'codeId' => Yii::app()->request->getParam('codeId'),
                'subject' => Yii::app()->request->getParam('subject'),
                'needSure' => Yii::app()->request->getParam('needSure'),
                'rgbHex' => Yii::app()->request->getParam('rgbHex'),
            ),
            array(
                array('codeId, subject', 'required'),
                array('codeId', 'numerical', 'integerOnly' => FALSE),
                array('subject', 'CZhEnV', 'min' => 1, 'max' => 24),
            )
        );
        if (!$ck->validate()){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

       // $actInfo = ActInfoO::model()->profile0($ck->actId);

        $info = ActCheckinStepO::model()->modifyCheckin($ck->codeId, $ck->subject, $ck->needSure);
        if(!$info){
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'failed');
        }

        return Yii::app()->res->output(Error::NONE, 'success',
                array(//'act_info' => $actInfo,
                    'total_num'=>1,
                    'checkin_codes' => $info,
                ));
    }


    /**
     * 活动删除签到码
     */
    public function actionDelCode()
    {
        $ck = Rules::instance(
            array(
                'codeId' => Yii::app()->request->getParam('codeId'),
            ),
            array(
                array('codeId', 'required'),
                array('codeId', 'numerical', 'integerOnly' => FALSE),
            )
        );
        if (!$ck->validate()){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }


        $ret = ActCheckinStepO::model()->delCheckin($ck->codeId);
        if(!$ret){
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'failed');
        }
        return Yii::app()->res->output(Error::NONE, 'success');
    }


    /**
     * 发送消息
     * actId	int	活动id
     * hasPush	int	是否发推送：0否，1是
     * hasSms	int	是否发短信：0否，1是
     * content	String	内容
     */
    public function actionSendMsg()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'hasPush' => Yii::app()->request->getParam('hasPush'),
                'hasSms' => Yii::app()->request->getParam('hasSms'),
                'content' => Yii::app()->request->getParam('content'),
            ),
            array(
                array('actId, hasPush, hasSms, content', 'required'),
                array('actId, hasPush, hasSms', 'numerical', 'integerOnly' => FALSE),
                array('hasPush', 'in', 'range' => array(0, 1)),
                array('hasSms', 'in', 'range' => array(0, 1)),
                array('content', 'CZhEnV', 'min' => 1, 'max' => 150),
            )
        );
        if (!$ck->validate()){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $actInfo = ActInfo::model()->profile(null, $ck->actId, null, false);
        if(!$actInfo){
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '该活动不存在');
        }
        
        //发送通知
        if($ck->hasPush == 1){
            //交由cron处理
            $msg = PushMsgContentTool::makeOrgActEnroll($actInfo['title'], $ck->content);
            PushMsgTask::model()->add(
                ConstPushMsgTaskType::TO_ACT_ENROLL_USERS, 
                $ck->actId, 
                0, 
                $msg['title'], 
                $msg['descri'], 
                PushMsgContentTool::makeFilterForAct($ck->actId)
            );
        }

        //发送短信
        if($ck->hasSms == 1){
             // 查询出所有报名的电话号码
            $phones = ActEnrollO::model()->enrollPhoneList($ck->actId);
            if(count($phones) > 0){
                //$phones = array('13550082125');
                $ret = Yii::app()->sms->send($phones, $ck->content);
                if(!$ret){
                    return Yii::app()->res->output(Error::SMS_SEND_FAIL, 'sms send failed');
                }
            }
        }
        return Yii::app()->res->output(Error::NONE, 'success');
    }


    /**
     * 活动主办方相册的图片
     */
    public function actionOrgAlbumImgs()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
            ),
            array(
                array('actId', 'required'),
                array('actId', 'numerical', 'integerOnly' => TRUE),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $org = OrgInfoO::model()->getByUidO(Yii::app()->user->id);
        if (empty($org)) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'org not exist');
        }

        $act = ActInfo::model()->findByPk($ck->actId);
        if (empty($act) || $act->org_id != $org->id) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'act not exist');
        }

        $album = ActAlbumO::model()->getOrgAlbumO($act->id);
        if (empty($album) || ConstCheckStatus::PASS != $album->status) {
            return Yii::app()->res->output(Error::NONE, 'org album imgs success', array('total_num' => 0, 'imgs' => array()));;
        }

        $rst = ActAlbumImgMapO::model()->imgsO($album->id);
        $rst['act_title'] = $act->title;
        $rst['act_b_time'] = empty($act->b_time) ? NULL : date('Y.m.d H:i', strtotime($act->b_time));

        Yii::app()->res->output(Error::NONE, 'org album imgs success', $rst);
    }


    /**
     * 活动相册添加图片
     */
    public function actionAddAlbumImg()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getPost('actId'),
                'imgId' => Yii::app()->request->getPost('imgId'),
            ),
            array(
                array('actId, imgId', 'required'),
                array('actId, imgId', 'numerical', 'integerOnly' => TRUE),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $org = OrgInfoO::model()->getByUidO(Yii::app()->user->id);
        if (empty($org)) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'org not exist');
        }

        $act = ActInfo::model()->findByPk($ck->actId);
        if (empty($act) || $act->org_id != $org->id) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'act not exist');
        }

        $album = ActAlbumO::model()->getOrgAlbumO($act->id);
        if(empty($album)){
            $album = new ActAlbum();
            $r = ActAlbumO::model()->createOrgAlbumO($act->id, $album);
            if (!$r) {
                return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act album create fail');
            }
        }else{
            if (ConstCheckStatus::PASS != $album->status) {
                $album->status = ConstCheckStatus::PASS;
                $album->modify_time = date('Y-m-d H:i:s');
                $album->update();
            }
        }

        $rst = ActAlbumImgMapO::model()->addO($album->id, $ck->imgId);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'act add album img success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act add album img fail');
    }


    /**
     * 活动相册删除图片
     */
    public function actionDelAlbumImg()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getPost('actId'),
                'imgId' => Yii::app()->request->getPost('imgId'),
            ),
            array(
                array('actId, imgId', 'required'),
                array('actId, imgId', 'numerical', 'integerOnly' => TRUE),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $org = OrgInfoO::model()->getByUidO(Yii::app()->user->id);
        if (empty($org)) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'org not exist');
        }

        $act = ActInfo::model()->findByPk($ck->actId);
        if (empty($act) || $act->org_id != $org->id) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'act not exist');
        }

        $album = ActAlbumO::model()->getOrgAlbumO($act->id);
        if (empty($album)) {
            return Yii::app()->res->output(Error::RECORD_NOT_EXIST, 'act album not exist');
        }

        $rst = ActAlbumImgMapO::model()->delO($album->id, $ck->imgId);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'act del album img success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act del album img fail');
    }


    /**
     * 活动成员列表
     */
    public function actionMembers()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
            ),
            array(
                array('actId', 'required'),
                array('actId', 'numerical', 'integerOnly' => TRUE),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $org = OrgInfoO::model()->getByUidO(Yii::app()->user->id);
        if (empty($org)) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'org not exist');
        }

        $act = ActInfo::model()->findByPk($ck->actId);
        if (empty($act) || $act->org_id != $org->id) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'act not exist');
        }

        $rst = ActEnrollO::model()->membersO($act->id);
        Yii::app()->res->output(Error::NONE, 'act members success', $rst);
    }


    /**
     * 活动更新成员分组
     */
    public function actionModifyGroup()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getPost('actId'),
                'groups' => Yii::app()->request->getPost('groups', array()),
            ),
            array(
                array('actId', 'required'),
                array('actId', 'numerical', 'integerOnly' => TRUE),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $org = OrgInfoO::model()->getByUidO(Yii::app()->user->id);
        if (empty($org)) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'org not exist');
        }

        $act = ActInfo::model()->findByPk($ck->actId);
        if (empty($act) || $act->org_id != $org->id) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'act not exist');
        }

        foreach ($ck->groups as $v) {
            $groupId = $v['groupId'];
            $groupName = $v['groupName'];
            $userIds = $v['userIds'];
            if (empty($userIds)) {
                continue;
            }
            if (empty($groupId)) {
                //创建一个分组
                $model = new ActGroup();
                $r = ActGroupO::model()->createGroupO($act->id, $groupName, $model);
                if (!$r) {
                    continue;
                }
                $groupId = $model->id;
            }  else {
                //验证是否为该活动分组
                $group = ActGroupO::model()->findByPk($groupId);
                if (empty($group) || ConstStatus::DELETE == $group->status || $group->act_id != $act->id) {
                    continue;
                }
                if (!empty($groupName)) {
                    $group->name = $groupName;
                    $group->modify_time = date('Y-m-d H:i:s');
                    $group->update();
                }
            }
            foreach ($userIds as $v) {
                //为活动报名者添加分组id
                ActGroupO::model()->modifyUserGroupId($v, $act->id, $groupId);
            }
        }
        

        Yii::app()->res->output(Error::NONE, 'act modify group success');
    }


}
