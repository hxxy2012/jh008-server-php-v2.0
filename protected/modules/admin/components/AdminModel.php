<?php

class AdminModel extends CActiveRecord
{
    public static $dbT;
    
    public function getDbConnection()
	{
        if(self::$dbT === null) {
            self::$dbT=Yii::app()->getComponent('dbAdmin');
			if(!(self::$dbT instanceof CDbConnection))
				throw new CDbException(Yii::t('yii','Active Record requires a "dbAmin" CDbConnection application component.'));
		}
        return self::$db = self::$dbT;
	}
    
}

?>
