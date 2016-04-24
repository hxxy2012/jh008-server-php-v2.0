<?php

class BusinessInfoController extends AdminController
{
    
    public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + add, update, del', // we only allow deletion via POST request
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
     * 添加商家
     */
    public function actionAdd()
    {
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
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $model = new BusinessInfo();
        $ck->setModelAttris($model);
        $r = BusinessInfo::model()->regist($model, $ck->u_name, $ck->u_pass);
        
        if ($r && !empty($ck->logoImgId)) {
            if (!BusinessLogoImgMap::model()->setCurrImg($model->id, $ck->logoImgId)) {
                return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'logo添加失败');
            }
        }

        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '添加了商家');
            return Yii::app()->res->output(Error::NONE, '添加成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '添加失败');
    }
    
    
    /**
     * 修改商家资料
     */
    public function actionUpdate() 
    {
        $ck = Rules::instance(
            array(
                'businessId' => Yii::app()->request->getPost('businessId'),
                'u_name' => Yii::app()->request->getPost('uName'),
                'uPass' => Yii::app()->request->getPost('uPass'),
                'name' => Yii::app()->request->getPost('name'),
                'address' => Yii::app()->request->getPost('address'),
                'contact_phone' => Yii::app()->request->getPost('contactPhone'),
                'contact_email' => Yii::app()->request->getPost('contactEmail'),
                'contact_descri' => Yii::app()->request->getPost('contactDescri'),
                'logoImgId' => Yii::app()->request->getPost('logoImgId'),
            ),
            array(
                array('businessId', 'required'),
                array('businessId', 'numerical', 'integerOnly' => TRUE),
                array('u_name', 'CZhEnV', 'min' => 1, 'max' => 16),
                array('uPass', 'CUserPassV'),
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
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = BusinessInfo::model()->findByPk($ck->businessId);
        
        if (!empty($ck->uPass)) {
            $model->u_pass = md5($model->salt . $ck->uPass);
        }
        $ck->setModelAttris($model);
        $r = $model->update();
        
        if (!empty($ck->logoImgId)) {
            if (!BusinessLogoImgMap::model()->setCurrImg($model->id, $ck->logoImgId)) {
                return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'logo修改失败');
            }
        }
        
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '添加了商家资料');
            return Yii::app()->res->output(Error::NONE, '修改成功');
        }
        Yii::app()->res->output(Error::REQUEST_EXCEPTION, '修改失败');
    }
    
    
    /**
     * 删除商家
     */
    public function actionDel()
    {
        $ck = Rules::instance(
            array(
                'businessId' => Yii::app()->request->getPost('businessId'),
            ),
            array(
                array('businessId', 'required'),
                array('businessId', 'numerical', 'integerOnly' => TRUE),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $r = BusinessInfo::model()->del($ck->businessId);
        
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '删除了商家');
            return Yii::app()->res->output(Error::NONE, '删除成功');
        }
        Yii::app()->res->output(Error::REQUEST_EXCEPTION, '删除失败');
    }
    
    
    /**
     * 获取商家列表
     */
    public function actionGetBusinesses()
    {
        $ck = Rules::instance(
            array(
                'keyWords' => Yii::app()->request->getParam('keyWords'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('page, size', 'required'),
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
                array('page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = BusinessInfo::model()->getUsers($ck->keyWords, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
    
    /**
     * 获取商家列表（回收站）
     */
    public function actionGetDelBusinesses() 
    {
        $ck = Rules::instance(
            array(
                'keyWords' => Yii::app()->request->getParam('keyWords'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('page, size', 'required'),
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
                array('page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = BusinessInfo::model()->getUsers($ck->keyWords, $ck->page, $ck->size, TRUE);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
    
    /**
     * 获取商家资料
     */
    public function actionGetInfo() 
    {
        $ck = Rules::instance(
            array(
                'businessId' => Yii::app()->request->getParam('businessId'),
            ),
            array(
                array('businessId', 'required'),
                array('businessId', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $business = BusinessInfo::model()->getInfo($ck->businessId);
        Yii::app()->res->output(Error::NONE, '获取成功', array('business' => $business));
    }

}