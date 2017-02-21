<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\mq\console;

use Yii;
use yii\base\Event;
use yii\console\Controller;

/**
 * Job queue
 */
class QueueController extends Controller
{
    private $_listen = [];

    /**
     * @var string the ID of the action that is used when the action ID is not specified
     * in the request. Defaults to 'listen'.
     */
    public $defaultAction = 'listen';

    /**
     * 处理消息
     *
     * @param string $queue
     * @throws \Exception
     */
    public function actionListen($queue = null)
    {
        while (true) {
            $message = $this->getQueue()->receiveMessage($queue);
            if ($message && isset($message['messageBody']['event'])) {
                Yii::$app->trigger(__CLASS__ . $message['messageBody']['event'], new Event([
                    'sender' => $this->getQueue(),
                    'data' => $message,
                ]));
            }
            sleep(1);
        }
    }

    /**
     * 设置消息监听
     * @param array $listen
     */
    public function setListen(array $listen)
    {
        foreach ($listen as $e => $h) {
            Yii::$app->on(__CLASS__ . $e, $h);
        }
    }


    /**
     * 获取队列
     * @return \xutl\mq\MessageQueue
     * @throws \yii\base\InvalidConfigException
     */
    private function getQueue()
    {
        return Yii::$app->get('mq');
    }
}