<?php

/**
 * Description of ActBehavior
 *
 * @author Zero2all
 */
class UserBehavior extends CActiveRecordBehavior 
{
    
    //public function behaviors()
    //{
    //    return array(
    //        // YII AR的事件行为
    //        'YiicmsActiveRecordBehavior',
    //        // 时间
    //        'CTimestampBehavior' => array(
    //            'class' => 'zii.behaviors.CTimestampBehavior',
    //            'createAttribute' => 'create_time',
    //            'updateAttribute' => 'update_time',
    //            )
    //        );
    //}
    
    //保存旧数据的信息
	private $_oldattributes = array();
    
	public function afterFind($event)
	{
		//记录旧数据
		$attributes = $this->Owner->getAttributes();
		$this->setOldAttributes($attributes);
	}
    
    
	public function afterSave($event)
	{
        //后台定时任务执行时不保存操作日志
        if (defined('YII_CRON') && YII_CRON) {
            return;
        }
        
        $moduleName = Yii::app()->getController()->module->name;
        
        //某些模块的调用才记录日志
        if (($moduleName != 'manager' && $moduleName != 'org' && $moduleName != 'org_v2') || Yii::app()->user->isGuest) {
            return;
        }
        
		//new attributes
		$newattributes = $this->owner->getAttributes();
		$oldattributes = $this->getOldAttributes();
        
		//后台log
		$log = NULL;
        switch ($moduleName) {
            case 'org':
            case 'org_v2':
                $log = new OrgLog();
                if (!Yii::app()->user->isGuest) {
                    $log->u_id = Yii::app()->user->id;
                }
                break;
            case 'manager':
                if (Yii::app()->user->getType() >= ConstCityManagerStatus::CITY_MANAGER) {
                    //城市管理操作日志
                    $log = new CityManagerLog();
                }  else {
                    //内部管理操作日志
                    $log = new ManagerLog();
                }
                if (!Yii::app()->user->isGuest) {
                    $log->m_id = Yii::app()->user->id;
                }
                break;
            default:
                return;
        }
        
        if ($this->owner->isNewRecord) {
            $log->model_behavior = 'insert';
        }  else {
            $log->model_behavior = 'update';
        }

        $model_attributes_old = CJSON::encode($oldattributes);
		$model_attributes_new = CJSON::encode($newattributes);

        //获得该操作资源的类名
        $log->model_class = $this->owner->tableName();
        //获得该操作资源的主键
		$log->model_pk = $this->owner->getPrimaryKey();
        $log->model_attributes_old = $model_attributes_old;
        $log->model_attributes_new = $model_attributes_new;
        
        $log->status = ConstStatus::NORMAL;
        $log->create_time = date('Y-m-d H:i:s');
        $log->save();
	}

    
    //旧数据
	public function getOldAttributes()
	{
		return $this->_oldattributes;
	}

    
    public function setOldAttributes($value)
	{
		$this->_oldattributes = $value;
	}

}

?>
