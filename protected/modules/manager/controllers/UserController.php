<?php
class UserController extends ManagerController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + setVip, dealVipApply, setVipInterview, setTopVips', // we only allow deletion via POST request
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
     * 用户搜索
     */
    public function actionUsers()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'sex' => Yii::app()->request->getParam('sex'),
                'keyWords' => Yii::app()->request->getParam('keyWords'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('page, size', 'required'),
                array('cityId, page, size', 'numerical', 'integerOnly' => true),
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
                array('sex', 'in', 'range' => array('1', '2')),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserInfoM::model()->usersM($ck->cityId, $ck->sex, $ck->keyWords, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'users success', $rst);
    }
    
    
    /**
     * 用户信息
     */
    public function actionUser()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'uid' => Yii::app()->request->getParam('uid'),
            ),
            array(
                array('uid', 'required'),
                array('cityId, uid', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserInfoM::model()->fullProfileM(NULL, $ck->uid, $ck->cityId);
        Yii::app()->res->output(Error::NONE, 'user success', $rst);
    }
    
    
    /**
     * 分享过的活动
     */
    public function actionShareActs()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'uid' => Yii::app()->request->getParam('uid'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('uid, page, size', 'required'),
                array('cityId, uid, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = ActShareM::model()->shareActsM($ck->uid, $ck->cityId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'share acts success', $rst);
    }
    
    
    /**
     * 收藏过的活动
     */
    public function actionLovActs()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'uid' => Yii::app()->request->getParam('uid'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('uid, page, size', 'required'),
                array('cityId, uid, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = ActLovUserMapM::model()->lovActsM($ck->uid, $ck->cityId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'lov acts success', $rst);
    }
    
    
    /**
     * 报名过的活动
     */
    public function actionEnrollActs()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'uid' => Yii::app()->request->getParam('uid'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('uid, page, size', 'required'),
                array('cityId, uid, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = ActEnrollM::model()->enrollActsM($ck->uid, $ck->cityId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'enroll acts success', $rst);
    }
    
    
    /**
     * 签到过的活动
     */
    public function actionCheckinActs()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'uid' => Yii::app()->request->getParam('uid'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('uid, page, size', 'required'),
                array('cityId, uid, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = ActCheckinM::model()->checkinActsM($ck->uid, $ck->cityId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'checkin acts success', $rst);
    }
    
    
    /**
     * 发布过的动态
     */
    public function actionDynamics()
    {
        $ck = Rules::instance(
            array(
                'uid' => Yii::app()->request->getParam('uid'),
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
        
        $rst = UserDynamicM::model()->dynamics($ck->uid, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'dynamics success', $rst);
    }
    
    
    /**
     * 达人搜索
     */
    public function actionVips()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'sex' => Yii::app()->request->getParam('sex'),
                'keyWords' => Yii::app()->request->getParam('keyWords'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('cityId, page, size', 'required'),
                array('cityId, page, size', 'numerical', 'integerOnly' => true),
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
                array('sex', 'in', 'range' => array('1', '2')),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserCityMapM::model()->vipsM($ck->cityId, $ck->sex, $ck->keyWords, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'vips success', $rst);
    }
    
    
    /**
     * 获取置顶的达人
     */
    public function actionTopVips()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'tagId' => Yii::app()->request->getParam('tagId'),
                'page' => Yii::app()->request->getParam('page', 1),
                'size' => Yii::app()->request->getParam('size', 10),
            ),
            array(
                array('cityId, page, size', 'required'),
                array('cityId, tagId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $tagId = $ck->tagId;
        if (empty($ck->tagId)) {
            $tagId = 0;
        }
        $rst = KeyValInfoM::model()->getRecommendUsersM($ck->cityId, $tagId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'top vips success', $rst);
    }
    
    
    /**
     * 达人标签列表
     */
    public function actionTags()
    {
        $ck = Rules::instance(
            array(
                'type' => Yii::app()->request->getParam('type'),
                'keyWords' => Yii::app()->request->getParam('keyWords'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('type, page, size', 'required'),
                array('type', 'in', 'range' => array(1, 2)),
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
                array('page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = array();
        if (1 == $ck->type) {
            $rst = VipTagM::model()->tagsM($ck->keyWords, $ck->page, $ck->size);
        }  else {
            $rst = UserTagM::model()->tagsM($ck->keyWords, $ck->page, $ck->size);
        }
        Yii::app()->res->output(Error::NONE, 'user tags success', $rst);
    }
    
    
    /**
     * 达人申请
     */
    public function actionVipApplys()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
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
        
        $rst = VipApplyCityMapM::model()->applysM($ck->cityId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'vip applys success', $rst);
    }
    
    
    /**
     * 获取意见反馈
     */
    public function actionFeedbacks()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'uid' => Yii::app()->request->getParam('uid'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('page, size', 'required'),
                array('cityId, uid, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserFeedbackM::model()->feedbacksM($ck->cityId, $ck->uid, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'feedbacks success', $rst);
    }
    
    
    /**
     * 设置达人
     */
    public function actionSetVip()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getPost('cityId'),
                'vipId' => Yii::app()->request->getPost('vipId'),
                'status' => Yii::app()->request->getPost('status'),
            ),
            array(
                array('cityId, vipId, status', 'required'),
                array('cityId, vipId', 'numerical', 'integerOnly' => TRUE),
                array('status', 'in', 'range' => array(
                    ConstStatus::DELETE,
                    ConstStatus::NORMAL,
                    )),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = FALSE;
        if (ConstStatus::DELETE == $ck->status) {
            $rst = UserCityMapM::model()->delM($ck->vipId, $ck->cityId);
        }  else {
            $rst = UserCityMapM::model()->updateM(NULL, $ck->vipId, $ck->cityId);
        }
        
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'set vip success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'set vip fail');
    }
    
    
    /**
     * 处理达人申请
     */
    public function actionDealVipApply()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getPost('cityId'),
                'applyId' => Yii::app()->request->getPost('applyId'),
            ),
            array(
                array('cityId, applyId', 'required'),
                array('cityId, applyId', 'numerical', 'integerOnly' => TRUE),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = VipApplyM::model()->dealM($ck->applyId, $ck->cityId);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'vip apply deal success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'vip apply deal fail');
    }
    
    
    /**
     * 设置达人专访
     */
    public function actionSetVipInterview()
    {
        $ck = Rules::instance(
            array(
                'vipId' => Yii::app()->request->getPost('vipId'),
                'newsId' => Yii::app()->request->getPost('newsId'),
            ),
            array(
                array('vipId', 'required'),
                array('vipId, newsId', 'numerical', 'integerOnly' => TRUE),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        if (empty($ck->newsId)) {
            //删除达人专访
            $rst = VipInterviewM::model()->delM($ck->vipId, $ck->newsId);
        }  else {
            //更新达人专访
            $rst = VipInterviewM::model()->updateM(NULL, $ck->vipId, $ck->newsId);
        }
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'set vip interview success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'set vip interview fail');
    }
    
    
    /**
     * 设置达人置顶排序
     */
    public function actionSetTopVips()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getPost('cityId'),
                'tagId' => Yii::app()->request->getPost('tagId'),
                'vipIds' => Yii::app()->request->getPost('vipIds', array()),
            ),
            array(
                array('cityId', 'required'),
                array('cityId, tagId', 'numerical', 'integerOnly' => TRUE),
                array('vipIds', 'CArrNumV', 'maxLen' => 10),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $tagId = $ck->tagId;
        if (empty($ck->tagId)) {
            $tagId = 0;
        }
        $rst = KeyValInfoM::model()->upRecommendUsersM($ck->cityId, $tagId, $ck->vipIds);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'set top vips success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'set top vips fail');
    }

    
    /**
     * 添加达人标签
     */
    public function actionAddTag()
    {
        $ck = Rules::instance(
            array(
                'type' => Yii::app()->request->getPost('type'),
                'name' => Yii::app()->request->getPost('name'),
            ),
            array(
                array('type, name', 'required'),
                array('type', 'in', 'range' => array(1, 2)),
                array('name', 'CZhEnV', 'min' => 1, 'max' => 8),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = FALSE;
        if (1 == $ck->type) {
            $rst = VipTagM::model()->addM(NULL, $ck->name);
        }  else {
            $rst = UserTagM::model()->addM(NULL, $ck->name);
        }
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'add user tag success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'add user tag fail');
    }
    
    
    /**
     * 修改达人标签
     */
    public function actionUpdateTag() 
    {
        $ck = Rules::instance(
            array(
                'type' => Yii::app()->request->getPost('type'),
                'tagId' => Yii::app()->request->getPost('tagId'),
                'name' => Yii::app()->request->getPost('name'),
                'status' => Yii::app()->request->getPost('status'),
            ),
            array(
                array('type, tagId', 'required'),
                array('type', 'in', 'range' => array(1, 2)),
                array('tagId', 'numerical', 'integerOnly' => true),
                array('name', 'CZhEnV', 'min' => 1, 'max' => 8),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = FALSE;
        if (1 == $ck->type) {
            $rst = VipTagM::model()->updateM(NULL, $ck->tagId, $ck->name, $ck->status);
        }  else {
            $rst = UserTagM::model()->updateM(NULL, $ck->tagId, $ck->name, $ck->status);
        }
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'update user tag success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'update user tag fail');
    }
    
}
