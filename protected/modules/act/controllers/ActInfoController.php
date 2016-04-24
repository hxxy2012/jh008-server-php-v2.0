<?php

class ActInfoController extends ActController {

    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + addLovAct, delLovAct, addShareAct', // we only allow deletion via POST request
        );
    }

    public function accessRules() {
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('SltActs', 'GetInfo', 'GetImgs', 'ViewDetailAll', 'ActRoute','ActRoutes'),
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
     * 获取筛选活动（两个标签+活动时间的状态）
     */
    public function actionSltActs() {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId', 1),
                'tagIds' => Yii::app()->request->getParam('tagIds', array()),
                'actTimeStatus' => Yii::app()->request->getParam('actTimeStatus', 0),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ), array(
                array('cityId, page, size', 'required'),
                array('tagIds', 'CArrNumV', 'maxLen' => 2),
                array('actTimeStatus, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        //$list = IndexPageActList::model()->getSltActs($ck->actTimeStatus, $ck->tagIds, $ck->page, $ck->size, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        $list = KeyValInfo::model()->getSltActs($ck->cityId, $ck->actTimeStatus, $ck->tagIds, $ck->page, $ck->size, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);

        Yii::app()->res->output(Error::NONE, '获取成功', $list);
    }

    
    /**
     * 用户感兴趣的活动列表
     */
    public function actionGetUActs() {
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
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $list = ActLovUserMap::model()->getUActs(Yii::app()->user->id, $ck->page, $ck->size);

        Yii::app()->res->output(Error::NONE, '获取成功', $list);
    }

    
    /**
     * 获取活动的详情
     */
    public function actionGetInfo() {
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
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $actInfo = ActInfo::model()->getAct($ck->actId);
        Yii::app()->res->output(Error::NONE, '获取成功', array('act' => $actInfo));
    }

    
    /**
     * 查看活动的图文详情
     */
    public function actionViewDetailAll() {
        $this->layout = '//layouts/blank';
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
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, "Invalid");
            //throw new CHttpException(404, '未找到活动');
        }

        $model = ActInfo::model()->findByPk($ck->actId);
        if (empty($model)) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, 'Not found');
            //throw new CHttpException(404, '未找到活动内容');
        }

        $act = array();
        $act['id'] = $model->id;
        $act['title'] = $model->title;
        $act['intro'] = $model->intro;
        $act['detail'] = $model->detail;
        $act['detail_all'] = $model->detail_all;
        $this->render('viewdetailall', array('act' => $act));
    }

    /**
     * 获取活动的图片
     */
    public function actionGetImgs() {
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
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $imgs = ActImgMap::model()->getImgs($ck->actId);
        Yii::app()->res->output(Error::NONE, '获取成功', array('imgs' => $imgs));
    }

    /**
     * 添加感兴趣的活动
     */
    public function actionAddLovAct() {
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
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $r = ActLovUserMap::model()->addLove($ck->actId, Yii::app()->user->id);

        if ($r) {
            return Yii::app()->res->output(Error::NONE, '添加感兴趣成功');
        }
        Yii::app()->res->output(Error::NONE, '添加感兴趣失败');
    }

    /**
     * 取消感兴趣的活动
     */
    public function actionDelLovAct() {
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
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $r = ActLovUserMap::model()->delLove($ck->actId, Yii::app()->user->id);

        if ($r) {
            return Yii::app()->res->output(Error::NONE, '取消感兴趣成功');
        }
        Yii::app()->res->output(Error::NONE, '取消感兴趣失败');
    }

    /**
     * 添加活动分享数
     */
    public function actionAddShareAct() {
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
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $r = ActShare::model()->addShare($ck->actId, Yii::app()->user->id);

        if ($r) {
            return Yii::app()->res->output(Error::NONE, '添加分享数成功');
        }
        Yii::app()->res->output(Error::NONE, '添加分享数失败');
    }

    /**
     * 活动签到
     */
    public function actionCheckin() {
        $ck = Rules::instance(
            array(
                'act_id' => Yii::app()->request->getPost('actId'),
                'lon' => Yii::app()->request->getPost('lon'),
                'lat' => Yii::app()->request->getPost('lat'),
                'address' => Yii::app()->request->getPost('address'),
            ), 
            array(
                array('act_id', 'required'),
                array('act_id', 'numerical', 'integerOnly' => true),
                array('lon, lat', 'numerical'),
                array('address', 'CZhEnV', 'max' => 32),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        if (ActCheckin::model()->check($ck->act_id, Yii::app()->user->id)) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '已签到');
        }

        $model = new ActCheckin();
        $ck->setModelAttris($model);
        $r = ActCheckin::model()->checkin($model, Yii::app()->user->id);

        if ($r) {
            return Yii::app()->res->output(Error::NONE, '签到成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '签到失败');
    }

    /**
     * 用户向后台推荐新的活动信息
     */
    public function actionURecommend() {
        $ck = Rules::instance(
            array(
                'img_id' => Yii::app()->request->getPost('imgId'),
                'act_name' => Yii::app()->request->getPost('actName'),
                'act_time' => Yii::app()->request->getPost('actTime'),
                'act_address' => Yii::app()->request->getPost('actAddress'),
                'act_contact' => Yii::app()->request->getPost('actContact'),
                'remark' => Yii::app()->request->getPost('remark'),
                'lon' => Yii::app()->request->getPost('lon'),
                'lat' => Yii::app()->request->getPost('lat'),
                'address' => Yii::app()->request->getPost('address'),
            ), array(
                array('img_id', 'required'),
                array('img_id', 'numerical', 'integerOnly' => true),
                array('act_name, act_time, act_address, act_contact, address', 'CZhEnV', 'max' => 32),
                array('remark', 'CZhEnV', 'max' => 120),
                array('lon, lat', 'numerical'),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $model = new RecommendAct();
        $ck->setModelAttris($model);
        $model->u_id = Yii::app()->user->id;
        $model->status = 0;
        $model->create_time = date("Y-m-d H:i:s", time());
        $r = $model->save();

        if ($r) {
            return Yii::app()->res->output(Error::NONE, '推荐成功');
        }
        Yii::app()->res->output(Error::NONE, '推荐失败');
    }

    
    /**
     * 活动相关图片文件上传
     */
    public function actionActImgUp() {
        $ck = Rules::instance(
            array(
                'actImg' => CUploadedFile::getInstanceByName('actImg'),
            ), 
            array(
                array('actImg', 'required'),
                array('actImg', 'file', 'allowEmpty' => true,
                    'types' => 'jpg',
                    'maxSize' => 1024 * 1024 * 1,
                    'tooLarge' => '上传文件超过 1024 * 1024 kb，无法上传。',
                ),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        //保存图像文件到指定目录
        $iu = Yii::app()->imgUpload->uActImg($ck->actImg);
        if (!$iu) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '图片上传失败');
        }

        //图像信息表插入
        $imgInfo = new ImgInfo();
        if (!$imgInfo->ins($ck->actImg->name, $iu)) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '图片保存失败');
        }

        if (!Yii::app()->user->isGuest) {
            $imgUpUser = new ImgUpUserMap();
            //图像上传者关联表插入
            if (!$imgUpUser->ins($imgInfo->id, Yii::app()->user->id)) {
                return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '图片保存失败');
            }
        }

        Yii::app()->res->output(Error::NONE, '图片上传成功', array('img_id' => $imgInfo->id));
    }

    /**
     * 历史签到
     */
    public function actionCheckinHistory() {
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
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $list = ActCheckin::model()->getHistory(Yii::app()->user->id, $ck->page, $ck->size);

        Yii::app()->res->output(Error::NONE, '获取成功', $list);
    }

    public function actionActRoute() {
        $ck = Rules::instance(
            array(
                'act_route_id' => Yii::app()->request->getParam('act_route_id'),
            ), 
            array(
                array('act_route_id', 'required'),
                array('act_route_id', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $route = ActRoute::model()->queryActRoute($ck->act_route_id);
        $route_info = $route->attributes;
        $route_info['act_route_points'] = unserialize($route_info['act_route_points']);
        
        //var_dump($route);
        Yii::app()->res->output(Error::NONE, '成功', $route_info);
    }
    
    public function actionActRoutes() {
        $ck = Rules::instance(
            array(
                'act_id' => Yii::app()->request->getParam('act_id'),
            ), 
            array(
                array('act_id', 'required'),
                array('act_id', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $routes =  array_map(function($record) {
                   $info =  $record->attributes;
                  //var_dump($info);
                  $info['act_route_points'] = unserialize( $info['act_route_points']);
                   return $info;
                }, ActRoute::model()->getActRoutes($ck->act_id));
                
        //var_dump($route);
        Yii::app()->res->output(Error::NONE, '成功', $routes);
    }
    
    
}
