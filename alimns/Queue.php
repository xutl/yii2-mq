<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\mq\alimns;

use Yii;
use yii\helpers\Json;
use xutl\mq\Message;
use AliyunMNS\Queue as QueueBackend;
use AliyunMNS\Requests\SendMessageRequest;
use AliyunMNS\Exception\MnsException;
use AliyunMNS\Model\SendMessageRequestItem;
use AliyunMNS\Requests\BatchSendMessageRequest;

/**
 * Class Queue
 * @package xutl\message\alimns
 */
class Queue extends \xutl\mq\Queue
{
    /**
     * @var \AliyunMNS\Http\HttpClient;
     */
    public $client;

    /**
     * @var \AliyunMNS\Queue
     */
    public $queue;

    /**
     * @var string
     */
    public $queueName;

    /**
     * @var bool 是否将消息体Base64后发送
     */
    public $base64 = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->queue = new QueueBackend($this->client, $this->queueName, $this->base64);
    }

    /**
     * @param array $message
     * @param int $delay
     * @return false|string
     */
    public function sendMessage($message, $delay = 0)
    {
        $message = Json::encode($message);
        $request = new SendMessageRequest($message, $delay, null, $this->base64);
        try {
            $response = $this->queue->sendMessage($request);
            if ($response->isSucceed()) {
                return $response->getMessageId();
            } else {
                return false;
            }
        } catch (MnsException $e) {
            Yii::trace(sprintf('send Message Failed:  `%s`...', $e));
            return false;
        }
    }

    /**
     * 批量推送消息到队列
     * @param array $messages
     * @param int $delay
     * @return array
     */
    public function batchSendMessage($messages, $delay = 0)
    {
        foreach ($messages as $key => $message) {
            $messages[$key] = new SendMessageRequestItem(Json::encode($message), $delay, null);
        }
        $count = count($messages);
        $size = ceil(count($count) / 16);
        $messages = array_chunk($messages, $size);
        $responses = [];
        foreach ($messages as $message) {
            $request = new BatchSendMessageRequest($message, $this->base64);
            try {
                $response = $this->queue->batchSendMessage($request);
                if ($response->isSucceed()) {
                    $responses = array_merge($responses, $response->getSendMessageResponseItems());
                }
            } catch (MnsException $e) {
                Yii::trace(sprintf('send Message Failed:  `%s`...', $e));
            }
        }
        return $responses;
    }

    /**
     * 获取消息
     * @return Message|bool
     */
    public function receiveMessage()
    {
        try {
            $response = $this->queue->receiveMessage(30);
            if ($response->isSucceed()) {
                return new Message([
                    'messageBody' => Json::decode($response->getMessageBody()),
                    'messageId' => $response->getMessageId(),
                    'receiptHandle' => $response->getReceiptHandle(),
                    'queue' => $this->queue
                ]);
            } else {
                return false;
            }
        } catch (MnsException $e) {
            Yii::trace(sprintf('receive Message Failed:  `%s`...', $e));
            return false;
        }
    }

    /**
     * 修改消息可见时间
     * @param string $receiptHandle
     * @param int $visibilityTimeout
     * @return bool
     */
    public function changeMessageVisibility($receiptHandle, $visibilityTimeout)
    {
        try {
            $response = $this->queue->changeMessageVisibility($receiptHandle, $visibilityTimeout);
            if ($response->isSucceed()) {
                return false;
            } else {
                return false;
            }
        } catch (MnsException $e) {
            Yii::trace(sprintf('receive Message Failed:  `%s`...', $e));
            return false;
        }
    }

    /**
     * 删除消息
     * @param string $receiptHandle
     * @return bool
     */
    public function deleteMessage($receiptHandle)
    {
        try {
            $this->queue->deleteMessage($receiptHandle);
            return true;
        } catch (MnsException $e) {
            Yii::trace(sprintf('Delete Message Failed:  `%s`...', $e));
            return false;
        }
    }
}