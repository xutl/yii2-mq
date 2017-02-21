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
use xutl\mq\ClientInterface;
use xutl\mq\QueueInterface;

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
    public function getQueueRef($queueName)
    {
        return $this->client->getQueueRef($queueName);
    }
}