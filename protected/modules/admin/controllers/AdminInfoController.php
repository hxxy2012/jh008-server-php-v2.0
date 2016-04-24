<?php

class AdminInfoController extends AdminController
{
    
    public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + regist, login, upInfo, addUser, updateUser, delUser', // we only allow deletion via POST request
		);
	}

    
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions' => array('regist', 'login'),
				'users' => array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'users' => array('@'),
			),
			array('deny',  // deny all users
				'users' => array('*'),
			),
		);
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
                array('u_name, u_pass', 'required'),
                array('u_name', 'CZhEnV', 'min' => 1, 'max' => 16),
                array('u_pass', 'CUserPassV'),
                array('rememberMe', 'in', 'range' => array(0, 1)),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $r = AdminInfo::model()->login($ck->u_name, $ck->u_pass, $ck->rememberMe);
        if($r) {
            $model = AdminInfo::model()->findByPk(Yii::app()->user->id);
            $logf = $model->getLogUInfo();
            AdminOperateLog::model()->log(Yii::app()->user->id, '登录了系统');
            return Yii::app()->res->output(Error::NONE, '登录成功', $logf);
        }
        
        return Yii::app()->res->output(Error::USERNAME_OR_USERPASS_INVALID, '登录失败');
    }


    /**
     * 修改管理员资料
     */
    public function actionUpInfo()
    {
        $ck = Rules::instance(
            array(
                'old_pass' => Yii::app()->request->getPost('oldPass'),
                'new_pass' => Yii::app()->request->getPost('newPass'),
                'nick_name' => Yii::app()->request->getPost('nickName'),
                'headImgId' => Yii::app()->request->getPost('headImgId'),
            ),
            array(
                array('old_pass, new_pass', 'CUserPassV'),
                array('nick_name', 'CZhEnV', 'min' => 1, 'max' => 32),
                array('headImgId', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = AdminInfo::model()->findByPk(Yii::app()->user->id);
        if (!empty($ck->old_pass) && !empty($ck->new_pass)) {
            if (md5($model->salt . $ck->old_pass) != $model->u_pass) {
                return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '旧密码错误');
            }
            $model->u_pass = md5($model->salt . $ck->new_pass);
        }
        $ck->setModelAttris($model);
        $r = $model->update();
        
        if (!empty($ck->headImgId)) {
            if (!AdminHeadImgMap::model()->setCurrImg($model->id, $ck->headImgId)) {
                return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '头像修改失败');
            }
        }
        
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '修改了资料');
            return Yii::app()->res->output(Error::NONE, '修改成功');
        }
        Yii::app()->res->output(Error::REQUEST_EXCEPTION, '修改失败');
    }
    
    
    /**
     * 查看自己的资料
     */
    public function actionGetMyInfo()
    {
        $model = AdminInfo::model()->findByPk(Yii::app()->user->id);
        $info = $model->getMyInfo();
        Yii::app()->res->output(Error::NONE, '获取成功', array('admin' => $info));
    }
    
    
    /**
     * 获取管理员列表
     */
    public function actionGetUsers() 
    {
        $admins = AdminInfo::model()->getUsers();
        Yii::app()->res->output(Error::NONE, '获取成功', array('admins' => $admins));
    }
    
    
    /**
     * 获取管理员列表（回收站）
     */
    public function actionGetDelUsers() 
    {
        $admins = AdminInfo::model()->getUsers(TRUE);
        Yii::app()->res->output(Error::NONE, '获取成功', array('admins' => $admins));
    }


    /**
     * 获取某个管理员的资料
     */
    public function actionGetUserInfo()
    {
        $ck = Rules::instance(
            array(
                'adminId' => Yii::app()->request->getPost('adminId'),
            ),
            array(
                array('adminId', 'required'),
                array('adminId', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $info = AdminInfo::model()->getUserInfo($ck->adminId);
        Yii::app()->res->output(Error::NONE, '获取成功', array('admin' => $info));
    }

    
    /**
     * 退出登录
     */
	public function actionLogout()
	{
		Yii::app()->user->logout();
        if (Yii::app()->request->isAjaxRequest) {
            return Yii::app()->res->output(Error::NONE, '退出登录成功');
        }
		$this->redirect(Yii::app()->homeUrl);
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
        $iu = Yii::app()->imgUpload->uBusinessImg($ck->img);
        if (!$iu) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '图片上传失败');
        }
        
        //图像信息表插入
        $imgInfo = new ImgInfo();
        if (!$imgInfo->ins($ck->img->name, $iu)) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '图片信息保存失败');
        }
        
        if (!Yii::app()->user->isGuest) {
            $imgUpAdmin = new ImgUpAdminMap();
            //图像上传者关联表插入
            if (!$imgUpAdmin->ins($imgInfo->id, Yii::app()->user->id)) {
                return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '图片关联保存失败');
            }
        }

        $ret = array();
        $ret['img_id'] = $imgInfo->id;
        if ($ck->isReturnUrl) {
            $ret['img_url'] = Yii::app()->imgUpload->getDownUrl($imgInfo->img_url);
        }
        AdminOperateLog::model()->log(Yii::app()->user->id, '上传了图片');
        Yii::app()->res->output(Error::NONE, '图片上传成功', $ret);
    }
    
    
    /**
     * 添加管理员
     */
    public function actionAddUser()
    {
        if (1 != Yii::app()->user->id) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, '权限不允许');
        }
        $ck = Rules::instance(
            array(
                'u_name' => Yii::app()->request->getPost('uName'),
                'uPass' => Yii::app()->request->getPost('uPass'),
                'nick_name' => Yii::app()->request->getPost('nickName'),
                'headImgId' => Yii::app()->request->getPost('headImgId'),
            ),
            array(
                array('u_name, uPass', 'required'),
                array('u_name', 'CZhEnV', 'min' => 1, 'max' => 16),
                array('uPass', 'CUserPassV'),
                array('nick_name', 'CZhEnV', 'min' => 1, 'max' => 32),
                array('headImgId', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $model = new AdminInfo();
        $ck->setModelAttris($model);
        $r = $model->addUser($ck->u_name, $ck->uPass);

        if ($r && !empty($ck->headImgId)) {
            if (!AdminHeadImgMap::model()->setCurrImg($model->id, $ck->headImgId)) {
                return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '头像添加失败');
            }
        }
        
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '添加了活动');
            return Yii::app()->res->output(Error::NONE, '添加成功');
        }
        
        Yii::app()->res->output(Error::NONE, '添加失败');
    }
    
    
    /**
     * 修改管理员
     */
    public function actionUpdateUser()
    {
        if (1 != Yii::app()->user->id) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, '权限不允许');
        }
        
        $ck = Rules::instance(
            array(
                'adminId' => Yii::app()->request->getPost('adminId'),
                'old_pass' => Yii::app()->request->getPost('oldPass'),
                'new_pass' => Yii::app()->request->getPost('newPass'),
                'nick_name' => Yii::app()->request->getPost('nickName'),
                'headImgId' => Yii::app()->request->getPost('headImgId'),
            ),
            array(
                array('adminId', 'required'),
                array('adminId', 'numerical', 'integerOnly' => true),
                array('old_pass, new_pass', 'CUserPassV'),
                array('nick_name', 'CZhEnV', 'min' => 1, 'max' => 32),
                array('headImgId', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko || 1 == $ck->adminId){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = AdminInfo::model()->findByPk($ck->adminId);
        if (empty($model)) {
            return Yii::app()->res->output(Error::USER_NOT_EXIST, '该用户不存在');
        }
        
        if (!empty($ck->old_pass) && !empty($ck->new_pass)) {
            if (md5($model->salt . $ck->old_pass) != $model->u_pass) {
                return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '旧密码错误');
            }
            $model->u_pass = md5($model->salt . $ck->new_pass);
        }
        $ck->setModelAttris($model);
        $r = $model->update();
        
        if (!empty($ck->headImgId)) {
            if (!AdminHeadImgMap::model()->setCurrImg($model->id, $ck->headImgId)) {
                return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '头像修改失败');
            }
        }
        
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '修改了管理员资料');
            return Yii::app()->res->output(Error::NONE, '修改成功');
        }
        Yii::app()->res->output(Error::REQUEST_EXCEPTION, '修改失败');
    }
    
    
    /**
     * 删除管理员
     */
    public function actionDelUser()
    {
        if (1 != Yii::app()->user->id) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, '权限不允许');
        }
        
        $ck = Rules::instance(
            array(
                'adminId' => Yii::app()->request->getPost('adminId'),
            ),
            array(
                array('adminId', 'required'),
                array('adminId', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko || 1 == $ck->adminId){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $r = AdminInfo::model()->delUser($ck->adminId);
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '删除了管理员');
            return Yii::app()->res->output(Error::NONE, '删除成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '删除失败');
    }
    
    
    /**
     * 获取某个商家操作日志
     */
    public function actionGetBusiLogs()
    {
        $ck = Rules::instance(
            array(
                'bid' => Yii::app()->request->getParam('bid'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('bid, page, size', 'required'),
                array('bid, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = BusinessOperateLog::model()->getLogs($ck->page, $ck->size, empty($ck->bid) ? NULL : $ck->bid);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
    
    /**
     * 获取管理员操作日志
     */
    public function actionGetLogs()
    {
        $ck = Rules::instance(
            array(
                'aid' => Yii::app()->request->getParam('aid'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('page, size', 'required'),
                array('page, size, aid', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = AdminOperateLog::model()->getLogs($ck->page, $ck->size, empty($ck->aid) ? NULL : $ck->aid);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
}