<?php

class UserInfoController extends UserController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + newPhone, validPhCode, regiInfo, headImgUp, imgUp, getPhCode, phRePass, login, updateInfo, setOpenid', // we only allow deletion via POST request
		);
	}

    
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('login', 'notLogin', 'error', 'logout', 'newPhone', 'validPhCode', 'getPhCode','SessionTest'),
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
     * 新手机号获取验证码
     */
    public function actionNewPhone() 
    {
        $ck = Rules::instance(
            array(
                'phoneNum' => Yii::app()->request->getPost('phoneNum'),
            ), 
            array(
                array('phoneNum', 'required'),
                array('phoneNum', 'CPhoneNumberV'),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $npo = UserPhoIdfy::model()->newPhCode($ck->phoneNum);
        if (Error::NONE === $npo) {
            Yii::app()->res->output(Error::NONE, '手机验证码发送成功');
        }  else {
            Yii::app()->res->output($npo, '手机验证码发送失败');
        }
    }
    
    
    /**
     * 验证手机验证码
     */
    public function actionValidPhCode() 
    {
        $ck = Rules::instance(
            array(
                //type：1新注册手机号验证登录，2已注册手机号验证登录
                'type' => Yii::app()->request->getPost('type', 1),
                'phoneNum' => Yii::app()->request->getPost('phoneNum'),
                'idfyCode' => Yii::app()->request->getPost('idfyCode'),
            ), 
            array(
                array('type, phoneNum', 'required'),
                array('type', 'in', 'range' => array(1, 2, 3)),
                array('phoneNum', 'CPhoneNumberV'),
                array('idfyCode', 'required'),
                array('idfyCode', 'match', 'pattern' => '/^[a-zA-Z0-9]{6}$/'),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $vco = Error::REQUEST_EXCEPTION;
        switch ($ck->type) {
            case 1:
                //新手机注册验证码
                $vco = UserPhoIdfy::model()->validNewPhCode($ck->phoneNum, $ck->idfyCode);
                break;
            case 2:
                //已注册手机验证码
                $vco = UserPhoIdfy::model()->validHasPhCode($ck->phoneNum, $ck->idfyCode);
                break;
        }
        
        if (Error::NONE === $vco) {
            if (2 == $ck->type) {
                Yii::app()->res->output(Error::NONE, '验证成功', array('uid' => Yii::app()->user->id));
            }  else {
                Yii::app()->res->output(Error::NONE, '验证成功');
            }
        }  else {
            Yii::app()->res->output($vco, '验证失败');
        }
    }


    /**
     * 注册密码及基本资料（密码选填，手机号注册方式必填）
     */
    public function actionRegiInfo() 
    {
        $userPass = Yii::app()->request->getPost('userPass');
        if (!empty($userPass) && strlen($userPass) < 32) {
            $userPass = md5($userPass);
        }
        
        $ck = Rules::instance(
            array(
                //'userPass' => Yii::app()->request->getPost('userPass'),
                'userPass' => $userPass,
                'nick_name' => Yii::app()->request->getPost('nickName'),
                'sex' => Yii::app()->request->getPost('sex'),
                'birth' => Yii::app()->request->getPost('birth'),
                'address' => Yii::app()->request->getPost('address'),
                'email' => Yii::app()->request->getPost('email'),
                'headImgId' => Yii::app()->request->getPost('headImgId'),
            ),
            array(
                array('nick_name, sex, birth', 'required'),
                array('userPass', 'length', 'min' => 32, 'max' => 32),
                array('nick_name', 'CZhEnV', 'min' => 1, 'max' => 24),
                array('sex', 'in', 'range' => array(1, 2)),
                array('birth', 'date', 'format' => 'yyyy-mm-dd'),
                array('address', 'CZhEnV', 'min' => 1, 'max' => 25),
                array('email', 'email'),
                array('headImgId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $phoneNum = Yii::app()->user->getPhoneNum();
        $model = NULL;
        if (!empty($phoneNum)) {
            if (!empty(Yii::app()->user->id)) {
                //已成功注册并登录的用户不能再次注册
                return Yii::app()->res->output(Error::USERNAME_EXIST, '该用户已存在');
            }
            if (UserInfo::model()->validUser($phoneNum)) {
                return Yii::app()->res->output(Error::USERNAME_EXIST, '该用户已存在');
            }
            
            $model = new UserInfo();
            $ck->setModelAttris($model);
            
            //手机号用户注册
            $model->pho_num = $phoneNum;
            //手机号注册须填写密码
            if (empty($ck->userPass)) {
                return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数');
            }
            $model->salt = StrTool::getRandStr(6);
            $model->user_pass = strtoupper(md5(strtoupper($model->salt) . strtoupper($ck->userPass)));
            $model->is_regist = 1;
            
            if (!$model->registUser()) {
                return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'regist by phone fail');
            }
        }  else {
            if (empty(Yii::app()->user->id)) {
                //未注册并登录的第三方用户不能写资料
                return Yii::app()->res->output(Error::USER_NOT_EXIST, '该用户不存在');
            }
            $model = UserInfo::model()->findByPk(Yii::app()->user->id);
            if (empty($model)) {
                return Yii::app()->res->output(Error::USER_NOT_EXIST, '该用户不存在');
            }
            //非手机号用户注册
            $model->is_regist = 2;
            if (!$model->update()) {
                return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'regist by third platform fail');
            }
        }
        
        if (!empty($ck->headImgId)) {
            if (!UserHeadImgMap::model()->setCurrImg($model->id, $ck->headImgId)) {
                return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'head img regist fail');
            }
        }
        
        $extendModel = UserInfoExtend::model()->find('u_id=:uid', array('uid' => $model->id));
        if (empty($extendModel)) {
            $extendModel = new UserInfoExtend();
            $extendModel->u_id = $model->id;
            $ck->setModelAttris($extendModel);
            $extendModel->save();
        }  else {
            $ck->setModelAttris($extendModel);
            $extendModel->update();
        }
        
        //覆盖登录
        UserInfo::model()->coverLogin($model->id);

        $logf = $model->getLogUInfo();
        Yii::app()->res->output(Error::NONE, 'regist success', $logf);
    }

    
    /**
     * 头像文件上传
     * @return type
     */
    public function actionHeadImgUp() {
        $ck = Rules::instance(
            array(
                'headImg' => CUploadedFile::getInstanceByName('headImg'),
            ),
            array(
                array('headImg', 'required'),
                array('headImg', 'file', 'allowEmpty' => true,
                    'types' => 'jpg',
                    'maxSize' => 512 * 512 * 1, 
                    'tooLarge' => '上传文件超过 512 * 512 kb，无法上传。',
                ),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        //保存头像文件到指定目录
        $iu = Yii::app()->imgUpload->uHeadImg($ck->headImg);
        if (!$iu) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '头像上传失败');
        }
        
        //图像信息表插入
        $imgInfo = new ImgInfo();
        if (!$imgInfo->ins($ck->headImg->name, $iu)) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '头像保存失败');
        }

        if (Yii::app()->user->isGuest || empty(Yii::app()->user->id)) {
            return Yii::app()->res->output(Error::NONE, '头像上传成功', array('img_id' => $imgInfo->id));
        }
        
        $imgUpUser = new ImgUpUserMap();
        //图像上传者关联表插入
        if (!$imgUpUser->ins($imgInfo->id, Yii::app()->user->id)) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '头像关联保存失败');
        }
            
        Yii::app()->res->output(Error::NONE, '头像上传成功', array('img_id' => $imgInfo->id));
    }
    
    
    /**
     * 图片文件上传
     * @return type
     */
    public function actionImgUp() {
        $ck = Rules::instance(
            array(
                'img' => CUploadedFile::getInstanceByName('img'),
            ),
            array(
                array('img', 'required'),
                array('img', 'file', 'allowEmpty' => true,
                    'types' => 'jpg',
                    'maxSize' => 1024 * 1024 * 1, 
                    'tooLarge' => '上传文件超过 1024 * 1024 kb，无法上传。',
                ),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        //保存图片文件到指定目录
        $iu = Yii::app()->imgUpload->uImg($ck->img);
        if (!$iu) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '图像上传失败');
        }
        
        //图像信息表插入
        $imgInfo = new ImgInfo();
        if (!$imgInfo->ins($ck->img->name, $iu)) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '图像信息保存失败');
        }
        
        $rstImg = ImgInfo::model()->profile(NULL, $imgInfo);
        
        if (Yii::app()->user->isGuest || empty(Yii::app()->user->id)) {
            return Yii::app()->res->output(Error::NONE, '图片上传成功', $rstImg);
        }
        
        $imgUpUser = new ImgUpUserMap();
        //图像上传者关联表插入
        if (!$imgUpUser->ins($imgInfo->id, Yii::app()->user->id)) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '图片关联保存失败');
        }
        
        Yii::app()->res->output(Error::NONE, '图片上传成功', $rstImg);
    }

    
    /**
     * 已有账号获取手机验证码（找回密码）
     */
    public function actionGetPhCode() 
    {
        $ck = Rules::instance(
            array(
                'phoneNum' => Yii::app()->request->getPost('phoneNum'),
            ), 
            array(
                array('phoneNum', 'required'),
                array('phoneNum', 'CPhoneNumberV'),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $pco = UserPhoIdfy::model()->getPhCode($ck->phoneNum);
        if ($cko && Error::NONE === $pco) {
            Yii::app()->res->output(Error::NONE, '手机验证码发送成功');
        }  else {
            Yii::app()->res->output($pco, '手机验证码发送失败');
        }
    }
    
    
    /**
     *  验证手机号方式修改密码（找回密码）
     */
    public function actionPhRePass()
    {
        $newPass = Yii::app()->request->getPost('newPass');
        if (!empty($newPass) && strlen($newPass) < 32) {
            $newPass = md5($newPass);
        }
        $ck = Rules::instance(
            array(
                //'newPass' => Yii::app()->request->getPost('newPass'),
                'newPass' => $newPass,
            ), 
            array(
                array('newPass', 'required'),
                array('newPass', 'length', 'min' => 32, 'max' => 32),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rpo = UserInfo::model()->phRePass(Yii::app()->user->id, $ck->newPass);
        if ($cko && Error::NONE === $rpo) {
            return Yii::app()->res->output(Error::NONE, '密码修改成功');
        }  else {
            Yii::app()->res->output($rpo, '密码修改失败');
        }
    }
    

    /**
     * 用户登录
     */
    public function actionLogin()
    {
        //登录类型：0用户名，1手机号，2email，3sina，4qq，5wechat     登录名：登录类型为0,1,2时有效
        $loginType = Yii::app()->request->getPost('loginType');
        $loginName = Yii::app()->request->getPost('loginName');
        $userPass = Yii::app()->request->getPost('userPass');
        if (!empty($userPass) && strlen($userPass) < 32) {
            $userPass = md5($userPass);
        }
        $openid = Yii::app()->request->getPost('openid');
        $token = Yii::app()->request->getPost('token');
        $expiresIn = Yii::app()->request->getPost('expiresIn');
        
        //手机号登录参数验证
        $phoneCk = Rules::instance(array(
            'loginName' => $loginName,
            'userPass' => $userPass,
        ), array(
            array('loginName, userPass', 'required'),
            array('userPass', 'length', 'min' => 32, 'max' => 32),
            array('loginName', 'CPhoneNumberV'),
        ));
        //新浪第三方登录参数验证
        $sinaCk = Rules::instance(array(
            'openid' => $openid,
            'token' => $token,
            'expiresIn' => $expiresIn,
        ), array(
            array('openid, token', 'required'),
            array('openid', 'length', 'max' => 32),
            array('token', 'length', 'max' => 32),
            array('expiresIn', 'numerical', 'integerOnly' => true),
        ));
        //qq第三方登录参数验证
        $qqCk = $sinaCk;
        //wechat第三方登录参数验证
        $wechatCk = Rules::instance(array(
            'openid' => $openid,
            'token' => $token,
            'expiresIn' => $expiresIn,
        ), array(
            array('openid, token', 'required'),
            array('openid', 'length', 'max' => 32),
            array('token', 'length', 'max' => 256),
            array('expiresIn', 'numerical', 'integerOnly' => true),
        ));
        $loginParms = array(
            '1' => $phoneCk,
            '3' => $sinaCk,
            '4' => $qqCk,
            '5' => $wechatCk,
        );
        //按loginType选择参数验证
        $params = $loginParms[$loginType];
        if (empty($params) || !$params->validate()) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($params->getErrors()));
        }
        
        //$logMtd = array(.
        //    '1' => array('phoneLogin', 'loginName', 'userPass'),
        //    '3' => array('sinaLogin', 'openid', 'token', 'expiresIn'),
        //    '4' => array('qqLogin', 'openid', 'token', 'expiresIn'),
        //);
        //$rs = UserInfo::model()->$logMtd[$loginType][0](
        //        $params->$logMtd[$loginType][1], 
        //        $params->$logMtd[$loginType][2],
        //        isset($params->$logMtd[$loginType][3])?$params->$logMtd[$loginType][3]:NULL
        //    );
        
        $rs = Error::REQUEST_EXCEPTION;
        switch ($loginType) {
            case 1:
                $rs = UserInfo::model()->phoneLogin($loginName, $userPass);
                break;
            case 3:
                $rs = UserInfo::model()->sinaLogin($openid, $token, $expiresIn);
                break;
            case 4:
                $rs = UserInfo::model()->qqLogin($openid, $token, $expiresIn);
                break;
            case 5:
                $rs = UserInfo::model()->wechatLogin($openid, $token, $expiresIn);
                break;
        }
        
        if (Error::NONE === $rs) {
            $model = UserInfo::model()->findByPk(Yii::app()->user->id);
            $logf = $model->getLogUInfo();
            Yii::app()->res->output(Error::NONE, '登录成功', $logf);
        }  else {
            Yii::app()->res->output($rs, '登录失败');
        }
    }
    
    
    /**
     * 修改用户资料
     */
    public function actionUpdateInfo() 
    {
        $ck = Rules::instance(
            array(
                'nick_name' => Yii::app()->request->getPost('nickName'),
                'sex' => Yii::app()->request->getPost('sex'),
                'birth' => Yii::app()->request->getPost('birth'),
                'address' => Yii::app()->request->getPost('address'),
                'email' => Yii::app()->request->getPost('email'),
                'real_name' => Yii::app()->request->getPost('realName'),
                'contact_qq' => Yii::app()->request->getPost('contactQq'),
                'contact_phone' => Yii::app()->request->getPost('contactPhone'),
                'headImgId' => Yii::app()->request->getPost('headImgId'),
            ),
            array(
                array('nick_name', 'CZhEnV', 'min' => 4, 'max' => 24),
                array('sex', 'in', 'range' => array(1, 2)),
                array('birth', 'date', 'format' => 'yyyy-mm-dd'),
                array('address', 'CZhEnV', 'min' => 1, 'max' => 64),
                array('real_name', 'CZhEnV', 'min' => 1, 'max' => 32),
                array('email', 'email'),
                array('contact_qq, contact_phone', 'length', 'max'=>16),
                array('headImgId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = UserInfo::model()->findByPk(Yii::app()->user->id);
        if (0 == $model->is_regist) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '尚未完成注册');
        }
        
        $ck->setModelAttris($model);
        if (!$model->update()) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '修改资料失败');
        }

        if (!empty($ck->headImgId)) {
            if (!UserHeadImgMap::model()->setCurrImg($model->id, $ck->headImgId)) {
                return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '头像修改失败');
            }
        }

        $extendModel = UserInfoExtend::model()->find('u_id=:uid', array('uid' => $model->id));
        if (empty($extendModel)) {
            $extendModel = new UserInfoExtend();
            $extendModel->u_id = $model->id;
        }
        $ck->setModelAttris($extendModel);
        $extendModel->update();
        
        Yii::app()->res->output(Error::NONE, '修改资料成功');
    }
    
    
    /**
     * 绑定第三方
     */
    public function actionSetOpenid()
    {
        //登录类型：0用户名，1手机号，2email，3sina，4qq，5wechat     登录名：登录类型为0,1,2时有效
        $openidType = Yii::app()->request->getPost('openidType');
        $openid = Yii::app()->request->getPost('openid');
        $token = Yii::app()->request->getPost('token');
        $expiresIn = Yii::app()->request->getPost('expiresIn');
        
        //新浪第三方登录参数验证
        $ck = Rules::instance(array(
            'openidType' => $openidType,
            'openid' => $openid,
            'token' => $token,
            'expiresIn' => $expiresIn,
        ), array(
            array('openidType, openid, token', 'required'),
            array('openidType', 'in', 'range' => array(3, 4, 5)),
            array('openid', 'length', 'max' => 32),
            array('token', 'length', 'max' => 32),
            array('expiresIn', 'numerical', 'integerOnly' => true),
        ));
        if (!$ck->validate()) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = UserInfo::model()->findByPk(Yii::app()->user->id);
        $rs = Error::REQUEST_EXCEPTION;
        switch ($openidType) {
            case 3:
                $rs = $model->sinaSet($model->id, $ck->openid, $ck->token, $ck->expiresIn);
                break;
            case 4:
                $rs = $model->qqSet($model->id, $ck->openid, $ck->token, $ck->expiresIn);
                break;
            case 5:
                $rs = $model->wechatSet($model->id, $ck->openid, $ck->token, $ck->expiresIn);
                break;
        }
        
        if ($rs) {
            return Yii::app()->res->output(Error::NONE, '绑定成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '绑定失败');
    }
    
    
    /**
     * 系统异常
     */
    public function actionError() 
    {
        if ($error = Yii::app()->errorHandler->error) {
            if ($error['type'] == 'ResError') {
                Yii::app()->res->output($error['code'], $error['message']);
                return;
            }
        }
        Yii::app()->res->output(Error::REQUEST_EXCEPTION, "请求异常");
    }
    
    
    /**
     * 退出登录
     */
    public function actionLogout()
	{
		Yii::app()->user->logout();
		Yii::app()->res->output(0, "退出登录");
	}
    
    
    /**
     * 未登录url
     */
    public function actionNotLogin()
	{
		Yii::app()->res->output(Error::LOGIN_INVALID, "登录无效");
	}
    
    
    public function  actionSessionTest()
    {
        /*
        * 得到sessionID号
        * 计算出来存在memcached的key值是多少.
        */
        $intent_extras['class'] = 'activity.VR365VideoPlayer';
        $intent_extras['VIDEO_NAME'] = '宠爱';
        $intent_extras['VIDEO_URI'] = 'http://res.vr365.cc/test3.m3u8';
        $intent_extras['VIDEO_ID'] = '2';
                
        //http://res.vr365.cc/test3.m3u8
        
        //Yii::app()->jPush->push(array('alias'=>array('UID_2')),'宠爱',$intent_extras);
        //Yii::app()->jPush->getAliasDevices('UID_2');
        Yii::app()->jPush->getDeviceTagAlias('0007f4da53d');
        Yii::app()->jPush->updateDeviceTag('0007f4da53d','袁老师','老师');
        Yii::app()->jPush->getDeviceTagAlias('0007f4da53d');

        //$sessionId = Yii::app()->session->sessionID;
        //echo "key:", $key = CCacheHttpSession::CACHE_KEY_PREFIX.$sessionId, '<br/>';
         
        /**
         * 这相当于是直接使用Memcached 连接，和session没有任何挂钩，
         * 我们来看一下session的数据是否真的就存在了memcached里边。
         * 通过计算出来的key直接用 get命令获取然后将数据打印出来就能看到值了。
         * 测试的时候先登录噢，别不登录就开始测试估计会获取不到值，以为有问题呢！
         */
        //$mem =  Yii::app()->sessionCache;
        //$data =$mem->get($key);
        //var_dump(unserialize($data));
    }
    
    
    
    
    
}
