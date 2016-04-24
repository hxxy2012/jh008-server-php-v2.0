<?php

class ActInfoController extends AdminController
{
    
    public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + addAct, updateAct, updateStatus, delAct, delRecommend', // we only allow deletion via POST request
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
     * 添加活动
     */
    public function actionAddAct()
    {
        $ck = Rules::instance(
            array(
                'businessId' => Yii::app()->request->getPost('businessId'),
                'title' => Yii::app()->request->getPost('title'),
                'intro' => Yii::app()->request->getPost('intro'),
                'city_id' => Yii::app()->request->getPost('cityId', 1),
                'lon' => Yii::app()->request->getPost('lon'),
                'lat' => Yii::app()->request->getPost('lat'),
                'addr_city' => Yii::app()->request->getPost('addrCity'),
                'addr_area' => Yii::app()->request->getPost('addrArea'),
                'addr_road' => Yii::app()->request->getPost('addrRoad'),
                'addr_num' => Yii::app()->request->getPost('addrNum'),
                'addr_route' => Yii::app()->request->getPost('addrRoute'),
                'contact_way' => Yii::app()->request->getPost('contactWay'),
                'b_time' => Yii::app()->request->getPost('bTime'),
                'e_time' => Yii::app()->request->getPost('eTime'),
                't_status_rule' => Yii::app()->request->getPost('tStatusRule'),
                'weekRules' => Yii::app()->request->getPost('weekRules'),
                'detail' => Yii::app()->request->getPost('detail'),
                'detail_all' => Yii::app()->request->getPost('detailAll'),
                'can_enroll' => Yii::app()->request->getPost('canEnroll'),
                'lov_base_num' => Yii::app()->request->getPost('lovBaseNum'),
                'share_base_num' => Yii::app()->request->getPost('shareBaseNum'),
                'tagIds' => Yii::app()->request->getPost('tagIds'),
                'h_img_id' => Yii::app()->request->getPost('headImgId'),
                'imgIds' => Yii::app()->request->getPost('imgIds'),
            ),
            array(
                array('businessId, city_id', 'required'),
                array('businessId, city_id', 'numerical', 'integerOnly' => true),
                array('title', 'CZhEnV', 'min' => 1, 'max' => 32, 'isDiff' => TRUE),
                array('intro', 'CZhEnV', 'min' => 1, 'max' => 256),
                array('lon, lat', 'numerical', 'integerOnly' => FALSE),
                array('addr_city, addr_area, addr_road', 'CZhEnV', 'min' => 1, 'max' => 12),
                array('addr_num', 'CZhEnV', 'min' => 1, 'max' => 24),
                array('addr_route', 'CZhEnV', 'min' => 1, 'max' => 120),
                array('contact_way', 'length', 'max' => 48),
                array('b_time, e_time', 'type', 'datetimeFormat' => 'yyyy-mm-dd hh:mm:ss', 'type' => 'datetime'),
                array('t_status_rule, can_enroll', 'in', 'range' => array(0, 1)),
                array('weekRules', 'CArrNumV', 'maxLen' => 7, 'minNum' => 0, 'maxNum' => 6),
                array('detail', 'CZhEnV', 'min' => 1, 'max' => 256),
                //array('detail_all', 'CZhEnV', 'min' => 1, 'max' => 24),
                array('tagIds', 'CArrNumV', 'maxLen' => 2),
                array('lov_base_num, share_base_num, h_img_id', 'numerical', 'integerOnly' => true),
                array('imgIds', 'CArrNumV', 'maxLen' => 3),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        if (1 == $ck->t_status_rule) {
            $bW = date('w', strtotime($ck->b_time));
            $eW = date('w', strtotime($ck->e_time));
            if (empty($ck->weekRules) || !in_array($bW, $ck->weekRules) || !in_array($eW, $ck->weekRules)) {
                return Yii::app()->res->output(Error::PARAMS_ILLEGAL, 'time rule error');
            }
        }
        
        $model = new ActInfo();
        $ck->setModelAttris($model);
        $r = ActBusinessMap::model()->addAct($model,
                $ck->tagIds,
                $ck->imgIds,
                $ck->businessId
                );
        if ($r) {
            if (1 == $model->t_status_rule) {
                $rstArr = ArrTool::uniqueAscStr($ck->weekRules);
                ActTimeStatusRule::model()->addWeek($model->id, $rstArr);
            }  else {
                ActTimeStatusRule::model()->delWeek($model->id);
            }
            
            AdminOperateLog::model()->log(Yii::app()->user->id, '添加了活动' . $model->id);
            return Yii::app()->res->output(Error::NONE, '添加成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '添加失败');
    }
    
    
    /**
     * 修改活动资料
     */
    public function actionUpdateAct() 
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getPost('actId'),
                'title' => Yii::app()->request->getPost('title'),
                'intro' => Yii::app()->request->getPost('intro'),
                'city_id' => Yii::app()->request->getPost('cityId', 1),
                'lon' => Yii::app()->request->getPost('lon'),
                'lat' => Yii::app()->request->getPost('lat'),
                'addr_city' => Yii::app()->request->getPost('addrCity'),
                'addr_area' => Yii::app()->request->getPost('addrArea'),
                'addr_road' => Yii::app()->request->getPost('addrRoad'),
                'addr_num' => Yii::app()->request->getPost('addrNum'),
                'addr_route' => Yii::app()->request->getPost('addrRoute'),
                'contact_way' => Yii::app()->request->getPost('contactWay'),
                'b_time' => Yii::app()->request->getPost('bTime'),
                'e_time' => Yii::app()->request->getPost('eTime'),
                't_status_rule' => Yii::app()->request->getPost('tStatusRule'),
                'weekRules' => Yii::app()->request->getPost('weekRules'),
                'detail' => Yii::app()->request->getPost('detail'),
                'detail_all' => Yii::app()->request->getPost('detailAll'),
                'can_enroll' => Yii::app()->request->getPost('canEnroll'),
                'lov_base_num' => Yii::app()->request->getPost('lovBaseNum'),
                'share_base_num' => Yii::app()->request->getPost('shareBaseNum'),
                'tagIds' => Yii::app()->request->getPost('tagIds'),
                'h_img_id' => Yii::app()->request->getPost('headImgId'),
                'imgIds' => Yii::app()->request->getPost('imgIds'),
            ),
            array(
                array('actId, city_id', 'required'),
                array('actId, city_id', 'numerical', 'integerOnly' => true),
                array('title', 'CZhEnV', 'min' => 1, 'max' => 32, 'isDiff' => TRUE),
                array('intro', 'CZhEnV', 'min' => 1, 'max' => 256),
                array('lon, lat', 'numerical', 'integerOnly' => FALSE),
                array('addr_city, addr_area, addr_road', 'CZhEnV', 'min' => 1, 'max' => 12),
                array('addr_num', 'CZhEnV', 'min' => 1, 'max' => 24),
                array('addr_route', 'CZhEnV', 'min' => 1, 'max' => 120),
                array('contact_way', 'length', 'max' => 48),
                array('b_time, e_time', 'type', 'datetimeFormat' => 'yyyy-mm-dd hh:mm:ss', 'type' => 'datetime'),
                array('t_status_rule, can_enroll', 'in', 'range' => array(0, 1)),
                array('weekRules', 'CArrNumV', 'maxLen' => 7, 'minNum' => 0, 'maxNum' => 6),
                array('detail', 'CZhEnV', 'min' => 1, 'max' => 256),
                //array('detail_all', 'CZhEnV', 'min' => 1, 'max' => 24),
                array('tagIds', 'CArrNumV', 'maxLen' => 2),
                array('lov_base_num, share_base_num, h_img_id', 'numerical', 'integerOnly' => true),
                array('imgIds', 'CArrNumV', 'maxLen' => 3),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = ActInfo::model()->findByPk($ck->actId);
        if (empty($model)){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '活动不存在');
        }
        if (1 == $ck->t_status_rule) {
            $bW = date('w', strtotime($ck->b_time));
            $eW = date('w', strtotime($ck->e_time));
            if (empty($ck->weekRules) || !in_array($bW, $ck->weekRules) || !in_array($eW, $ck->weekRules)) {
                return Yii::app()->res->output(Error::PARAMS_ILLEGAL, 'time rule error');
            }
        }
        
        $ck->setModelAttris($model);
        $r = ActBusinessMap::model()->updateAct(
                $model,
                $ck->tagIds,
                $ck->imgIds);
        if ($r) {
            if (1 == $model->t_status_rule) {
                $rstArr = ArrTool::uniqueAscStr($ck->weekRules);
                ActTimeStatusRule::model()->addWeek($model->id, $rstArr);
            }  else {
                ActTimeStatusRule::model()->delWeek($model->id);
            }
            
            AdminOperateLog::model()->log(Yii::app()->user->id, '修改了活动' . $model->id);
            return Yii::app()->res->output(Error::NONE, '修改成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '修改失败');
    }
    
    
    /**
     * 修改活动状态
     */
    public function actionUpdateStatus() 
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getPost('actId'),
                'actStatus' => Yii::app()->request->getPost('actStatus'),
            ),
            array(
                array('actId, actStatus', 'required'),
                array('actId', 'numerical', 'integerOnly' => true),
                array('actStatus', 'in', 'range' => array(
                    ConstActStatus::NOT_COMMIT,
                    ConstActStatus::CHECK_WAITING,
                    ConstActStatus::CHECKING,
                    ConstActStatus::NOT_PASS,
                    ConstActStatus::NOT_PUBLISH,
                    ConstActStatus::PUBLISHING,
                    ConstActStatus::OFF_PUBLISH,
                    )),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = ActInfo::model()->findByPk($ck->actId);
        if (empty($model)) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '活动不存在');
        }
        
        $r = $model->updateStatus($ck->actStatus);
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '修改活动' . $model->id . '状态为' . $model->status);
            return Yii::app()->res->output(Error::NONE, '修改成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '修改失败');
    }
    
    
    /**
     * 删除活动
     */
    public function actionDelAct() 
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getPost('actId'),
            ),
            array(
                array('actId', 'required'),
                array('actId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $r = ActInfo::model()->delAct($ck->actId);
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '删除了活动' . $ck->actId);
            return Yii::app()->res->output(Error::NONE, '删除成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '删除失败');
    }
    
    
    /**
     * 获取活动详情
     */
    public function actionGetActInfo()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
            ),
            array(
                array('actId', 'required'),
                array('actId', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $act = ActInfoAdmin::model()->getActInfo($ck->actId);
        Yii::app()->res->output(Error::NONE, '获取成功', array('act' => $act));
    }
    
    
    /**
     * 获取活动列表
     * actStatus	int	活动状态： 1待审核，2审核中，3未通过，4未发布，5已发布
     */
    public function actionGetActs()
    {
        $ck = Rules::instance(
            array(
                'tStatus' => Yii::app()->request->getParam('tStatus'),
                'actStatus' => Yii::app()->request->getParam('actStatus'),
                'keyWords' => Yii::app()->request->getParam('keyWords'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('page, size', 'required'),
                array('tStatus', 'in', 'range' => array(1, 2, 3, 4)),
                array('actStatus', 'in', 'range' => array(1, 2, 3, 4, 5)),
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
                array('page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = ActInfoAdmin::model()->searchActs($ck->tStatus, $ck->actStatus, $ck->keyWords, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
    
    /**
     * 获取活动列表（回收站）
     */
    public function actionGetDelActs()
    {
        $ck = Rules::instance(
            array(
                'tStatus' => Yii::app()->request->getParam('tStatus'),
                'actStatus' => Yii::app()->request->getParam('actStatus'),
                'keyWords' => Yii::app()->request->getParam('keyWords'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('page, size', 'required'),
                array('tStatus', 'in', 'range' => array(1, 2, 3, 4)),
                array('actStatus', 'in', 'range' => array(1, 2, 3, 4, 5)),
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
                array('page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = ActInfoAdmin::model()->searchActs($ck->tStatus, $ck->actStatus, $ck->keyWords, $ck->page, $ck->size, TRUE);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }


    /**
     * 获取签到的活动列表
     */
    public function actionGetCheckinActs()
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
        
        $rst = ActInfoAdmin::model()->getActsWithCheckin($ck->keyWords, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }


    /**
     * 获取签到的用户列表
     */
    public function actionGetCheckinUsers()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'keyWords' => Yii::app()->request->getParam('keyWords'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('actId, page, size', 'required'),
                array('actId, page, size', 'numerical', 'integerOnly' => true),
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = ActCheckin::model()->getCheckinWithUsers($ck->actId, $ck->keyWords, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
    
    /**
     * 获取推荐资料
     */
    public function actionGetRecommend()
    {
        $ck = Rules::instance(
            array(
                'recommendId' => Yii::app()->request->getParam('recommendId'),
            ),
            array(
                array('recommendId', 'required'),
                array('recommendId', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $recommend = RecommendAct::model()->getRecommend($ck->recommendId);
        Yii::app()->res->output(Error::NONE, '获取成功', array('recommend' => $recommend));
    }


    /**
     * 删除推荐
     */
    public function actionDelRecommend()
    {
        $ck = Rules::instance(
            array(
                'recommendId' => Yii::app()->request->getParam('recommendId'),
            ),
            array(
                array('recommendId', 'required'),
                array('recommendId', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $r = RecommendAct::model()->del($ck->recommendId);
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '删除了活动推荐信息');
            return Yii::app()->res->output(Error::NONE, '删除成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '删除失败');
    }
    
    
    /**
     * 获取推荐信息列表
     */
    public function actionGetRecommends()
    {
        $ck = Rules::instance(
            array(
                'startTime' => Yii::app()->request->getParam('startTime'),
                'endTime' => Yii::app()->request->getParam('endTime'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('page, size', 'required'),
                array('page, size', 'numerical', 'integerOnly' => true),
                array('startTime, endTime', 'type', 'datetimeFormat' => 'yyyy-mm-dd hh:mm:ss', 'type' => 'datetime'),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = RecommendAct::model()->searchRecords($ck->startTime, $ck->endTime, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
    
    /**
     * 获取推荐信息列表（回收站）
     */
    public function actionGetDelRecommends()
    {
        $ck = Rules::instance(
            array(
                'startTime' => Yii::app()->request->getParam('startTime'),
                'endTime' => Yii::app()->request->getParam('endTime'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('page, size', 'required'),
                array('page, size', 'numerical', 'integerOnly' => true),
                array('startTime, endTime', 'type', 'datetimeFormat' => 'yyyy-mm-dd hh:mm:ss', 'type' => 'datetime'),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = RecommendAct::model()->searchRecords($ck->startTime, $ck->endTime, $ck->page, $ck->size, TRUE);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
}