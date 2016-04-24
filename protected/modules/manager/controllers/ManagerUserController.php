<?php

class ManagerUserController extends ManagerController
{
    
    public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + login, cityLogin, addM, updateM, updateMSelf, addCM, updateCM, updateCMSelf, addRemarkM, delRemarkM, addRemarkCM, delRemarkCM', // we only allow deletion via POST request
		);
	}

    
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions' => array('cities', 'login', 'cityLogin'),
				'users' => array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
                //'actions' => array(''),
				'users' => array('@'),
			),
            array('allow',
                'actions' => array(''),
				'expression' => 'yii::app()->user->superManager()',
			),
            array('allow',
                'actions' => array(''),
				'expression' => 'yii::app()->user->managerDataRegulator()',
			),
            array('allow',
                'actions' => array(''),
				'expression' => 'yii::app()->user->managerOperator()',
			),
            array('allow',
                'actions' => array(''),
				'expression' => 'yii::app()->user->cityManager()',
			),
            array('allow',
                'actions' => array(''),
				'expression' => 'yii::app()->user->cityOperator()',
			),
			array('deny',  // deny all users
				'users' => array('*'),
			),
		);
	}
    
    
    /**
     * 验证登录信息
     */
    public function actionLoginInfo()
    {
        echo 'success<br>';
        echo 'id:' . Yii::app()->user->id . '<br>';
        echo 'type:' . Yii::app()->user->type . '<br>';
    }


    /**
     * 城市列表
     */
    public function actionCities()
    {
        $ck = Rules::instance(
            array(
                'page' => Yii::app()->request->getParam('page', 1),
                'size' => Yii::app()->request->getParam('size', 10000),
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
        
        $rst = CityInfoM::model()->cities($ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }


    /**
     * 管理员列表
     */
    public function actionManagers()
    {
        $ck = Rules::instance(
            array(
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
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
        
        $rst = ManagerInfo::model()->managers(NULL, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'managers success', $rst);
    }
    
    
    /**
     * 城市管理员列表
     */
    public function actionCityManagers()
    {
        $ck = Rules::instance(
            array(
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
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
        
        $rst = ManagerCityMap::model()->cityManagers(ConstCityManagerStatus::CITY_MANAGER, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'city managers success', $rst);
    }
    
    
    /**
     * 城市操作员列表
     */
    public function actionCityOperators()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('page, size', 'required'),
                array('cityId, page, size', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = ManagerCityMap::model()->cityManagers(ConstCityManagerStatus::CITY_OPERATOR, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'city operators success', $rst);
    }

    
    /**
     * 管理员登录
     */
    public function actionLogin()
    {
        $ck = Rules::instance(
            array(
                'u_name' => Yii::app()->request->getPost('uName'),
                'u_pass' => Yii::app()->request->getPost('uPass'),
                'rememberMe' => Yii::app()->request->getPost('rememberMe', 0),
            ),
            array(
                array('u_name, u_pass, rememberMe', 'required'),
                array('u_name, u_pass', 'length', 'max' => 16),
                array('rememberMe', 'in', 'range' => array(0, 1)),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $r = ManagerInfo::model()->login($ck->u_name, $ck->u_pass, $ck->rememberMe);
        if($r) {
            $manager = ManagerInfo::model()->profile(Yii::app()->user->id);
            //AdminOperateLog::model()->log(Yii::app()->user->id, '登录了系统');
            //echo 'id:' . Yii::app()->user->id . '<br>';
            //echo 'type:' . Yii::app()->user->type . '<br>';
            return Yii::app()->res->output(Error::NONE, 'manager login success', array('manager' => $manager));
        }
        
        Yii::app()->res->output(Error::USERNAME_OR_USERPASS_INVALID, '登录失败');
    }
    
    
    /**
     * 城市管理员登录
     */
    public function actionCityLogin()
    {
        $ck = Rules::instance(
            array(
                'u_name' => Yii::app()->request->getPost('uName'),
                'u_pass' => Yii::app()->request->getPost('uPass'),
                'rememberMe' => Yii::app()->request->getPost('rememberMe', 0),
            ),
            array(
                array('u_name, u_pass, rememberMe', 'required'),
                array('u_name, u_pass', 'length', 'max' => 16),
                array('rememberMe', 'in', 'range' => array(0, 1)),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $r = CityManager::model()->login($ck->u_name, $ck->u_pass, $ck->rememberMe);
        if($r) {
            $cityManager = ManagerCityMap::model()->cityManager(Yii::app()->user->id);
            //AdminOperateLog::model()->log(Yii::app()->user->id, '登录了系统');
            //echo 'id:' . Yii::app()->user->id . '<br>';
            //echo 'type:' . Yii::app()->user->type . '<br>';
            return Yii::app()->res->output(Error::NONE, 'city manager login success', array('city_manager' => $cityManager));
        }
        
        Yii::app()->res->output(Error::USERNAME_OR_USERPASS_INVALID, '登录失败');
    }

    
    /**
     * 添加管理员
     */
    public function actionAddM()
    {
        $ck = Rules::instance(
            array(
                'u_name' => Yii::app()->request->getPost('uName'),
                'u_pass' => Yii::app()->request->getPost('uPass'),
                'name' => Yii::app()->request->getPost('name'),
                'type' => Yii::app()->request->getPost('type'),
            ),
            array(
                array('u_name, u_pass, name, type', 'required'),
                array('u_name, u_pass, name', 'length', 'max' => 16),
                array('type', 'in', 'range' => array(ConstManagerStatus::MANAGER_DATA_REGULATOR, ConstManagerStatus::MANAGER_OPERATOR)),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = new ManagerInfo();
        $ck->setModelAttris($model);
        $rst = ManagerInfo::model()->add($model);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'manager add success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'manager add fail');
    }
    
    
    /**
     * 修改指定管理员
     */
    public function actionUpdateM()
    {
        $ck = Rules::instance(
            array(
                'managerId' => Yii::app()->request->getPost('managerId'),
                'uPass' => Yii::app()->request->getPost('uPass'),
                'name' => Yii::app()->request->getPost('name'),
                'type' => Yii::app()->request->getPost('type'),
            ),
            array(
                array('managerId', 'required'),
                array('managerId', 'numerical', 'integerOnly' => true),
                array('uPass, name', 'length', 'max' => 16),
                array('type', 'in', 'range' => array(ConstManagerStatus::MANAGER_DATA_REGULATOR, ConstManagerStatus::MANAGER_OPERATOR)),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = ManagerInfo::model()->up(NULL, $ck->managerId, $ck->uPass, $ck->name, $ck->type);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'manager update success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'manager update fail');
    }

    
    /**
     * 管理员修改自己的信息
     */
    public function actionUpdateMSelf()
    {
        $ck = Rules::instance(
            array(
                'oldPass' => Yii::app()->request->getPost('oldPass'),
                'newPass' => Yii::app()->request->getPost('newPass'),
                'name' => Yii::app()->request->getPost('name'),
            ),
            array(
                array('oldPass, newPass, name', 'length', 'max' => 16),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = Yii::app()->user;
        
        //有新密码存在，则为修改密码
        if (!empty($ck->newPass)) {
            //旧密码不能为空
            if (empty($ck->oldPass)) {
                return Yii::app()->res->output(Error::PARAMS_ILLEGAL, 'user pass is wrong');
            }
            //旧密码与真实密码不一致
            if (md5($model->salt . $ck->oldPass) != $model->u_pass) {
                return Yii::app()->res->output(Error::PARAMS_ILLEGAL, 'user pass is wrong');
            }
        }
        
        $rst = ManagerInfo::model()->up($model, NULL, $ck->newPass, $ck->name, NULL);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'manager self update success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'manager self update fail');
    }
    
    
    /**
     * 添加城市管理员
     */
    public function actionAddCM()
    {
        $ck = Rules::instance(
            array(
                'u_name' => Yii::app()->request->getPost('uName'),
                'u_pass' => Yii::app()->request->getPost('uPass'),
                'name' => Yii::app()->request->getPost('name'),
                'cityId' => Yii::app()->request->getPost('cityId'),
                'type' => Yii::app()->request->getPost('type'),
            ),
            array(
                array('u_name, u_pass, name, cityId, type', 'required'),
                array('u_name, u_pass, name', 'length', 'max' => 16),
                array('cityId', 'numerical', 'integerOnly' => true),
                array('type', 'in', 'range' => array(ConstCityManagerStatus::CITY_MANAGER, ConstCityManagerStatus::CITY_OPERATOR)),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = new CityManager();
        $ck->setModelAttris($model);
        $rst = CityManager::model()->add($model, NULL, NULL, NULL, $ck->cityId, $ck->type);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'city manager add success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'city manager add fail');
    }
    
    
    /**
     * 修改城市管理员
     */
    public function actionUpdateCM()
    {
        $ck = Rules::instance(
            array(
                'cityManagerId' => Yii::app()->request->getPost('cityManagerId'),
                'uPass' => Yii::app()->request->getPost('uPass'),
                'name' => Yii::app()->request->getPost('name'),
            ),
            array(
                array('cityManagerId', 'required'),
                array('cityManagerId', 'numerical', 'integerOnly' => true),
                array('uPass, name', 'length', 'max' => 16),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = CityManager::model()->up(NULL, $ck->cityManagerId, $ck->uPass, $ck->name);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'city manager update success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'city manager update fail');
    }
    
    
    /**
     * 城市管理员修改自己的信息
     */
    public function actionUpdateCMSelf()
    {
        $ck = Rules::instance(
            array(
                'oldPass' => Yii::app()->request->getPost('oldPass'),
                'newPass' => Yii::app()->request->getPost('newPass'),
                'name' => Yii::app()->request->getPost('name'),
            ),
            array(
                array('oldPass, newPass, name', 'length', 'max' => 16),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = Yii::app()->user;
        
        //有新密码存在，则为修改密码
        if (!empty($ck->newPass)) {
            //旧密码不能为空
            if (empty($ck->oldPass)) {
                return Yii::app()->res->output(Error::PARAMS_ILLEGAL, 'user pass is wrong');
            }
            //旧密码与真实密码不一致
            if (md5($model->salt . $ck->oldPass) != $model->u_pass) {
                return Yii::app()->res->output(Error::PARAMS_ILLEGAL, 'user pass is wrong');
            }
        }
        
        $rst = CityManager::model()->up($model, NULL, $ck->newPass, $ck->name);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'city manager self update success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'city manager self update fail');
    }
    
    
    /**
     * 管理员相关图片上传
     */
    public function actionImgUp() {
        $ck = Rules::instance(
            array(
                'img' => CUploadedFile::getInstanceByName('img'),
                'isReturnUrl' => Yii::app()->request->getPost('isReturnUrl', 0),
            ),
            array(
                array('img', 'required'),
                array('img', 'file', 'allowEmpty' => true,
                    'types' => 'jpg',
                    'maxSize' => 1024 * 1024 * 1, 
                    'tooLarge' => '上传文件超过 1024 * 1024 kb，无法上传。',
                ),
                array('isReturnUrl', 'in', 'range' => array(0, 1)),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        //保存图像文件到指定目录
        $iu = Yii::app()->imgUpload->uManagerImg($ck->img);
        if (!$iu) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'img upload fail');
        }
        
        //图像信息表插入
        $imgInfo = new ImgInfo();
        if (!$imgInfo->ins($ck->img->name, $iu)) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'img upload info save fail');
        }
        
        if (!Yii::app()->user->isGuest) {
            $imgUpM = array();
            if (Yii::app()->user->getType() < ConstCityManagerStatus::CITY_MANAGER) {
                $imgUpM = new ImgUpManagerMap();
            }  else {
                $imgUpM = new ImgUpCityManagerMap();
            }
            
            //图像上传者关联表插入
            if (!$imgUpM->ins($imgInfo->id, Yii::app()->user->id)) {
                return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'img upload relation save fail');
            }
        }

        $ret = array();
        $ret['img_id'] = $imgInfo->id;
        if ($ck->isReturnUrl) {
            $ret['img_url'] = Yii::app()->imgUpload->getDownUrl($imgInfo->img_url);
        }
        Yii::app()->res->output(Error::NONE, 'img upload success', $ret);
    }
    
    
    /**
     * 管理员相关视频上传
     */
    public function actionVideoUp() {
        $ck = Rules::instance(
            array(
                'video' => CUploadedFile::getInstanceByName('video'),
                'isReturnUrl' => Yii::app()->request->getPost('isReturnUrl', 0),
            ),
            array(
                array('video', 'required'),
                array('video', 'file', 'allowEmpty' => true,
                    //'types' => 'jpg',
                    'maxSize' => 4096 * 4096 * 1, 
                    'tooLarge' => '上传文件超过 4096 * 4096 kb，无法上传。',
                ),
                array('isReturnUrl', 'in', 'range' => array(0, 1)),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        //保存视频文件到指定目录
        $vu = Yii::app()->fileUpload->upFile($ck->video);
        if (!$vu) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'video upload fail');
        }
        
        //文件信息表插入
        $videoInfo = new VideoInfo();
        if (!$videoInfo->ins($ck->video->name, $vu)) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'video upload info save fail');
        }
        
        if (!Yii::app()->user->isGuest) {
            $videoUpM = array();
            if (Yii::app()->user->getType() < ConstCityManagerStatus::CITY_MANAGER) {
                $videoUpM = new VideoUpManagerMap();
            }  else {
                $videoUpM = new VideoUpCityManagerMap();
            }
            
            //视频上传者关联表插入
            if (!$videoUpM->ins($videoInfo->id, Yii::app()->user->id)) {
                return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'video upload relation save fail');
            }
        }

        $ret = array();
        $ret['video_id'] = $videoInfo->id;
        if ($ck->isReturnUrl) {
            $ret['video_url'] = Yii::app()->fileUpload->getDownUrl($videoInfo->video_url);
        }
        Yii::app()->res->output(Error::NONE, 'video upload success', $ret);
    }
    
    
    /**
     * 退出登录
     */
	public function actionLogout()
	{
		Yii::app()->user->logout();
        if (Yii::app()->request->isAjaxRequest) {
            return Yii::app()->res->output(Error::NONE, 'loginout success');
        }
		$this->redirect(Yii::app()->homeUrl);
	}
    
    
    /**
     * 管理员备注列表
     */
    public function actionManagerRemarks()
    {
        $ck = Rules::instance(
            array(
                //类型id：1活动，2资讯，3用户，4活动标签，5达人标签
                'targetId' => Yii::app()->request->getParam('targetId'),
                'typeId' => Yii::app()->request->getParam('typeId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('targetId, typeId, page, size', 'required'),
                array('targetId, page, size', 'numerical', 'integerOnly' => true),
                array('typeId', 'in', 'range' => array(1, 2, 3, 4, 5)),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = array();
        switch ($ck->typeId) {
            case 1:
                $rst = ManagerActRemark::model()->remarksM($ck->targetId, Yii::app()->user->id, $ck->page, $ck->size);
                break;
            case 2:
                $rst = ManagerNewsRemark::model()->remarksM($ck->targetId, Yii::app()->user->id, $ck->page, $ck->size);
                break;
            case 3:
                $rst = ManagerUserRemark::model()->remarksM($ck->targetId, Yii::app()->user->id, $ck->page, $ck->size);
                break;
            case 4:
                $rst = ManagerTagRemark::model()->remarksM($ck->targetId, Yii::app()->user->id, $ck->page, $ck->size);
                break;
            case 5:
                $rst = ManagerUserTagRemark::model()->remarksM($ck->targetId, Yii::app()->user->id, $ck->page, $ck->size);
                break;
            default:
                break;
        }
        Yii::app()->res->output(Error::NONE, 'manager remarks success', $rst);
    }
    
    
    /**
     * 城市管理员备注列表
     */
    public function actionCityManagerRemarks()
    {
        $ck = Rules::instance(
            array(
                //类型id：1活动，2资讯，3用户，4活动标签，5达人标签
                'targetId' => Yii::app()->request->getParam('targetId'),
                'typeId' => Yii::app()->request->getParam('typeId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('targetId, typeId, page, size', 'required'),
                array('targetId, page, size', 'numerical', 'integerOnly' => true),
                array('typeId', 'in', 'range' => array(1, 2, 3, 4, 5)),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = array();
        switch ($ck->typeId) {
            case 1:
                $rst = CityManagerActRemark::model()->remarksM($ck->targetId, Yii::app()->user->id, $ck->page, $ck->size);
                break;
            case 2:
                $rst = CityManagerNewsRemark::model()->remarksM($ck->targetId, Yii::app()->user->id, $ck->page, $ck->size);
                break;
            case 3:
                $rst = CityManagerUserRemark::model()->remarksM($ck->targetId, Yii::app()->user->id, $ck->page, $ck->size);
                break;
            case 4:
                $rst = CityManagerTagRemark::model()->remarksM($ck->targetId, Yii::app()->user->id, $ck->page, $ck->size);
                break;
            case 5:
                $rst = CityManagerUserTagRemark::model()->remarksM($ck->targetId, Yii::app()->user->id, $ck->page, $ck->size);
                break;
            default:
                break;
        }
        Yii::app()->res->output(Error::NONE, 'city manager remarks success', $rst);
    }
    
    
    /**
     * 管理员添加备注
     */
    public function actionAddRemarkM()
    {
        $ck = Rules::instance(
            array(
                'targetId' => Yii::app()->request->getPost('targetId'),
                'typeId' => Yii::app()->request->getPost('typeId'),
                'remark' => Yii::app()->request->getPost('remark'),
            ),
            array(
                array('targetId, typeId', 'required'),
                array('targetId', 'numerical', 'integerOnly' => true),
                array('typeId', 'in', 'range' => array(1, 2, 3, 4, 5)),
                array('remark', 'length', 'max' => 256),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = FALSE;
        switch ($ck->typeId) {
            case 1:
                $rst = ManagerActRemark::model()->addM(Yii::app()->user->id, $ck->targetId, $ck->remark);
                break;
            case 2:
                $rst = ManagerNewsRemark::model()->addM(Yii::app()->user->id, $ck->targetId, $ck->remark);
                break;
            case 3:
                $rst = ManagerUserRemark::model()->addM(Yii::app()->user->id, $ck->targetId, $ck->remark);
                break;
            case 4:
                $rst = ManagerTagRemark::model()->addM(Yii::app()->user->id, $ck->targetId, $ck->remark);
                break;
            case 5:
                $rst = ManagerUserTagRemark::model()->addM(Yii::app()->user->id, $ck->targetId, $ck->remark);
                break;
            default:
                break;
        }
        
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'add remark m success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'add remark m fail');
    }
    
    
    /**
     * 管理员删除备注
     */
    public function actionDelRemarkM()
    {
        $ck = Rules::instance(
            array(
                'remarkId' => Yii::app()->request->getPost('remarkId'),
                'typeId' => Yii::app()->request->getPost('typeId'),
            ),
            array(
                array('remarkId, typeId', 'required'),
                array('remarkId', 'numerical', 'integerOnly' => true),
                array('typeId', 'in', 'range' => array(1, 2, 3, 4, 5)),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = FALSE;
        switch ($ck->typeId) {
            case 1:
                $rst = ManagerActRemark::model()->delM($ck->remarkId, Yii::app()->user->id);
                break;
            case 2:
                $rst = ManagerNewsRemark::model()->delM($ck->remarkId, Yii::app()->user->id);
                break;
            case 3:
                $rst = ManagerUserRemark::model()->delM($ck->remarkId, Yii::app()->user->id);
                break;
            case 4:
                $rst = ManagerTagRemark::model()->delM($ck->remarkId, Yii::app()->user->id);
                break;
            case 5:
                $rst = ManagerUserTagRemark::model()->delM($ck->remarkId, Yii::app()->user->id);
                break;
            default:
                break;
        }
        
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'del remark m success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'del remark m fail');
    }
    
    
    /**
     * 城市管理员添加备注
     */
    public function actionAddRemarkCM()
    {
        $ck = Rules::instance(
            array(
                'targetId' => Yii::app()->request->getPost('targetId'),
                'typeId' => Yii::app()->request->getPost('typeId'),
                'remark' => Yii::app()->request->getPost('remark'),
            ),
            array(
                array('targetId, typeId', 'required'),
                array('targetId', 'numerical', 'integerOnly' => true),
                array('typeId', 'in', 'range' => array(1, 2, 3, 4, 5)),
                array('remark', 'length', 'max' => 256),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = FALSE;
        switch ($ck->typeId) {
            case 1:
                $rst = CityManagerActRemark::model()->addM(Yii::app()->user->id, $ck->targetId, $ck->remark);
                break;
            case 2:
                $rst = CityManagerNewsRemark::model()->addM(Yii::app()->user->id, $ck->targetId, $ck->remark);
                break;
            case 3:
                $rst = CityManagerUserRemark::model()->addM(Yii::app()->user->id, $ck->targetId, $ck->remark);
                break;
            case 4:
                $rst = CityManagerTagRemark::model()->addM(Yii::app()->user->id, $ck->targetId, $ck->remark);
                break;
            case 5:
                $rst = CityManagerUserTagRemark::model()->addM(Yii::app()->user->id, $ck->targetId, $ck->remark);
                break;
            default:
                break;
        }
        
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'add remark cm success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'add remark cm fail');
    }
    
    
    /**
     * 城市管理员删除备注
     */
    public function actionDelRemarkCM()
    {
        $ck = Rules::instance(
            array(
                'remarkId' => Yii::app()->request->getPost('remarkId'),
                'typeId' => Yii::app()->request->getPost('typeId'),
            ),
            array(
                array('remarkId, typeId', 'required'),
                array('remarkId', 'numerical', 'integerOnly' => true),
                array('typeId', 'in', 'range' => array(1, 2, 3, 4, 5)),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = FALSE;
        switch ($ck->typeId) {
            case 1:
                $rst = CityManagerActRemark::model()->delM($ck->remarkId, Yii::app()->user->id);
                break;
            case 2:
                $rst = CityManagerNewsRemark::model()->delM($ck->remarkId, Yii::app()->user->id);
                break;
            case 3:
                $rst = CityManagerUserRemark::model()->delM($ck->remarkId, Yii::app()->user->id);
                break;
            case 4:
                $rst = CityManagerTagRemark::model()->delM($ck->remarkId, Yii::app()->user->id);
                break;
            case 5:
                $rst = CityManagerUserTagRemark::model()->delM($ck->remarkId, Yii::app()->user->id);
                break;
            default:
                break;
        }
        
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'del remark cm success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'del remark cm fail');
    }
    
}