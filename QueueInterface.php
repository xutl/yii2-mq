<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\mq;

interface QueueInterface
{
    /**
     * 发送消息
     * @param array $message
     * @param int $delay
     * @return @return false|string 成功返回消息ID，失败返回false
     */
    public function sendMessage($message, $delay = 0);

    /**
     * 消费消息
     * @return array
     */
    public function receiveMessage();

    /**
     * 修改消息可见时间
     * @param string $receiptHandle
     * @param int $visibilityTimeout
     * @return bool
     */
    public function changeMessageVisibility($receiptHandle, $visibilityTimeout);

    /**
     * 删除消息
     * @param string $receiptHandle
     * @return bool
     */
    public function deleteMessage($receiptHandle);
}