<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\mq\db;

use Yii;
use yii\helpers\Json;
use yii\base\Object;
use yii\db\Connection;
use xutl\mq\QueueInterface;

/**
 * Class Queue
 * @package xutl\mq\db
 */
class Queue extends Object implements QueueInterface
{
    /**
     * @var Connection;
     */
    public $db;

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
        $this->db->createCommand()->insert('{{%message_queue}}', [
            'queue' => $this->queueName,
            'payload' => $payload,
            'available_at' => time() + $delay,
            'created_at' => time(),
        ])->execute();
        return $this->db->lastInsertID;
    }

    /**
     * 批量推送消息到队列
     * @param array $messages
     * @param int $delay
     * @return false|string
     */
    public function BatchSendMessage($messages, $delay = 0)
    {
        $successCount = 0;
        foreach ($messages as $key => $message) {
            if ($this->sendMessage($message, $delay)) {
                $successCount++;
            }

        }
        return $successCount;
    }

    /**
     * 获取消息
     * @return array|bool
     */
    public function receiveMessage()
    {
        //准备事务
        if (($message = $this->pop()) != false) {
            $transaction = $this->db->beginTransaction();
            try {
                $this->db->createCommand("UPDATE {{%message_queue}} SET available_at=:available_at WHERE id=:id")
                    ->bindValue(':available_at', time() + $this->expire)
                    ->bindValue(':id', $message['messageId'])
                    ->execute();
                $transaction->commit();
                return $message;
            } catch (\Exception $e) {
                $transaction->rollBack();
                return false;
            }
        }
        return false;
    }

    /**
     * 弹个消息出来
     * @return array|bool
     */
    protected function pop()
    {
        $message = $this->db->createCommand('SELECT * FROM {{%message_queue}} WHERE queue=:queue AND available_at<=:available_at for update ')
            ->bindValue(':queue', $this->queueName)
            ->bindValue(':available_at', time())
            ->queryOne();
        $receiptHandle = $message['payload'];
        $m = Json::decode($message['payload']);
        return $message ? [
            'messageId' => $message['id'],
            'messageBody' => $m['body'],
            'receiptHandle' => $receiptHandle,
            'queue' => $this->queueName,
        ] : false;
    }

    /**
     * 修改消息可见时间
     * @param string $receiptHandle
     * @param int $visibilityTimeout
     * @return bool
     */
    public function changeMessageVisibility($receiptHandle, $visibilityTimeout)
    {
        $this->db->createCommand()->update(
            '{{%message_queue}}',
            [
                'available_at' => time() + $visibilityTimeout,
            ],
            ['payload' => $receiptHandle]
        )->execute();
    }

    /**
     * 删除消息
     * @param string $receiptHandle
     * @return bool
     */
    public function deleteMessage($receiptHandle)
    {
        $this->db->createCommand()->delete('{{%message_queue}}', ['payload' => $receiptHandle])->execute();
    }
}