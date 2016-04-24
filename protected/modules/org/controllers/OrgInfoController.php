<?php

class OrgInfoController extends OrgController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + registOrg, modifyInfo, addManager, delManager', // we only allow deletion via POST request
		);
	}

    
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('OrgDA', 'registOrg'),
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
     * 开通社团权限并创建社团
     */
    public function actionRegistOrg()
    {
        $ck = Rules::instance(
            array(
                'phoNum' => Yii::app()->request->getPost('phoNum'),
                'orgName' => Yii::app()->request->getPost('orgName'),
                'cityId' => Yii::app()->request->getPost('cityId'),
            ),
            array(
                array('phoNum, orgName, cityId', 'required'),
                array('phoNum', 'CPhoneNumberV'),
                array('cityId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $user = UserInfo::model()->getByPhoneNum($ck->phoNum);
        if (empty($user)) {
            return Yii::app()->res->output(Error::USER_NOT_EXIST, $ck->phoNum . ' is not regist');
        }
        
        $org = OrgInfoO::model()->getByUidO($user->id);
        if (!empty($org)) {
            return Yii::app()->res->output(Error::RECORD_HAS_EXIST, $ck->phoNum . ' has org:' . $org->name);
        }
        
        $model = new OrgInfo();
        $model->own_id = $user->id;
        $model->name = $ck->orgName;
        $rst = OrgInfoO::model()->insOrgO($user->id, $model);
        if (!$rst) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'org add fail' . json_encode($model->getErrors()));
        }
        OrgCityMapO::model()->upO($model->id, $ck->cityId);
        Yii::app()->res->output(Error::NONE, 'user with org add success');
    }

    
    /**
     * 社团资料查看
     */
    public function actionInfo()
    {
        $ck = Rules::instance(
            array(
                'orgId' => Yii::app()->request->getParam('orgId'),
            ),
            array(
                array('orgId', 'required'),
                array('orgId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $orgRst = NULL;
        if (empty($ck->orgId)) {
            $org = OrgInfoO::model()->getByUidO(Yii::app()->user->id);
            if (empty($org)) {
                Yii::app()->res->output(Error::RECORD_NOT_EXIST, 'org not exist');
            }
            $orgRst = OrgInfoO::model()->profileO(NULL, $org);
        }  else {
            $orgRst = OrgInfoO::model()->profileO($ck->orgId);
        }
        
        Yii::app()->res->output(Error::NONE, 'org info success', array('org' => $orgRst));
    }
    
    
    /**
     * 社团资料修改
     */
    public function actionModifyInfo()
    {
        $ck = Rules::instance(
            array(
                'logo_img_id' => Yii::app()->request->getPost('logoImgId'),
                'name' => Yii::app()->request->getPost('name'),
                'intro' => Yii::app()->request->getPost('intro'),
                'contact_way' => Yii::app()->request->getPost('contactWay'),
                'address' => Yii::app()->request->getPost('address'),
            ),
            array(
                array('logo_img_id', 'numerical', 'integerOnly' => true),
                array('name', 'CZhEnV', 'min' => 1, 'max' => 32, 'isDiff' => TRUE),
                array('intro', 'CZhEnV', 'min' => 1, 'max' => 1024, 'isDiff' => TRUE),
                array('contact_way, address', 'length', 'max'=>255),
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
        $ck->setModelAttris($org);
        $org->modify_time = date('Y-m-d H:i:s');
        $rst = $org->update();
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'modify org info success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'modify org info fail');
    }
    
    
    /**
     * 添加社团管理员
     */
    public function actionAddManager()
    {
        $ck = Rules::instance(
            array(
                'uid' => Yii::app()->request->getPost('uid'),
            ),
            array(
                array('uid', 'required'),
                array('uid', 'numerical', 'integerOnly' => true),
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
        
        if ($ck->uid == Yii::app()->user->id) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, 'org own can not to be manager');
        }
        
        $rst = OrgManagerMapO::model()->addO($org->id, $ck->uid);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'org add manager success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'org add manager fail');
    }
    
    
    /**
     * 删除社团管理员
     */
    public function actionDelManager()
    {
        $ck = Rules::instance(
            array(
                'uid' => Yii::app()->request->getPost('uid'),
            ),
            array(
                array('uid', 'required'),
                array('uid', 'numerical', 'integerOnly' => true),
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
        
        $rst = OrgManagerMapO::model()->delO($org->id, $ck->uid);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'org del manager success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'org del manager fail');
    }
    
    
    /**
     * 社团关注者
     */
    public function actionLovs()
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
        
        $rst = OrgLovUserMapO::model()->lovUsersO($org->id, $ck->page, $ck->size);
        
        Yii::app()->res->output(Error::NONE, 'org lovs success', $rst);
    }
    
    
    /**
     * 社团管理员
     */
    public function actionManagers()
    {
        $ck = Rules::instance(
            array(
                'page' => Yii::app()->request->getParam('page', 1),
                'size' => Yii::app()->request->getParam('size', 1000),
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
        
        $rst = OrgManagerMapO::model()->managersO($org->id, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'org managers success', $rst);
    }
    
    
     public function actionOrgDA(){
        //$org = OrgInfoO::model()->getByUidO(Yii::app()->user->id);
        $org = OrgInfoO::model()->getByUidO(Yii::app()->user->id);
        if (empty($org)) {
            return Yii::app()->res->output(Error::RECORD_NOT_EXIST, 'org not exist');
        }
        
        $da = array(
            'holded' => 0,
            'enrolled' => 0,
            'checked_in'=> 0,
            'active_members' => array(),
        );
        
        //举办活动数
        $da['holded'] = OrgInfoO::model()->getHoldedActCount($org->id);
        
        //总报名人数
        $da['enrolled'] = OrgInfoO::model()->getEnrolledActCount($org->id);
        
        //总签到人数
        $da['checked_in'] = OrgInfoO::model()->getCheckedInActCount($org->id);
        
        //活跃成员
        $da['active_members'] = OrgInfoO::model()->getActiveMembers($org->id);
        Yii::app()->res->output(Error::NONE, 'success', $da);
     }
}
