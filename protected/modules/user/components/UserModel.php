<?php

class UserModel extends CActiveRecord
{
    public static $dbT;
    
    public function getDbConnection()
	{
        if(self::$dbT === null) {
            //self::$dbT = Yii::app()->controller->module->getComponent('dbUser');
            self::$dbT = Yii::app()->getComponent('dbUser');
			if(!(self::$dbT instanceof CDbConnection))
				throw new CDbException(Yii::t('yii','Active Record requires a "db" CDbConnection application component.'));
		}
        return self::$db = self::$dbT;
	}
    
}

?>
