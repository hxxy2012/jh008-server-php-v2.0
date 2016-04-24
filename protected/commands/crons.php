<?php

defined('YII_DEBUG') or define('YII_DEBUG', FALSE);

//定义常量YII_CRON标识后台定时任务
defined('YII_CRON') or define('YII_CRON', TRUE);

// including Yii
require_once(dirname(dirname(dirname(__FILE__))) . '/framework/yii.php');
// we'll use a separate config file
//$configFile=dirname(dirname(__FILE__)).'/config/console.php';
$configFile = dirname(dirname(__FILE__)) . '/config/main.php';
// creating and running console application
Yii::createConsoleApplication($configFile)->run();

//crontab -e
//*/1 * * * *  php /home/wwwroot/gathersrv/protected/commands/crons.php gather >>/具体地址/protected/commands/gather.log
//##上面命令说明，每分钟执行Test任务一次，把日志保存在gather.log下
//*/1 * * * *  php /home/wwwroot/staging_jhla/protected/commands/crons.php gather
//(*代表必须添加到cron，-代表不添加到cron，+代表也添加到cron)
//0 */1 * * *  php /home/wwwroot/staging_jhla/protected/commands/crons.php ActTimeStatus                                                      每小时0分刷新+定时任务 +
//2 */1 * * *  php /home/wwwroot/staging_jhla/protected/commands/crons.php TagActCount                                                        每小时2分刷新 +
//4 */1 * * *  php /home/wwwroot/staging_jhla/protected/commands/crons.php IndexPageAct                                                       每小时4分刷新 +
//0 3 * * *  php /home/wwwroot/staging_jhla/protected/commands/crons.php UserRegistCount                                                      每天凌晨3点0分刷新 *
//*/30 * * * *  php /home/wwwroot/staging_jhla/protected/commands/crons.php Push >>/home/wwwroot/jhla-logs/cron-push.log                      每半小时刷新 -
//*/30 * * * *  php /home/wwwroot/staging_jhla/protected/commands/crons.php Msg                                                               定时任务 -
//*/1 * * * *  php /home/wwwroot/staging_jhla/protected/commands/crons.php TimeTask >>/home/wwwroot/jhla-logs/cron-timetask.log               每分钟刷新 *
//2 3 * * *  php /home/wwwroot/staging_jhla/protected/commands/crons.php ActBaseGrowNums >>/home/wwwroot/jhla-logs/cron-actbasegrownums.log   每天凌晨3点2分刷新 *
//*/2 * * * *  php /home/wwwroot/staging_jhla/protected/commands/crons.php FriendDynamicTask                                                  每两分钟刷新 *
//* */1 * * *  php /home/wwwroot/staging_jhla/protected/commands/crons.php VipSearchTask                                                     每小时刷新 *
//*/1 * * * *  php /home/wwwroot/staging_jhla/protected/commands/crons.php SystemMsgUserTask                                                  每分钟刷新 *
//*/1 * * * *  php /home/wwwroot/staging_jhla/protected/commands/crons.php PushMsgTask                                                        每分钟刷新 *
//* */1 * * *  php /home/wwwroot/staging_jhla/protected/commands/crons.php CityVipRandomDynamicsTask                                          每小时刷新 *
//* */1 * * *  php /home/wwwroot/staging_jhla/protected/commands/crons.php ActInfoBaiduSynchroTask                                            每小时刷新 *
//0 */1 * * *  php /home/wwwroot/jhla/protected/commands/crons.php ActTimeStatus
//2 */1 * * *  php /home/wwwroot/jhla/protected/commands/crons.php TagActCount
//4 */1 * * *  php /home/wwwroot/jhla/protected/commands/crons.php IndexPageAct
//0 3 * * *  php /home/wwwroot/jhla/protected/commands/crons.php UserRegistCount
//*/30 * * * *  php /home/wwwroot/jhla/protected/commands/crons.php Push >>/home/wwwroot/jhla-logs/cron-push.log
//*/30 * * * *  php /home/wwwroot/jhla/protected/commands/crons.php Msg
//*/1 * * * *  php /home/wwwroot/jhla/protected/commands/crons.php TimeTask >>/home/wwwroot/jhla-logs/cron-timetask.log
//2 3 * * *  php /home/wwwroot/jhla/protected/commands/crons.php ActBaseGrowNums >>/home/wwwroot/jhla-logs/cron-actbasegrownums.log
//*/2 * * * *  php /home/wwwroot/jhla/protected/commands/crons.php FriendDynamicTask                                                  每两分钟刷新 *
//* */1 * * *  php /home/wwwroot/jhla/protected/commands/crons.php VipSearchTask                                                      每小时刷新 *
//*/1 * * * *  php /home/wwwroot/jhla/protected/commands/crons.php SystemMsgUserTask                                                  每分钟刷新 *
//*/1 * * * *  php /home/wwwroot/jhla/protected/commands/crons.php PushMsgTask                                                        每分钟刷新 *
//* */1 * * *  php /home/wwwroot/jhla/protected/commands/crons.php CityVipRandomDynamicsTask                                          每小时刷新 *
//4 3 * * *  php /home/wwwroot/jhla/protected/commands/crons.php ActInfoBaiduSynchroTask                                            每天凌晨3点4分刷新 *
?>