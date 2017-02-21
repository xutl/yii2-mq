<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\mq;


use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * Class MessageQueue
 * @package xutl\queue
 */
class MessageQueue extends Component
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var array 驱动配置
     */
    public $driver;

    /**
     * 默认队列
     * @var string
     */
    public $defaultQueue = 'default';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->driver === null) {
            throw new InvalidConfigException('The "driver" property must be set.');
        }
        $this->client = Yii::createObject($this->driver);
    }

    /**
     * 获取指定队列实例
     * @param string $queueName
     * @return QueueInterface
     */
    public function getQueueRef($queueName = null)
    {
        return $this->client->getQueueRef($queueName ? $queueName : $this->defaultQueue);
    }

    /**
     * 快速发送消息
     * @param string $queueName
     * @param array|string $message
     * @param int $delay
     * @return mixed
     */
    public function sendMessage($message, $delay = 0, $queueName = null)
    {
        $queue = $this->getQueueRef($queueName);
        return $queue->sendMessage($message, $delay);
    }

    /**
     * 消费消息
     * @param string $queueName
     * @return array
     */
    public function receiveMessage($queueName = null)
    {
        $queue = $this->getQueueRef($queueName);
        return $queue->receiveMessage();
    }

    /**
     * 修改消息可见性
     * @param string $queueName
     * @param string $receiptHandle
     * @param int $visibilityTimeout
     * @return bool
     */
    public function changeMessageVisibility($receiptHandle, $visibilityTimeout, $queueName = null)
    {
        $queue = $this->getQueueRef($queueName);
        return $queue->changeMessageVisibility($receiptHandle, $visibilityTimeout);
    }

    /**
     * 删除消息
     * @param string $queueName
     * @param string $receiptHandle
     * @return bool
     */
    public function deleteMessage($receiptHandle, $queueName = null)
    {
        $queue = $this->getQueueRef($queueName);
        return $queue->deleteMessage($receiptHandle);
    }
}