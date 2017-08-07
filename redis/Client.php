<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\mq\redis;

use yii\base\Component;
use yii\base\InvalidConfigException;
use xutl\mq\ClientInterface;
use Predis\Client as Redis;

/**
 * Class Client
 * @package xutl\mq\redis
 */
class Client extends Component implements ClientInterface
{
    /**
     * @var array
     */
    public $redis;

    /**
     * @var integer
     */
    public $expire = 60;

    /**
     * @var Client
     */
    private $client;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->redis === null) {
            throw new InvalidConfigException('The "redis" property must be set.');
        }
        $this->client = new Redis($this->redis);
    }

    /**
     * 获取队列
     * @param string $queueName
     * @return Queue
     */
    public function getQueueRef($queueName)
    {
        return new Queue([
            'client' => $this->client,
            'queueName' => $queueName,
            'expire' => $this->expire
        ]);
    }
}