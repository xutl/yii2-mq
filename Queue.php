<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\mq;

use Yii;
use yii\base\Object;

/**
 * 队列基类
 * @package xutl\mq
 */
abstract class Queue extends Object
{
    /**
     * 发送消息
     * @param array $message 消息正文
     * @param int $delay 指定的秒数延后可被消费，单位为秒
     * @return @return false|string 成功返回消息ID，失败返回false
     */
    abstract public function sendMessage($message, $delay = 0);

    /**
     * 批量推送消息到队列
     * @param array $messages
     * @param int $delay
     * @return false|string
     */
    public function batchSendMessage($messages, $delay = 0)
    {
        $successCount = 0;
        foreach ($messages as $message) {
            if ($this->sendMessage($message, $delay)) {
                $successCount++;
            }
        }
        return $successCount;
    }

    /**
     * 消费消息
     * @return array
     */
    abstract public function receiveMessage();

    /**
     * 批量消费消息\
     * @param int $num 本次获取的消息数量
     * @return array
     */
    public function batchReceiveMessage($num = 10)
    {
        $message = [];
        for ($i = 1; $i <= $num; $i++) {
            $message[] = $this->receiveMessage();
        }
        return $message;
    }

    /**
     * 修改消息可见时间
     * @param string $receiptHandle
     * @param int $visibilityTimeout
     * @return bool
     */
    abstract public function changeMessageVisibility($receiptHandle, $visibilityTimeout);

    /**
     * 删除消息
     * @param string $receiptHandle
     * @return bool
     */
    abstract public function deleteMessage($receiptHandle);

    /**
     * 批量删除消息
     * @param array $receiptHandles
     * @return int
     */
    public function batchDeleteMessage($receiptHandles)
    {
        $successCount = 0;
        foreach ($receiptHandles as $receiptHandle) {
            if ($this->deleteMessage($receiptHandle)) {
                $successCount++;
            }
        }
        return $successCount;
    }
}