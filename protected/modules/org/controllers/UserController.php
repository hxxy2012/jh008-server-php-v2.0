<?php

class UserController extends OrgController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + modifyPassword, logout', // we only allow deletion via POST request
		);
	}

    
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array(''),
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
     * 个人信息
     */
    public function actionInfo()
    {
        $user = UserInfo::model()->findByPk(Yii::app()->user->id);
        if (empty($user)) {
            return Yii::app()->res->output(Error::USER_NOT_EXIST, 'user not exist');
        }
        $org = OrgInfoO::model()->getByUidO($user->id);
        if (empty($org)) {
            return Yii::app()->res->output(Error::RECORD_NOT_EXIST, 'org not exist');
        }
        $city = OrgCityMapO::model()->getCityByOrgO($org->id);
        if (empty($city)) {
            return Yii::app()->res->output(Error::RECORD_NOT_EXIST, 'city not exist');
        }
        $rstInfo = array();
        $rstInfo['pho_num'] = $user->pho_num;
        $rstInfo['account_balance'] = $user->account_balance;
        $rstInfo['progress_act_num'] = ActInfoO::model()->progressActNumO($org->id);
        $rstInfo['city_name'] = $city->name;
        $rstInfo['org'] = OrgInfoO::model()->profileO(NULL, $org);
        return Yii::app()->res->output(Error::NONE, 'user info success', array('user' => $rstInfo));
    }
    
    
    /**
     * 修改密码
     */
    public function actionModifyPassword()
    {
        $ck = Rules::instance(
            array(
                'oldPassword' => Yii::app()->request->getPost('oldPassword'),
                'newPassword' => Yii::app()->request->getPost('newPassword'),
            ),
            array(
                array('oldPassword, newPassword', 'required'),
                array('oldPassword, newPassword', 'length', 'min' => 32, 'max' => 32),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $r = UserInfo::model()->rePass(Yii::app()->user->id, $ck->oldPassword, $ck->newPassword);
        if (Error::NONE == $r) {
            return Yii::app()->res->output(Error::NONE, 're pass success');
        }
        Yii::app()->res->output($r, 're pass fail');
    }
    
    
    /**
     * 搜索用户
     */
    public function actionSearchUser()
    {
        $ck = Rules::instance(
            array(
                'keyWords' => Yii::app()->request->getParam('keyWords'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('page, size', 'required'),
                array('page, size', 'numerical', 'integerOnly' => TRUE),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserInfoO::model()->usersO($ck->keyWords, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'search user success', $rst);
    }
    
    
}
