<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\mq\awsqs;

use yii\base\Component;
use yii\base\InvalidConfigException;
use xutl\mq\ClientInterface;
use Aws\Sqs\SqsClient;

/**
 * Class Client
 * @package xutl\mq\awsqs
 */
class Client extends Component implements ClientInterface
{
    /**
     * @var SqsClient
     */
    public $client;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->client = new SqsClient($this->client);
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
        ]);
    }
}