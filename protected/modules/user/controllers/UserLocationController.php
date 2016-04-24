<?php

class UserLocationController extends Controller {

    const LOCATION_KEY = 'location_';
    const LOCATION_VALID_TIME = 600; //单位秒，这个时间内的记录有效

    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
                // 'postOnly', // we only allow deletion via POST request
        );
    }

    public function accessRules() {
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('Get','ActRouteTest','GetGroup'),
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

    public function actionReport() {
        //latitude should between(-90,90)
        //longitude should between(-180,180)
        $key = self::LOCATION_KEY . (Yii::app()->user->id);

        $ck = Rules::instance(
                        array(
                    'lat' => Yii::app()->request->getParam('lat'),
                    'lng' => Yii::app()->request->getParam('lng'),
                    'loc_time' => Yii::app()->request->getParam('loc_time'),
                        ), array(
                    array('lat,lng,loc_time', 'required'),
                    array('lat', 'numerical', 'min' => -90, 'max' => 90, 'integerOnly' => false),
                    array('lng', 'numerical', 'min' => -180, 'max' => 180, 'integerOnly' => false),
                    array('loc_time', 'numerical', 'integerOnly' => false),
                        )
        );

        if (!$ck->validate()) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '经纬度不合法' . json_encode($ck->getErrors()));
        }

        $location = array(
            'lat' => $ck->lat,
            'lng' => $ck->lng,
            'loc_time' => $ck->loc_time,
            'svr_time' => time(),
           // 'key' => $key,
        );

        $last_location = Yii::app()->memcached->get($key);

        if (!$last_location || ($last_location && $last_location['loc_time'] < $location['loc_time'])) {
            if (Yii::app()->memcached->set($key, $location)) {
                return Yii::app()->res->output(Error::NONE, 'success', $location);
            } else {
                return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'failed');
            }
        }

        return Yii::app()->res->output(Error::NONE, 'no need to update');
    }

    public function actionGet() {
        $ck = Rules::instance(
                        array(
                    'uids' => Yii::app()->request->getParam('uids'),
                        ), array(
                    array('uids', 'required'),
                    array('uids', 'length', 'min' => 1),
                        )
        );
        if (!$ck->validate()) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '参数错误' . json_encode($ck->getErrors()));
        }

        $uids = explode(',', $ck->uids);
        if (count($uids) > 10) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '查询太多,最多10个');
        }

        $locations = array();
        foreach ($uids as $uid) {
            if (!empty($uid)) {
                $location = Yii::app()->memcached->get(self::LOCATION_KEY . $uid);
                if ($location && $location['svr_time'] > (time() - self::LOCATION_VALID_TIME)) {
                    $locations[$uid] = $location;
                } else {
                    $locations[$uid] = false;
                }
            }
        }

        return Yii::app()->res->output(Error::NONE, 'success', $locations);
    }

    public function actionGetGroup() {
        $ck = Rules::instance(
                        array(
                    'group_id' => Yii::app()->request->getParam('group_id'),
                    //'time' => Yii::app()->request->getParam('time'),
                        ), array(
                    array('group_id', 'required'),
                    array('group_id', 'numerical', 'integerOnly' => true),
                        )
        );
        if (!$ck->validate()) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '参数错误' . json_encode($ck->getErrors()));
        }
        $users = ActEnroll::model()->groupUsersId($ck->group_id);
       
        $locations = array();
        foreach ($users as $user){
            //var_dump(self::LOCATION_KEY . $user['u_id']);
            $location = Yii::app()->memcached->get(self::LOCATION_KEY . $user['u_id']);
           if ($location /*&& $location['svr_time'] > (time() - self::LOCATION_VALID_TIME)*/ && $user['u_id'] != Yii::app()->user->id) {
                $user['location_info'] = $location;
                $locations[] = $user;
            } else {
            }
        }
        return Yii::app()->res->output(Error::NONE, 'success', array('locations'=> $locations));
    }

    public function actionActRouteTest(){
         var_dump(ActRoute::model()->getActRoutesBasic(99));
    }
    
}
