<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\mq\awsqs;

use Yii;
use yii\base\Object;
use yii\helpers\Json;
use xutl\mq\QueueInterface;
use Aws\Sqs\SqsClient;

/**
 * Class Queue
 * @package xutl\message\alimns
 */
class Queue extends Object implements QueueInterface
{
    /**
     * @var SqsClient;
     */
    public $client;

    /**
     * @var string
     */
    public $queueName;

    /**
     * @param array $message
     * @param int $delay
     * @return false|string
     */
    public function sendMessage($message, $delay = 0)
    {
        $message = Json::encode($message);
        return $this->client->sendMessage([
            'QueueUrl' => $this->queueName,
            'MessageBody' => $message,
            'DelaySeconds' => $delay,
        ])->get('MessageId');
    }

    /**
     * 获取消息
     * @return array|bool
     */
    public function receiveMessage()
    {
        $response = $this->client->receiveMessage(['QueueUrl' => $this->queueName]);

        if (empty($response['Messages'])) {
            return false;
        }

        $data = reset($response['Messages']);

        return [
            'id' => $data['MessageId'],
            'body' => $data['Body'],
            'queue' => $this->queueName,
            'receipt-handle' => $data['ReceiptHandle'],
        ];
    }

    /**
     * 修改消息可见时间
     * @param string $receiptHandle
     * @param int $visibilityTimeout
     * @return bool
     */
    public function changeMessageVisibility($receiptHandle, $visibilityTimeout)
    {
        $this->client->changeMessageVisibility([
            'QueueUrl' => $this->queueName,
            'ReceiptHandle' => $receiptHandle,
            'VisibilityTimeout' => $visibilityTimeout,
        ]);
    }

    /**
     * 删除消息
     * @param string $receiptHandle
     * @return bool
     */
    public function deleteMessage($receiptHandle)
    {
        $this->client->deleteMessage([
            'QueueUrl' => $this->queueName,
            'ReceiptHandle' => $receiptHandle,
        ]);
    }
}