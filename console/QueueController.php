<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\mq\console;

use Yii;
use yii\console\Controller;

/**
 * Job queue
 */
class QueueController extends Controller
{
    /**
     * @var integer
     * Delay after each step (in seconds)
     */
    public $_sleep = 1;

    /**
     * @var integer
     * Delay before running first job in listening mode (in seconds)
     */
    public $_timeout;

    public $_daemonize = false;

    /**
     * @var bool
     * Need restart job if failure or not
     */
    public $restartOnFailure = true;

    /**
     * @var string
     * Queue component ID
     */
    public $queue = 'mq';

    /**
     * @var string the ID of the action that is used when the action ID is not specified
     * in the request. Defaults to 'listen'.
     */
    public $defaultAction = 'listen';

    /**
     * Purges the queue.
     * @param string $queue
     */
    public function actionPurge($queue)
    {
        $this->getQueue()->purge($queue);
        $this->stdout("Purges the queue. ok!...\r\n");
    }

    /**
     * Process a job
     *
     * @param string $queue
     * @throws \Exception
     */
    public function actionWork($queue)
    {
        $this->process($queue);
    }

    /**
     * Continuously process jobs
     *
     * @param string $queue
     * @return bool
     * @throws \Exception
     */
    public function actionListen($queue = null)
    {
        while (true) {
            if ($this->_timeout !== null) {
                if ($this->_timeout < time()) {
                    if (!$this->_daemonize) {
                        $this->stdout('Script execution time is too long, the process exits until the next start.' . PHP_EOL);
                    } else {
                        Yii::trace('Script execution time is too long, the process exits until the next start.');
                    }
                    return true;
                }
            }
            if (!$this->process($queue)) {
                if (!$this->_daemonize) {
                    $this->stdout(sprintf('Wait %s second and continue.', $this->_sleep) . PHP_EOL);
                } else {
                    Yii::trace(sprintf('Wait %s second and continue.', $this->_sleep));
                }
            }
        }
    }

    /**
     * Process one unit of job in queue
     *
     * @param string $queue
     * @return bool
     */
    protected function process($queue)
    {
        $message = $this->getQueue()->receiveMessage($queue);
        if ($message) {
            try {
                /** @var \xutl\mq\Job $job */
                $job = call_user_func($message['messageBody']['serializer'][1], $message['messageBody']['object']);
                if (!$this->_daemonize) {
                    $this->stdout(sprintf('Begin executing a job `%s`...', get_class($job)) . PHP_EOL);
                } else {
                    Yii::trace(sprintf('Begin executing a job `%s`...', get_class($job)));
                }
                if ($job->run() || (bool)$this->restartOnFailure === false) {
                    $this->getQueue()->deleteMessage($message['receiptHandle']);
                } else {//执行失败60秒后重试
                    $this->getQueue()->changeMessageVisibility($message['receiptHandle'], 600);
                }
                return true;
            } catch (\Exception $e) {
                $this->getQueue()->deleteMessage($message);
                Yii::error($e->getMessage(), __METHOD__);
            }
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        if (getenv('QUEUE_TIMEOUT')) {
            $this->_timeout = (int)getenv('QUEUE_TIMEOUT') + time();
        }
        if (getenv('QUEUE_SLEEP')) {
            $this->_sleep = (int)getenv('QUEUE_SLEEP');
        }
        if (getenv('DAEMONIZE')) {
            $this->_daemonize = (int)getenv('DAEMONIZE');
        }
        return true;
    }

    /**
     * 获取队列
     * @return \xutl\mq\MessageQueue
     * @throws \yii\base\InvalidConfigException
     */
    private function getQueue()
    {
        return Yii::$app->get($this->queue);
    }
}