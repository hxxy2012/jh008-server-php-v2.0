<?php

class BusinessInfoController extends BusinessController
{
    
    public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + regist, login, upInfo, imgUp', // we only allow deletion via POST request
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
     * 注册
     */
	public function actionRegist()
	{
        //$this->layout = '//layouts/blank';
        
        $ck = Rules::instance(
            array(
                'u_name' => Yii::app()->request->getPost('uName'),
                'u_pass' => Yii::app()->request->getPost('uPass'),
                'name' => Yii::app()->request->getPost('name'),
                'address' => Yii::app()->request->getPost('address'),
                'contact_phone' => Yii::app()->request->getPost('contactPhone'),
                'contact_email' => Yii::app()->request->getPost('contactEmail'),
                'contact_descri' => Yii::app()->request->getPost('contactDescri'),
                'logoImgId' => Yii::app()->request->getPost('logoImgId'),
            ),
            array(
                array('u_name, u_pass', 'required'),
                array('u_name', 'CZhEnV', 'min' => 1, 'max' => 16),
                array('u_pass', 'CUserPassV'),
                array('name', 'CZhEnV', 'min' => 1, 'max' => 16),
                array('address', 'CZhEnV', 'min' => 1, 'max' => 32),
                array('contact_phone', 'CPhoneNumberV'),
                array('contact_email', 'email'),
                array('contact_descri', 'CZhEnV', 'min' => 1, 'max' => 60),
                array('logoImgId', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            //if (Yii::app()->request->isAjaxRequest) {
                return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数');
            //}
            //$this->render('regist');
        }

        $model = new BusinessInfo();
        $ck->setModelAttris($model);
        $r = BusinessInfo::model()->regist($model, $ck->u_name, $ck->u_pass);
        
        if (!empty($ck->logoImgId)) {
            if (!BusinessLogoImgMap::model()->setCurrImg($model->id, $ck->logoImgId)) {
                return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'logo添加失败');
            }
        }

        if ($r) {
            //if (Yii::app()->request->isAjaxRequest) {
                return Yii::app()->res->output(Error::NONE, '注册成功');
            //}
            //$this->redirect(array('business/businessInfo/index'));
        }
        
        //if (Yii::app()->request->isAjaxRequest) {
            return Yii::app()->res->output(Error::NONE, '注册失败');
        //}
        //$this->render('regist');
	}
    
    
    /**
     * 登录
     */
	public function actionLogin()
	{
        //$this->layout = '//layouts/blank';
        
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
            //if (Yii::app()->request->isAjaxRequest) {
                return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数');
            //}
            //$this->render('login');
        }
        
        $r = BusinessInfo::model()->login($ck->u_name, $ck->u_pass, $ck->rememberMe);
        if($r) {
            //if (Yii::app()->request->isAjaxRequest) {
                $model = BusinessInfo::model()->findByPk(Yii::app()->user->id);
                $logf = $model->getLogUInfo();
                BusinessOperateLog::model()->log(Yii::app()->user->id, '登录了系统 ,来自：' . Yii::app()->request->userHostAddress);
                return Yii::app()->res->output(Error::NONE, '登录成功', $logf);
            //}
            //$this->redirect(array('business/businessInfo/index'));
        }
        
        //if (Yii::app()->request->isAjaxRequest) {
            return Yii::app()->res->output(Error::USERNAME_OR_USERPASS_INVALID, '登录失败');
        //}
        //$this->render('regist');
	}

    
    /**
     * 修改商家资料
     */
    public function actionUpInfo()
    {
        $ck = Rules::instance(
            array(
                'old_pass' => Yii::app()->request->getPost('oldPass'),
                'new_pass' => Yii::app()->request->getPost('newPass'),
                'name' => Yii::app()->request->getPost('name'),
                'address' => Yii::app()->request->getPost('address'),
                'contact_phone' => Yii::app()->request->getPost('contactPhone'),
                'contact_email' => Yii::app()->request->getPost('contactEmail'),
                'contact_descri' => Yii::app()->request->getPost('contactDescri'),
                'logoImgId' => Yii::app()->request->getPost('logoImgId'),
            ),
            array(
                array('old_pass, new_pass', 'CUserPassV'),
                array('name', 'CZhEnV', 'min' => 1, 'max' => 16),
                array('address', 'CZhEnV', 'min' => 1, 'max' => 32),
                array('contact_phone', 'CPhoneNumberV'),
                array('contact_email', 'email'),
                array('contact_descri', 'CZhEnV', 'min' => 1, 'max' => 60),
                array('logoImgId', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数');
        }
        
        $model = BusinessInfo::model()->findByPk(Yii::app()->user->id);
        if (!empty($ck->old_pass) && !empty($ck->new_pass)) {
            if (md5($model->salt . $ck->old_pass) != $model->u_pass) {
                return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '旧密码错误');
            }
            $model->u_pass = md5($model->salt . $ck->new_pass);
        }
        $ck->setModelAttris($model);
        $r = $model->update();
        
        if (!empty($ck->logoImgId)) {
            if (!BusinessLogoImgMap::model()->setCurrImg($model->id, $ck->logoImgId)) {
                return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'logo修改失败');
            }
        }
        
        if ($r) {
            BusinessOperateLog::model()->log(Yii::app()->user->id, '修改了商家资料');
            return Yii::app()->res->output(Error::NONE, '修改成功');
        }
        Yii::app()->res->output(Error::REQUEST_EXCEPTION, '修改失败');
    }

    
    /**
     * 查看商家资料
     */
    public function actionGetMyInfo()
    {
        $model = BusinessInfo::model()->findByPk(Yii::app()->user->id);
        $info = $model->getMyInfo();
        Yii::app()->res->output(Error::NONE, '获取成功', array('business' => $info));
    }

    
    /**
     * 退出登录
     */
	public function actionLogout()
	{
        BusinessOperateLog::model()->log(Yii::app()->user->id, '退出了登录');
		Yii::app()->user->logout();
        if (Yii::app()->request->isAjaxRequest) {
            return Yii::app()->res->output(Error::NONE, '退出登录成功');
        }
		$this->redirect(Yii::app()->homeUrl);
	}
    
    
    /**
     * 商家相关图片上传
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
                    'maxSize' => 512 * 512 * 1, 
                    'tooLarge' => '上传文件超过 512 * 512 kb，无法上传。',
                ),
                array('isReturnUrl', 'in', 'range' => array(0, 1)),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数');
        }
        
        //保存图像文件到指定目录
        $iu = Yii::app()->imgUpload->uBusinessImg($ck->img);
        if (!$iu) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '图片上传失败');
        }
        
        //图像信息表插入
        $imgInfo = new ImgInfo();
        if (!$imgInfo->ins($ck->img->name, $iu)) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '图片保存失败');
        }
        
        if (!Yii::app()->user->isGuest) {
            $imgUpBusi = new ImgUpBusinessMap();
            //图像上传者关联表插入
            if (!$imgUpBusi->ins($imgInfo->id, Yii::app()->user->id)) {
                return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '图片保存失败');
            }
        }

        $ret = array();
        $ret['img_id'] = $imgInfo->id;
        if ($ck->isReturnUrl) {
            $ret['img_url'] = Yii::app()->imgUpload->getDownUrl($imgInfo->img_url);
        }
        BusinessOperateLog::model()->log(Yii::app()->user->id, '上传了一张图片');
        Yii::app()->res->output(Error::NONE, '图片上传成功', $ret);
    }
    
    
    /**
     * 获取商家操作日志
     */
    public function actionGetLogs()
    {
        $ck = Rules::instance(
            array(
                //'bid' => Yii::app()->request->getParam('bid'),
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
        
        $rst = BusinessOperateLog::model()->getLogs($ck->page, $ck->size, Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
}