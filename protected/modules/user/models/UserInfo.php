<?php

/**
 * This is the model class for table "user_info".
 *
 * The followings are the available columns in table 'user_info':
 * @property string $id
 * @property string $user_name
 * @property string $salt
 * @property integer $is_regist
 * @property string $user_pass
 * @property string $pho_num
 * @property string $sina_openid
 * @property string $sina_token
 * @property string $sina_expires_in
 * @property string $qq_openid
 * @property string $qq_token
 * @property string $qq_expires_in
 * @property integer $status
 * @property string $create_time
 * @property string $last_login_time
 *
 * The followings are the available model relations:
 * @property ActCheckin[] $actCheckins
 * @property ActLovUserMap[] $actLovUserMaps
 * @property ImgUpUserMap[] $imgUpUserMaps
 * @property MsgRevUserMap[] $msgRevUserMaps
 * @property RecommendAct[] $recommendActs
 * @property TagUserMap[] $tagUserMaps
 * @property UserHeadImgMap[] $userHeadImgMaps
 */
class UserInfo extends UserModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('create_time, last_login_time', 'required'),
			array('is_regist, status', 'numerical', 'integerOnly'=>true),
			array('user_name, user_pass, pho_num, sina_openid, sina_token, qq_openid, qq_token', 'length', 'max'=>32),
			array('salt', 'length', 'max'=>6),
            array('sina_expires_in, qq_expires_in', 'length', 'max'=>16),
			array('birth', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_name, salt, is_regist, user_pass, pho_num, sina_openid, sina_token, sina_expires_in, qq_openid, qq_token, qq_expires_in, status, create_time, last_login_time', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			//'actCheckins' => array(self::HAS_MANY, 'ActCheckin', 'u_id'),
			//'actLovUserMaps' => array(self::HAS_MANY, 'ActLovUserMap', 'u_id'),
			//'imgUpUserMaps' => array(self::HAS_MANY, 'ImgUpUserMap', 'u_id'),
			//'msgRevUserMaps' => array(self::HAS_MANY, 'MsgRevUserMap', 'u_id'),
			//'recommendActs' => array(self::HAS_MANY, 'RecommendAct', 'u_id'),
			//'tagUserMaps' => array(self::HAS_MANY, 'TagUserMap', 'u_id'),
			//'userHeadImgMaps' => array(self::HAS_MANY, 'UserHeadImgMap', 'u_id'),
            
            'fkHeadImg' => array(self::HAS_ONE, 'UserHeadImgMap', 'u_id', 'on' => 'fkHeadImg.status=1'),
            'fkExtend' => array(self::HAS_ONE, 'UserInfoExtend', 'u_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '用户id[自增]',
			'user_name' => '用户名',
			'salt' => '哈希标识',
			'is_regist' => '是否已完成注册',
			'user_pass' => '用户密码',
			'pho_num' => '手机号用户名',
			'sina_openid' => '新浪微博openid',
			'sina_token' => '新浪微博token',
			'sina_expires_in' => '新浪过期时间',
			'qq_openid' => '腾讯QQopenid',
			'qq_token' => '腾讯QQtoken',
			'qq_expires_in' => '腾讯过期时间',
			'status' => '状态：-1删除，0正常',
			'create_time' => '用户注册时间',
			'last_login_time' => '用户最后登录时间',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    public function registUser()
    {
        $this->status = ConstStatus::NORMAL;
        $this->create_time = date('Y-m-d H:i:s');
        $this->last_login_time = date('Y-m-d H:i:s');
        return $this->save();
    }
    
    
    /**
     * 验证该手机号注册用户是否存在
     * @param type $phoneNum
     */
    public function validUser($phoneNum)
    {
        $model = $this->find('pho_num=:phoneNum and status>=:status', array(':phoneNum' => $phoneNum, ':status' => ConstStatus::NORMAL));
        if (empty($model)) {
            return FALSE;
        }
        return TRUE;
    }
    
    
    /**
     * 根据手机号获取用户
     * 
     * @param type $phoneNum 手机号
     */
    public function getByPhoneNum($phoneNum)
    {
        return $this->find('pho_num=:phoneNum and status>=:status', array(':phoneNum' => $phoneNum, ':status' => ConstStatus::NORMAL));
    }


    /**
     * 登录
     * @param type $identity
     * @return type
     */
    function login($identity, $duration = NULL) 
    {
        $identity->authenticate();
		if($identity->errorCode === UserIdentity::ERROR_NONE){
            Yii::app()->user->login($identity, $duration);
			return Error::NONE;
		}
        return $identity->errorCode;
    }
    
    
    /**
     * 覆盖登录
     * 
     * @param type $uid
     */
    public function coverLogin($uid) 
    {
        Yii::app()->user->coverLogin($uid);
    }
    
    
    /**
     * 手机号验证码获取登录权限
     * @param type $phoneNum
     * @param type $code
     * @return type
     */
    public function phCodeLogin($phoneNum, $code) 
    {
        $identity = new UserIdentity('phCodeAuthenticate', $phoneNum, $code);
        return $this->login($identity);
    }


    /**
     * 手机号+密码登录
     * @param type $phoneNum
     * @param type $userPass
     * @return type
     */
    public function phoneLogin($phoneNum, $userPass, $expiresIn=NULL) 
    {
        $identity = new UserIdentity('phoneAuthenticate', $phoneNum, $userPass);
        return $this->login($identity);
    }
    
    
    /**
     * 新浪第三方登录
     * @param type $openid
     * @param type $token
     * @return type
     */
    public function sinaLogin($openid, $token, $expiresIn) 
    {
        if (!Yii::app()->openid->sinaTokenValid($openid, $token)) {
            return Error::OPEN_TOKEN_INVALID;
        }
        $identity = new UserIdentity('sinaAuthenticate', $openid, $token, $expiresIn);
        return $this->login($identity);
    }
    
    
    /**
     * qq第三方登录
     * @param type $openid
     * @param type $token
     * @return type
     */
    public function qqLogin($openid, $token, $expiresIn) 
    {
        if (!Yii::app()->openid->qqTokenValid($openid, $token)) {
            return Error::OPEN_TOKEN_INVALID;
        }
        $identity = new UserIdentity('qqAuthenticate', $openid, $token, $expiresIn);
        return $this->login($identity);
    }
    
    
    /**
     * wechat第三方登录
     * @param type $openid
     * @param type $token
     * @param type $expiresIn
     * @return type
     */
    public function wechatLogin($openid, $token, $expiresIn) 
    {
        if (!Yii::app()->openid->wechatTokenValid($openid, $token)) {
            return Error::OPEN_TOKEN_INVALID;
        }
        $identity = new UserIdentity('wechatAuthenticate', $openid, $token, $expiresIn);
        return $this->login($identity);
    }
    
    
    /**
     * 新浪第三方注册
     * @param type $openid
     * @param type $token
     * @return type
     */
    public function sinaRegist($openid, $token, $expiresIn)
    {
        $model = new UserInfo();
        $model->salt = StrTool::getRandStr(6);
        $model->sina_openid = $openid;
        $model->sina_token = $token;
        $model->sina_expires_in = $expiresIn;
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date("Y-m-d H:i:s", time());
        $model->last_login_time = date("Y-m-d H:i:s", time());
        return $model->save();
    }
    
    
    /**
     * qq第三方注册
     * @param type $openid
     * @param type $token
     * @return type
     */
    public function qqRegist($openid, $token, $expiresIn)
    {
        $model = new UserInfo();
        $model->salt = StrTool::getRandStr(6);
        $model->qq_openid = $openid;
        $model->qq_token = $token;
        $model->qq_expires_in = $expiresIn;
        $model->status = 0;
        $model->create_time = date("Y-m-d H:i:s", time());
        $model->last_login_time = date("Y-m-d H:i:s", time());
        return $model->save();
    }
    
    
    /**
     * wechat第三方注册
     * @param type $openid
     * @param type $token
     * @param type $expiresIn
     * @return type
     */
    public function wechatRegist($openid, $token, $expiresIn)
    {
        $model = new UserInfo();
        $model->salt = StrTool::getRandStr(6);
        $model->wechat_openid = $openid;
        $model->wechat_token = $token;
        $model->wechat_expires_in = $expiresIn;
        $model->status = 0;
        $model->create_time = date("Y-m-d H:i:s", time());
        $model->last_login_time = date("Y-m-d H:i:s", time());
        return $model->save();
    }
    
    
    /**
     * 根据新浪的openid获取用户
     * @param type $openid
     */
    public function getBySina($openid)
    {
        return $this->find('sina_openid=:openid and status>=:status', array(':openid' => $openid, ':status' => ConstStatus::NORMAL));
    }
    
    
    /**
     * 根据qq的openid获取用户
     * @param type $openid
     */
    public function getByQq($openid)
    {
        return $this->find('qq_openid=:openid and status>=:status', array(':openid' => $openid, ':status' => ConstStatus::NORMAL));
    }
    
    
    /**
     * 根据wechat的openid获取用户
     * @param type $openid
     * @return type
     */
    public function getByWechat($openid)
    {
        return $this->find('wechat_openid=:openid and status>=:status', array(':openid' => $openid, ':status' => ConstStatus::NORMAL));
    }


    /**
     * sina第三方绑定
     * @param type $openid
     * @param type $token
     * @return type
     */
    public function sinaSet($uid, $openid, $token, $expiresIn)
    {
        if (!Yii::app()->openid->sinaTokenValid($openid, $token)) {
            throw new ResError(Error::OPEN_TOKEN_INVALID, "sina 验证失败");
        }
        
        $user = $this->getBySina($openid);
        if (!empty($user) && $user->id != $uid) {
            throw new ResError(Error::OPEN_TOKEN_INVALID, "sina 第三方信息已存在");
        }
        $this->sina_openid = $openid;
        $this->sina_token = $token;
        $this->sina_expires_in = $expiresIn;
        return $this->update();
    }
    
    
    /**
     * qq第三方绑定
     * @param type $openid
     * @param type $token
     * @return type
     */
    public function qqSet($uid, $openid, $token, $expiresIn)
    {
        if (!Yii::app()->openid->qqTokenValid($openid, $token)) {
            throw new ResError(Error::OPEN_TOKEN_INVALID, "qq 验证失败");
        }
        $user = $this->getByQq($openid);
        if (!empty($user) && $user->id != $uid) {
            throw new ResError(Error::OPEN_TOKEN_INVALID, "qq 第三方信息已存在");
        }
        $this->qq_openid = $openid;
        $this->qq_token = $token;
        $this->qq_expires_in = $expiresIn;
        return $this->update();
    }
    
    
    /**
     * wechat第三方绑定
     * @param type $openid
     * @param type $token
     * @return type
     */
    public function wechatSet($uid, $openid, $token, $expiresIn)
    {
        if (!Yii::app()->openid->wechatTokenValid($openid, $token)) {
            throw new ResError(Error::OPEN_TOKEN_INVALID, "wechat 验证失败");
        }
        $user = $this->getByWechat($openid);
        if (!empty($user) && $user->id != $uid) {
            throw new ResError(Error::OPEN_TOKEN_INVALID, "wechat 第三方信息已存在");
        }
        $this->wechat_openid = $openid;
        $this->wechat_token = $token;
        $this->wechat_expires_in = $expiresIn;
        return $this->update();
    }
    
    
    /**
     * 手机验证修改密码
     * @param type $uid
     * @param type $newPass
     * @return boolean
     */
    public function phRePass($uid, $newPass) 
    {
        if (empty($uid)) {
            return Error::USERNAME_INVALID;
        }
        $model = $this->findByPk($uid);
        if (empty($model)) {
            return Error::USERNAME_INVALID;
        }
        //必须是已完善资料的用户才能直接通过验证手机号修改密码
        if (1 != $model->is_regist) {
            return Error::USERNAME_INVALID;
        }
        
        $model->user_pass = strtoupper(md5(strtoupper($model->salt) . strtoupper($newPass)));

        if (!$model->update()) {
            return Error::OPERATION_EXCEPTION;
        }
        return Error::NONE;
    }
    
    
    /**
     * 修改密码
     * 
     * @param type $uid 用户id
     * @param type $oldPass 旧密码
     * @param type $newPass 新密码
     */
    public function rePass($uid, $oldPass, $newPass) 
    {
        if (empty($uid)) {
            return Error::USERNAME_INVALID;
        }
        $model = $this->findByPk($uid);
        if (empty($model)) {
            return Error::USERNAME_INVALID;
        }
        //旧密码验证修改新密码
        if (strtoupper(md5(strtoupper($model->salt) . strtoupper($oldPass))) != $model->user_pass) {
            return Error::USERNAME_OR_USERPASS_INVALID;
        }
        
        $model->user_pass = strtoupper(md5(strtoupper($model->salt) . strtoupper($newPass)));

        if (!$model->update()) {
            return Error::OPERATION_EXCEPTION;
        }
        return Error::NONE;
    }
    
    
    /**
     * 登录后获取的基础用户信息
     */
    public function getLogUInfo() {
        return array(
            'uid' => $this->id,
            'is_regist' => $this->is_regist,
        );
    }
    
    
    /**
     * 获取自己的用户资料
     * v0.0.0
     * @return type
     */
    public function getMyUInfo() {
        $baseArr = array(
            'uid' => $this->id,
            'is_regist' => $this->is_regist,
            'pho_num' => $this->pho_num,
            'sina_openid' => $this->sina_openid,
            'sina_token' => $this->sina_token,
            'sina_expires_in' => $this->sina_expires_in,
            'qq_openid' => $this->qq_openid,
            'qq_token' => $this->qq_token,
            'qq_expires_in' => $this->qq_expires_in,
            'wechat_openid' => $this->wechat_openid,
            'wechat_token' => $this->wechat_token,
            'wechat_expires_in' => $this->wechat_expires_in,
            'last_login_time' => $this->last_login_time,
            'head_img_url' => UserHeadImgMap::model()->getCurImgUrl($this->id),
        );
        $extendArr = UserInfoExtend::model()->fullProfile($this->id);
        return array_merge($baseArr, $extendArr);
    }
    
    
    /**
     * 删除用户
     * @param type $uid
     */
    public function del($uid)
    {
        $model = $this->findByPk($uid);
        if (empty($model)) {
            return FALSE;
        }
        $model->status = ConstStatus::DELETE;
        return $model->update();
    }
    
    
    /**
     * 用户基本信息
     * 
     * @param type $model 用户数据
     * @param type $id 用户id
     * @param type $cityId 城市id
     * @param type $uid 当前用户id，值为null则是id为当前用户
     * @param type $is_focus 是否被当前用户关注，值为null则需查询得到
     * @param type $needFocusNum 是否需要查询关注数和粉丝数
     * @param type $needIsShield 是否需要查询是否屏蔽此联系人
     */
    public function profile($model = NULL, $id = NULL, $cityId = NULL, $uid = NULL, $is_focus = NULL, $needFocusNum = FALSE, $needIsShield = FALSE)
    {
        if (empty($model)) {
            $model = $this->findByPk($id);
        }
        if (empty($model)) {
            return NULL;
        }
        $baseArr = array(
            'id' => $model->id,
            'head_img_url' => UserHeadImgMap::model()->getCurImgUrl($model->id),
        );
        
        if (!empty($cityId)) {
            //查询在某个城市该用户是否为达人
            $ucity = UserCityMap::model()->get($model->id, $cityId);
            if (empty($ucity)) {
                $baseArr['is_vip'] = 0;
            }  else {
                $baseArr['is_vip'] = 1;
            }
        }
        
        //用户扩展信息
        $extendArr = UserInfoExtend::model()->profile($model->id);
        
        if (empty($extendArr)) {
            $extendArr = array();
        }
        
        //关注数和粉丝数
        if ($needFocusNum) {
            $extendArr['focus_num'] = UserFans::model()->countUsers($model->id, 1);
            $extendArr['fans_num'] = UserFans::model()->countUsers($model->id, 2);
        }
        
        //当前用户未登录或者获取自己的资料不提供相互关系
        if (empty($uid) || $id == $uid) {
            return array_merge($baseArr, $extendArr);
        }
        
        if ($needIsShield) {
            $extendArr['is_shield'] = UserContact::model()->isShield($uid, $model->id) ? 1 : 0;
        }
        
        //强制设置的is_focus
        if (!empty($is_focus)) {
            $extendArr['is_focus'] = $is_focus;
            return array_merge($baseArr, $extendArr);
        }
        
        //动态查询的is_focus
        $uuModel = UserFans::model()->get($model->id, $uid);
        if (empty($uuModel)) {
            $extendArr['is_focus'] = 0;
        }  else {
            $extendArr['is_focus'] = 1;
        }
        
        return array_merge($baseArr, $extendArr);
    }
    
    
    /**
     * 完整信息
     * 
     * @param type $model 用户数据
     * @param type $id 用户id
     * @param type $cityId 城市id
     * @param type $uid 当前用户id
     */
    public function fullProfile($model = NULL, $id = null, $cityId, $uid = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($id);
        }
        if (empty($model)) {
            return NULL;
        }
        $baseArr = array(
            'id' => $model->id,
            'is_regist' => $model->is_regist,
            'pho_num' => $model->pho_num,
            'sina_openid' => $model->sina_openid,
            'sina_token' => $model->sina_token,
            'sina_expires_in' => $model->sina_expires_in,
            'qq_openid' => $model->qq_openid,
            'qq_token' => $model->qq_token,
            'qq_expires_in' => $model->qq_expires_in,
            'wechat_openid' => $model->wechat_openid,
            'wechat_token' => $model->wechat_token,
            'wechat_expires_in' => $model->wechat_expires_in,
            'last_login_time' => $model->last_login_time,
            'head_img_url' => UserHeadImgMap::model()->getCurImgUrl($model->id),
        );
        
        $ucity = UserCityMap::model()->get($id, $cityId);
        if (empty($ucity)) {
            $baseArr['is_vip'] = 0;
        }  else {
            $baseArr['is_vip'] = 1;
        }
        
        $extendArr = UserInfoExtend::model()->fullProfile($model->id);
        
        if (empty($extendArr)) {
            $extendArr = array();
        }
        
        //关注数和粉丝数
        $extendArr['focus_num'] = UserFans::model()->countUsers($model->id, 1);
        $extendArr['fans_num'] = UserFans::model()->countUsers($model->id, 2);
        
        //当前用户未登录或者获取自己的资料不提供相互关系
        if (empty($uid) || $id == $uid) {
            return array_merge($baseArr, $extendArr);
        }
        
        $extendArr['is_shield'] = UserContact::model()->isShield($uid, $model->id) ? 1 : 0;
        
        //动态查询的is_focus
        $uuModel = UserFans::model()->get($model->id, $uid);
        if (empty($uuModel)) {
            $extendArr['is_focus'] = 0;
        }  else {
            $extendArr['is_focus'] = 1;
        }
        
        return array_merge($baseArr, $extendArr);
    }
    
}
