<?php

class OrgController extends ActController 
{

    public function filters() 
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + lov_v2, unLov_v2', // we only allow deletion via POST request
        );
    }

    
    public function accessRules() 
    {
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('orgs_v2', 'org_v2', 'acts_v2', 'managers_v2', 'lovs_v2'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    
    /**
     * 社团列表
     */
    public function actionOrgs_v2() 
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'keyWords' => Yii::app()->request->getParam('keyWords'),
                'page' => Yii::app()->request->getParam('page', 1),
                'size' => Yii::app()->request->getParam('size', 10),
            ),
            array(
                array('cityId, page, size', 'required'),
                array('cityId, page, size', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = OrgCityMap::model()->orgs($ck->cityId, $ck->keyWords, $ck->page, $ck->size, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'orgs success', $rst);
    }

    
    /**
     * 社团详情
     */
    public function actionOrg_v2() 
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
        
        $org = OrgInfo::model()->fullProfile($ck->orgId, NULL, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'org success', array('org' => $org));
    }

    
    /**
     * 关注社团
     */
    public function actionLov_v2() 
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
        
        $rst = OrgLovUserMap::model()->addLove($ck->orgId, Yii::app()->user->id);
        
        $isLoved = OrgLovUserMap::model()->isLoved($ck->orgId, Yii::app()->user->id);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'org add lov success', array('is_loved' => $isLoved));
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'org add lov fail', array('is_loved' => $isLoved));
    }

    
    /**
     * 取消关注社团
     */
    public function actionUnLov_v2() 
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
        
        $rst = OrgLovUserMap::model()->delLove($ck->orgId, Yii::app()->user->id);
        
        $isLoved = OrgLovUserMap::model()->isLoved($ck->orgId, Yii::app()->user->id);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'org del lov success', array('is_loved' => $isLoved));
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'org del lov fail', array('is_loved' => $isLoved));
    }

    
    /**
     * 社团近期活动
     */
    public function actionActs_v2() 
    {
        $ck = Rules::instance(
            array(
                'orgId' => Yii::app()->request->getParam('orgId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('orgId, page, size', 'required'),
                array('orgId, page, size', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = OrgInfo::model()->acts($ck->orgId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'org acts success', $rst);
    }

    
    /**
     * 社团管理员
     */
    public function actionManagers_v2() 
    {
        $ck = Rules::instance(
            array(
                'orgId' => Yii::app()->request->getParam('orgId'),
                'page' => Yii::app()->request->getParam('page', 1),
                'size' => Yii::app()->request->getParam('size', 10),
            ), array(
                array('orgId, page, size', 'required'),
                array('orgId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $org = OrgInfo::model()->findByPk($ck->orgId);
        if (empty($org)) {
            return Yii::app()->res->output(Error::RECORD_NOT_EXIST, 'org not exist');
        }
        
        $rst = OrgManagerMap::model()->managers($ck->orgId, $ck->page, $ck->size, NULL, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        
        //社团所有者也是管理员
        if (!empty($org->own_id)) {
            $user = UserInfo::model()->profile(NULL, $org->own_id, NULL, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id, NULL, FALSE, FALSE);
            if (!empty($user)) {
                $rst['total_num'] += 1;
                array_unshift($rst['users'], $user);
            }
        }
        Yii::app()->res->output(Error::NONE, 'org managers success', $rst);
    }
    
    
    /**
     * 社团关注者
     */
    public function actionLovs_v2() 
    {
        $ck = Rules::instance(
            array(
                'orgId' => Yii::app()->request->getParam('orgId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ), array(
                array('orgId, page, size', 'required'),
                array('orgId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = OrgLovUserMap::model()->users($ck->orgId, $ck->page, $ck->size, NULL, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'org lovs success', $rst);
    }

}
