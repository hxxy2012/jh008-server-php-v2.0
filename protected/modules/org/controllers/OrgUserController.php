<?php

class OrgUserController extends OrgController {

    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + login', // we only allow deletion via POST request
        );
    }

    public function accessRules() {
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('login', 'MemcacheAdd'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                //'actions' => array(''),
                'users' => array('@'),
            ),
            //array('allow',
            //    'actions' => array(''),
            //	'expression' => 'yii::app()->user->superManager()',
            //),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * 验证登录信息
     */
    public function actionLoginInfo() {
        echo 'success<br>';
        echo 'id:' . Yii::app()->user->id . '<br>';
    }

    /**
     * 管理员列表
     */
    public function actionManagers() {
        $ck = Rules::instance(
                        array(
                    'page' => Yii::app()->request->getParam('page'),
                    'size' => Yii::app()->request->getParam('size'),
                        ), array(
                    array('page, size', 'required'),
                    array('page, size', 'numerical', 'integerOnly' => true),
                        )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $rst = ManagerInfo::model()->managers(NULL, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'managers success', $rst);
    }

    /**
     * 城市管理员列表
     */
    public function actionCityManagers() {
        $ck = Rules::instance(
                        array(
                    'page' => Yii::app()->request->getParam('page'),
                    'size' => Yii::app()->request->getParam('size'),
                        ), array(
                    array('page, size', 'required'),
                    array('page, size', 'numerical', 'integerOnly' => true),
                        )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $rst = ManagerCityMap::model()->cityManagers(ConstCityManagerStatus::CITY_MANAGER, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'city managers success', $rst);
    }

    /**
     * 城市操作员列表
     */
    public function actionCityOperators() {
        $ck = Rules::instance(
                        array(
                    'cityId' => Yii::app()->request->getParam('cityId'),
                    'page' => Yii::app()->request->getParam('page'),
                    'size' => Yii::app()->request->getParam('size'),
                        ), array(
                    array('page, size', 'required'),
                    array('cityId, page, size', 'numerical', 'integerOnly' => true),
                        )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $rst = ManagerCityMap::model()->cityManagers(ConstCityManagerStatus::CITY_OPERATOR, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'city operators success', $rst);
    }

    /**
     * 管理员登录
     */
    public function actionLogin() {
        $ck = Rules::instance(
                        array(
                    'u_name' => Yii::app()->request->getPost('uName'),
                    'u_pass' => Yii::app()->request->getPost('uPass'),
                    'rememberMe' => Yii::app()->request->getPost('rememberMe', 0),
                        ), array(
                    array('u_name, u_pass, rememberMe', 'required'),
                    array('u_name, u_pass', 'length', 'max' => 16),
                    array('rememberMe', 'in', 'range' => array(0, 1)),
                        )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $r = ManagerInfo::model()->login($ck->u_name, $ck->u_pass, $ck->rememberMe);
        if ($r) {
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
    public function actionCityLogin() {
        $ck = Rules::instance(
                        array(
                    'u_name' => Yii::app()->request->getPost('uName'),
                    'u_pass' => Yii::app()->request->getPost('uPass'),
                    'rememberMe' => Yii::app()->request->getPost('rememberMe', 0),
                        ), array(
                    array('u_name, u_pass, rememberMe', 'required'),
                    array('u_name, u_pass', 'length', 'max' => 16),
                    array('rememberMe', 'in', 'range' => array(0, 1)),
                        )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $r = CityManager::model()->login($ck->u_name, $ck->u_pass, $ck->rememberMe);
        if ($r) {
            $cityManager = ManagerCityMap::model()->cityManager(Yii::app()->user->id);
            //AdminOperateLog::model()->log(Yii::app()->user->id, '登录了系统');
            //echo 'id:' . Yii::app()->user->id . '<br>';
            //echo 'type:' . Yii::app()->user->type . '<br>';
            return Yii::app()->res->output(Error::NONE, 'city manager login success', array('city_manager' => $cityManager));
        }

        Yii::app()->res->output(Error::USERNAME_OR_USERPASS_INVALID, '登录失败');
    }

    /**
     * 修改自己的密码
     */
    public function actionUpPass() {
        $ck = Rules::instance(
                        array(
                    'oldPass' => Yii::app()->request->getPost('oldPass'),
                    'newPass' => Yii::app()->request->getPost('newPass'),
                        ), array(
                    array('oldPass, newPass', 'length', 'max' => 16),
                        )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $model = Yii::app()->user;

        //旧密码不能为空
        if (empty($ck->oldPass)) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, 'user pass is wrong');
        }

        //旧密码与真实密码不一致
        if (md5($model->salt . $ck->oldPass) != $model->u_pass) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, 'user pass is wrong');
        }

        $rst = OrgInfo::model()->up($model, NULL, $ck->newPass);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'manager self update success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'manager self update fail');
    }

    /**
     * 退出登录
     */
    public function actionLogout() {
        Yii::app()->user->logout();
        if (Yii::app()->request->isAjaxRequest) {
            return Yii::app()->res->output(Error::NONE, 'loginout success');
        }
        $this->redirect(Yii::app()->homeUrl);
    }

 

}
