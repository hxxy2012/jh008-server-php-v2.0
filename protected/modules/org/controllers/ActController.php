<?php

class ActController extends OrgController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + create, modify, del', // we only allow deletion via POST request
		);
	}

    
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('ActDA'),
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
     * 当前活动
     */
    public function actionCurrentActs()
    {
        $ck = Rules::instance(
            array(
                'page' => Yii::app()->request->getParam('page', 1),
                'size' => Yii::app()->request->getParam('size', 100),
            ),
            array(
                array('page, size', 'required'),
                array('page, size', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $org = OrgInfoO::model()->getByUidO(Yii::app()->user->id);
        if (empty($org)) {
            return Yii::app()->res->output(Error::RECORD_NOT_EXIST, 'org not exist');
        }
        
        $rst = ActInfoO::model()->actsO($org->id, FALSE, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'current acts success', $rst);
    }
    
    
    /**
     * 过往活动
     */
    public function actionPastActs()
    {
        $ck = Rules::instance(
            array(
                'page' => Yii::app()->request->getParam('page', 1),
                'size' => Yii::app()->request->getParam('size', 100),
            ),
            array(
                array('page, size', 'required'),
                array('page, size', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $org = OrgInfoO::model()->getByUidO(Yii::app()->user->id);
        if (empty($org)) {
            return Yii::app()->res->output(Error::RECORD_NOT_EXIST, 'org not exist');
        }
        
        $rst = ActInfoO::model()->actsO($org->id, TRUE, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'current acts success', $rst);
    }
    
    
    /**
     * 创建活动（基本资料）
     */
    public function actionCreate()
    {
        $ck = Rules::instance(
            array(
                'title' => Yii::app()->request->getPost('title'),
                'b_time' => Yii::app()->request->getPost('bTime'),
                'e_time' => Yii::app()->request->getPost('eTime'),
                'lon' => Yii::app()->request->getPost('lon'),
                'lat' => Yii::app()->request->getPost('lat'),
                'addr_city' => Yii::app()->request->getPost('addrCity'),
                'addr_area' => Yii::app()->request->getPost('addrArea'),
                'addr_road' => Yii::app()->request->getPost('addrRoad'),
                'addr_num' => Yii::app()->request->getPost('addrNum',''),
                'addr_name' => Yii::app()->request->getPost('addrName'),
            ),
            array(
                array('title, b_time, e_time, lon, lat, addr_city', 'required'),
                array('title', 'CZhEnV', 'min' => 1, 'max' => 255, 'isDiff' => TRUE),
                array('b_time, e_time', 'type', 'datetimeFormat' => 'yyyy-mm-dd hh:mm:ss', 'type' => 'datetime'),
                array('lon, lat', 'numerical', 'integerOnly' => FALSE),
                array('addr_city, addr_area, addr_road', 'CZhEnV', 'min' => 1, 'max' => 12),
                array('addr_name', 'CZhEnV', 'min' => 1, 'max' => 24),
                array('addr_num', 'CZhEnV', 'max' => 24),
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
        
        $city = OrgCityMapO::model()->getCityByOrgO($org->id);
        if (empty($city)) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'city not exist');
        }
        
        $model = new ActInfo();
        $ck->setModelAttris($model);
        
        $rst = ActInfoO::model()->createActO($model, $city->id, $org->id);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'act create success', array('act_id' => $model->id));
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act create fail' . json_encode($model->getErrors()));
    }
    
    
    /**
     * 修改活动
     */
    public function actionModify()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getPost('actId'),
                'title' => Yii::app()->request->getPost('title'),
                'b_time' => Yii::app()->request->getPost('bTime'),
                'e_time' => Yii::app()->request->getPost('eTime'),
                'lon' => Yii::app()->request->getPost('lon'),
                'lat' => Yii::app()->request->getPost('lat'),
                'addr_city' => Yii::app()->request->getPost('addrCity'),
                'addr_area' => Yii::app()->request->getPost('addrArea'),
                'addr_road' => Yii::app()->request->getPost('addrRoad'),
                'addr_num' => Yii::app()->request->getPost('addrNum',''),
                'addr_name' => Yii::app()->request->getPost('addrName'),
                'detail' => Yii::app()->request->getPost('detail'),
                'h_img_id' => Yii::app()->request->getPost('hImgId'),
                'imgIds' => Yii::app()->request->getPost('imgIds'),
                'enroll_b_time' => Yii::app()->request->getPost('enrollBeginTime'),
                'enroll_e_time' => Yii::app()->request->getPost('enrollEndTime'),
                'enroll_limit' => Yii::app()->request->getPost('enrollLimit'),
                'enroll_limit_num' => Yii::app()->request->getPost('enrollLimitNum'),
                'show_pay' => Yii::app()->request->getPost('showPay'),
                'total_fee' => Yii::app()->request->getPost('totalFee'),
                'show_verify' => Yii::app()->request->getPost('showVerify'),
                'customFields' => Yii::app()->request->getPost('customFields'),
                'route_name' => Yii::app()->request->getPost('route_name'),
                'route_points' => Yii::app()->request->getPost('route_points'),
            ),
            array(
                array('actId', 'required'),
                array('actId, h_img_id, enroll_limit_num', 'numerical', 'integerOnly' => TRUE),
                array('title', 'CZhEnV', 'min' => 1, 'max' => 255, 'isDiff' => TRUE),
                array('b_time, e_time, enroll_b_time, enroll_e_time', 'type', 'datetimeFormat' => 'yyyy-mm-dd hh:mm:ss', 'type' => 'datetime'),
                array('lon, lat, total_fee', 'numerical', 'integerOnly' => FALSE),
                array('addr_city, addr_area, addr_road', 'CZhEnV', 'min' => 1, 'max' => 12),
                array('addr_name, route_name', 'CZhEnV', 'min' => 1, 'max' => 24),
                array('imgIds', 'CArrNumV', 'maxLen' => 3),
                array('enroll_limit, show_pay, show_verify', 'in', 'range' => array(0, 1)),
                array('addr_num', 'CZhEnV', 'max' => 24),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        //$output = array();
        
        $org = OrgInfoO::model()->getByUidO(Yii::app()->user->id);
        if (empty($org)) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'org not exist');
        }
        
        $act = ActInfo::model()->findByPk($ck->actId);
        if (empty($act)) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'act not exist');
        }

        //确定修改的是不是该社团的活动
        if ($act->org_id != $org->id && 1 != Yii::app()->user->getOrgId()) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'act not this org');
        }
        
        $ck->setModelAttris($act);
        
        $rst = ActInfoO::model()->modifyActO($act);
        if (!$rst) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act modify fail');
        }
        
        $extend = ActInfoExtendO::model()->getByActIdO($ck->actId);
        $isPublish = FALSE;
        $r = FALSE;
        if (empty($extend)) {
            $extend = new ActInfoExtend();
            $extend->act_id = $act->id;
            $ck->setModelAttris($extend);
            $r = ActInfoExtendO::model()->createActO($extend, $act->title, $act->title, $ck->total_fee);
        }  else {
            if(empty($extend->enroll_e_time) && $ck->enroll_e_time){
                //第三部才会设置这个
                $isPublish = TRUE;
            }
            $ck->setModelAttris($extend);
            $r = ActInfoExtendO::model()->modifyActO($extend, $act->title, $act->title, $ck->total_fee);
           
        }
        if (!$r) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act extend modify fail' . json_encode($extend->getErrors()));
        }
        
        if (!empty($ck->imgIds)) {
            ActImgMap::model()->setActImgs($ck->imgIds, $act->id);
        }
        
        if (!empty($ck->customFields)) {
            $cids = UserCustomExtendO::model()->dealNamesO($ck->customFields);
            if (!empty($cids)) {
                CustomExtActMapO::model()->dealCusIdsO($act->id, $cids);
                CustomExtOrgMapO::model()->dealCusIdsO($org->id, $cids);
            }
        }
        
        if(!empty($ck->route_points)){
            $ret = ActRoute::model()->addActRouteEx($ck->actId, $ck->route_name, $ck->route_points);
            if(!$ret){
                 return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act route add fail');
            }
        }
        
        //给团长发短信
        if($isPublish){
            //查询团长的手机号码
            $phoneNum = UserInfoO::model()->getPhoneNum(Yii::app()->user->id);
            if(!empty($phoneNum)){
                $sms = $act->title.'活动已经成功发布，可以从App分享给朋友了哟！【找活动，找社团，就上集合啦】';
                //$sms = '您创建的活动['.$act->title.']已经生效,请悉知！';
                $ret = Yii::app()->sms->send(array($phoneNum), $sms);
            }
        }
        
        Yii::app()->res->output(Error::NONE, 'act modify success');
    }
    
    
    /**
     * 活动详情
     */
    public function actionDetail()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getPost('actId'),
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
        if ((empty($act) || $act->org_id != $org->id) && 1 != Yii::app()->user->getOrgId()) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'act not exist');
        }
        
        $detail = ActInfoO::model()->fullProfile0(NULL, $act);

        Yii::app()->res->output(Error::NONE, 'act detail success', 
                array('act' => $detail,
            ));
    }
    
    
    /**
     * 活动删除
     */
    public function actionDel()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getPost('actId'),
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
        if ((empty($act) || $act->org_id != $org->id) && 1 != Yii::app()->user->getOrgId()) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'act not exist');
        }
        
        $rst = ActInfoO::model()->modifyActStatus($ck->actId, -1);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'act del success');;
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act del fail');;
    }
    
    
    public function actionActDA(){
        $org = OrgInfoO::model()->getByUidO(Yii::app()->user->id);
        if (empty($org)) {
            return Yii::app()->res->output(Error::RECORD_NOT_EXIST, 'org not exist');
        }
        
        $rst = ActInfoO::model()->getActDA($org->id);
        
        Yii::app()->res->output(Error::NONE, 'success',$rst );
    }
    
}
