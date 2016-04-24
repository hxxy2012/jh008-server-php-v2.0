<?php

abstract class ManagerModel extends CActiveRecord
{
    public static $dbT;
    
    public function getDbConnection()
	{
        if(self::$dbT === null) {
            self::$dbT=Yii::app()->getComponent('dbManager');
			if(!(self::$dbT instanceof CDbConnection))
				throw new CDbException(Yii::t('yii','Active Record requires a "dbManager" CDbConnection application component.'));
		}
        return self::$db = self::$dbT;
	}
    
    
    public function behaviors()
    {
        return array(
            'ManagerBehavior',
        );
    }
 
}

?>
