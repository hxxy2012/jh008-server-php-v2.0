<?php

class UserController extends ActController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + updateFullprofile, addPhoto, delPhoto, addComment, delComment, imgUp, setBaiduPush, feedback', // we only allow deletion via POST request
		);
	}

    
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('profile', 'photos', 'comments'),
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
     * 用户主页
     */
    public function actionProfile()
    {
        $ck = Rules::instance(
            array(
                'uid' => Yii::app()->request->getParam('uid', Yii::app()->user->id),
                'cityId' => Yii::app()->request->getParam('cityId'),
            ),
            array(
                array('uid, cityId', 'required'),
                array('uid, cityId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $user = UserInfo::model()->profile(NULL, $ck->uid, $ck->cityId, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id, NULL, TRUE, TRUE);
        Yii::app()->res->output(Error::NONE, 'user profile success', array('user' => $user));
    }
    
    
    /**
     * 用户照片墙
     */
    public function actionPhotos()
    {
        $ck = Rules::instance(
            array(
                'uid' => Yii::app()->request->getParam('uid', Yii::app()->user->id),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('uid, page, size', 'required'),
                array('uid, page, size', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserPhotoWall::model()->photos($ck->uid, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'user photos success', $rst);
    }
    
    
    /**
     * 用户完整信息（须登录获取自己的）
     */
    public function actionFullprofile()
    {
        $ck = Rules::instance(
            array(
                'uid' => Yii::app()->request->getParam('uid', Yii::app()->user->id),
                'cityId' => Yii::app()->request->getParam('cityId'),
            ),
            array(
                array('uid, cityId', 'required'),
                array('uid, cityId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        if ($ck->uid != Yii::app()->user->id) {
            return Yii::app()->res->output(Error::PERMISSION_DENIED, '权限不够');
        }
        
        $user = UserInfo::model()->fullProfile(NULL, $ck->uid, $ck->cityId, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'user fullprofile success', array('user' => $user));
    }
    
    
    /**
     * 用户获取系统信息
     */
    public function actionSystemMsgs()
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
        
        $rst = SystemMsgUserMap::model()->userSystemMsgs(Yii::app()->user->id, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'user system msgs success', $rst);
    }
    
    
    /**
     * 设置修改个人主页
     */
    public function actionUpdateFullprofile()
    {
        $ck = Rules::instance(
            array(
                'nick_name' => Yii::app()->request->getPost('nickName'),
                'sex' => Yii::app()->request->getPost('sex'),
                'birth' => Yii::app()->request->getPost('birth'),
                'intro' => Yii::app()->request->getPost('intro'),
                'address' => Yii::app()->request->getPost('address'),
                'email' => Yii::app()->request->getPost('email'),
                'real_name' => Yii::app()->request->getPost('realName'),
                'contact_qq' => Yii::app()->request->getPost('contactQq'),
                'contact_phone' => Yii::app()->request->getPost('contactPhone'),
                'hobby' => Yii::app()->request->getPost('hobby'),
                'headImgId' => Yii::app()->request->getPost('headImgId'),
            ),
            array(
                array('nick_name', 'CZhEnV', 'min' => 1, 'max' => 16),
                array('sex', 'in', 'range' => array(1, 2)),
                array('birth', 'date', 'format' => 'yyyy-mm-dd'),
                array('address', 'CZhEnV', 'min' => 1, 'max' => 64),
                array('real_name, hobby', 'CZhEnV', 'min' => 1, 'max' => 32),
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

        $isNew = FALSE;
        $extendModel = UserInfoExtend::model()->find('u_id=:uid', array('uid' => $model->id));
        if (empty($extendModel)) {
            $extendModel = new UserInfoExtend();
            $extendModel->u_id = $model->id;
            $isNew = TRUE;
        }
        $ck->setModelAttris($extendModel);
        if ($isNew) {
            $extendModel->save();
        }  else {
            $extendModel->update();
        }
        
        Yii::app()->res->output(Error::NONE, '修改资料成功');
    }
    
    
    /**
     * 更新照片墙
     */
    public function actionUpdatePhotos()
    {
        $ck = Rules::instance(
            array(
                'imgIds' => Yii::app()->request->getPost('imgIds', array()),
            ),
            array(
                array('imgIds', 'CArrNumV', 'maxLen' => 9),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserPhotoWall::model()->updatePhotos(Yii::app()->user->id, $ck->imgIds);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'update user photo wall success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'update user photo wall fail');
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
        
        $imgUpUser = new ImgUpUserMap();
        //图像上传者关联表插入
        if (!$imgUpUser->ins($imgInfo->id, Yii::app()->user->id)) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '图片关联保存失败');
        }
            
        Yii::app()->res->output(Error::NONE, '图片上传成功', array('img_id' => $imgInfo->id));
    }
    
    
    /**
     * 设置百度推送最后登录信息
     */
    public function actionSetBaiduPush()
    {
        $ck = Rules::instance(
            array(
                'last_login_city_id' => Yii::app()->request->getPost('cityId'),
                'last_login_platform' => Yii::app()->request->getPost('platform'),
                'baidu_user_id' => Yii::app()->request->getPost('baiduUserId'),
                'baidu_channel_id' => Yii::app()->request->getPost('baiduChannelId'),
            ),
            array(
                //array('last_login_platform, baidu_user_id, baidu_channel_id', 'required'),
                array('last_login_city_id', 'numerical', 'integerOnly' => true),
                array('last_login_platform', 'in', 'range' => array(3, 4)),
                array('baidu_user_id, baidu_channel_id', 'length', 'max' => 256),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = UserInfoExtend::model()->get(Yii::app()->user->id);
        if (empty($model)) {
            return Yii::app()->res->output(Error::RECORD_NOT_EXIST, 'user info extend is not exist');
        }
        
        $ck->setModelAttris($model);
        
        $rst = $model->update();
        if ($rst) {
            UserInfoExtend::model()->delOtherPushParm(Yii::app()->user->id, $ck->baidu_user_id, $ck->baidu_channel_id);
            return Yii::app()->res->output(Error::NONE, 'user set baidu push success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'user set baidu push fail');
    }
    
    
    /**
     * 用户意见反馈
     */
    public function actionFeedback()
    {
        $ck = Rules::instance(
            array(
                'city_id' => Yii::app()->request->getPost('cityId'),
                'u_id' => Yii::app()->request->getPost('uid'),
                'content' => Yii::app()->request->getPost('intro'),
                'imgIds' => Yii::app()->request->getPost('imgIds', array()),
                'lon' => Yii::app()->request->getPost('lon'),
                'lat' => Yii::app()->request->getPost('lat'),
                'address' => Yii::app()->request->getPost('address'),
            ),
            array(
                array('city_id, u_id', 'numerical', 'integerOnly' => true),
                array('content', 'length', 'max' => 256),
                array('imgIds', 'CArrNumV', 'maxLen' => 3),
                array('lon, lat', 'numerical', 'integerOnly' => FALSE),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = new UserFeedback();
        $ck->setModelAttris($model);
        $rst = UserFeedback::model()->add($model, $ck->imgIds);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'user feedback news success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'user feedback news fail');
    }
    
}
