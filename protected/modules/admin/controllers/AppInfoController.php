<?php

class AppInfoController extends AdminController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + upload, addApp, updateApp, delApp', // we only allow deletion via POST request
		);
	}

    
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions' => array(''),
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
     * 添加app
     */
    public function actionAddApp()
    {
        $ck = Rules::instance(
            array(
                'type' => Yii::app()->request->getPost('type'),
                'code' => Yii::app()->request->getPost('code'),
                'name' => Yii::app()->request->getPost('name'),
                'descri' => Yii::app()->request->getPost('descri'),
                'up_id' => Yii::app()->request->getPost('upId'),
            ),
            array(
                array('type, code, name, descri', 'required'),
                array('type', 'in', 'range' => array(1)),
                array('code, up_id', 'numerical', 'integerOnly' => true),
                array('name', 'CZhEnV', 'min' => 1, 'max' => 16),
                array('descri', 'CZhEnV', 'min' => 1, 'max' => 60),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = new AppInfo();
        $ck->setModelAttris($model);
        $r = $model->ins();
        
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '添加了app');
            return Yii::app()->res->output(Error::NONE, '添加app成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '添加app失败');
    }

    
    /**
     * 修改app
     */
    public function actionUpdateApp()
    {
        $ck = Rules::instance(
            array(
                'appId' => Yii::app()->request->getPost('appId'),
                'type' => Yii::app()->request->getPost('type'),
                'code' => Yii::app()->request->getPost('code'),
                'name' => Yii::app()->request->getPost('name'),
                'descri' => Yii::app()->request->getPost('descri'),
                'up_id' => Yii::app()->request->getPost('upId'),
            ),
            array(
                array('appId', 'required'),
                array('type', 'in', 'range' => array(1)),
                array('appId, code, up_id', 'numerical', 'integerOnly' => true),
                array('name', 'CZhEnV', 'min' => 1, 'max' => 16),
                array('descri', 'CZhEnV', 'min' => 1, 'max' => 60),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = AppInfo::model()->findByPk($ck->appId);
        if (empty($model)) {
            return Yii::app()->res->output(Error::RECORD_NOT_EXIST, 'app版本不存在');
        }
        $ck->setModelAttris($model);
        $r = $model->updateApp();
        
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '修改了app');
            return Yii::app()->res->output(Error::NONE, '修改成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '修改失败');
    }
    

    /**
     * 删除app
     */
    public function actionDelApp()
    {
        $ck = Rules::instance(
            array(
                'appId' => Yii::app()->request->getPost('appId'),
            ),
            array(
                array('appId', 'required'),
                array('appId', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = AppInfo::model()->findByPk($ck->appId);
        if (empty($model)) {
            return Yii::app()->res->output(Error::RECORD_NOT_EXIST, 'app版本不存在');
        }
        $r = $model->del();
        
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '删除了app');
            return Yii::app()->res->output(Error::NONE, '删除成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '删除失败');
    }
    
    
    /**
     * 上传apk
     */
    public function actionUpload()
    {
        $ck = Rules::instance(
            array(
                'file' => CUploadedFile::getInstanceByName('file'),
            ),
            array(
                array('file', 'required'),
                array('file', 'file', 'allowEmpty' => true,
                    //'types' => 'jpg',
                    //'maxSize' => 512 * 512 * 1, // 1MB = 1024*1024
                    //'tooLarge' => '上传文件超过 512kb，无法上传。',
                ),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        //保存文件到指定目录
        $fu = Yii::app()->fileUpload->upAdminFile($ck->file);
        if (!$fu) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '文件上传失败');
        }
        
        //上传文件信息表插入
        $upInfo = new UpInfo();
        if (!$upInfo->ins($ck->file->name, $fu)) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '文件保存失败');
        }
        
        $upAdmin = new UpAdminMap();
        //文件上传者关联表插入
        if (!$upAdmin->ins($upInfo->id, Yii::app()->user->id)) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '文件保存失败');
        }

        AdminOperateLog::model()->log(Yii::app()->user->id, '上传了文件');
        Yii::app()->res->output(Error::NONE, '文件上传成功', array('up_id' => $upInfo->id));
    }
    
    
    /**
     * 获取app版本列表
     */
    public function actionGetApps()
    {
        $ck = Rules::instance(
            array(
                'type' => Yii::app()->request->getParam('type'),
                'keyWords' => Yii::app()->request->getParam('keyWords'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('page, size', 'required'),
                array('type', 'in', 'range' => array(1)),
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
                array('page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = AppInfo::model()->searchApps($ck->type, $ck->keyWords, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
    
    /**
     * 获取app版本列表（回收站）
     */
    public function actionGetDelApps()
    {
        $ck = Rules::instance(
            array(
                'type' => Yii::app()->request->getParam('type'),
                'keyWords' => Yii::app()->request->getParam('keyWords'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('page, size', 'required'),
                array('type', 'in', 'range' => array(1)),
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
                array('page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = AppInfo::model()->searchApps($ck->type, $ck->keyWords, $ck->page, $ck->size, TRUE);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
}
