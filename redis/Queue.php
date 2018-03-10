<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\mq\redis;

use xutl\mq\Message;
use yii\base\Object;
use yii\helpers\Json;
use Predis\Client;
use Predis\Transaction\MultiExec;

/**
 * Class Queue
 * @package xutl\mq\redis
 */
class Queue extends \xutl\mq\Queue
{
    /**
     * @var Client;
     */
    public $client;

    /**
     * @var string
     */
    public $queueName;

    /**
     * @var integer
     */
    public $expire = 60;

    /**
     * @param array $message
     * @param int $delay
     * @return false|string
     */
    public function sendMessage($message, $delay = 0)
    {
        $payload = Json::encode(['id' => $id = md5(uniqid('', true)), 'body' => $message]);
        if ($delay > 0) {
            //放入等待
            $this->client->zadd($this->queueName . ':delayed', [$payload => time() + $delay]);
        } else {
            $this->client->rpush($this->queueName, [$payload]);
        }
        return $id;
    }

    /**
     * 获取消息
     * @return Message|bool
     */
    public function receiveMessage()
    {
        //遍历保留和等待
        foreach ([':delayed', ':reserved'] as $type) {
            $options = ['cas' => true, 'watch' => $this->queueName . $type];
            $this->client->transaction($options, function (MultiExec $transaction) use ($type) {
                $data = $this->client->zrangebyscore($this->queueName . $type, '-inf', $time = time());
                if (!empty($data)) {
                    $transaction->zremrangebyscore($this->queueName . $type, '-inf', $time);
                    //压入队列
                    foreach ($data as $payload) {
                        $transaction->rpush($this->queueName, [$payload]);
                    }
                }
            });
        }

        $data = $this->client->lpop($this->queueName);

        if ($data === null) {
            return false;
        }

        $this->client->zadd($this->queueName . ':reserved', [$data => time() + $this->expire]);

        $receiptHandle = $data;
        $data = Json::decode($data);

        return new Message([
            'messageId' => $data['id'],
            'messageBody' => $data['body'],
            'receiptHandle' => $receiptHandle,
            'queue' => $this,
        ]);
    }

    /**
     * 修改消息可见时间
     * @param string $receiptHandle
     * @param int $visibilityTimeout
     * @return bool
     */
    public function changeMessageVisibility($receiptHandle, $visibilityTimeout)
    {
        $this->deleteMessage($receiptHandle);
        if ($visibilityTimeout > 0) {
            $this->client->zadd($this->queueName . ':delayed', [$receiptHandle => time() + $visibilityTimeout]);
        } else {
            $this->client->rpush($this->queueName, [$receiptHandle]);
        }
    }

    /**
     * 删除消息
     * @param string $receiptHandle
     * @return bool
     */
    public function deleteMessage($receiptHandle)
    {
        $this->client->zrem($this->queueName . ':reserved', $receiptHandle);
    }
}