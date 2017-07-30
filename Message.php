<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\mq;

use yii\base\BaseObject;

/**
 * Class Message
 * @package xutl\mq
 */
class Message extends BaseObject
{
    /**
     * @var Queue 所属队列实例
     */
    public $queue;

    /**
     * @var string 消息ID
     */
    public $messageId;

    /**
     * @var array|string 解析后的消息内容
     */
    public $messageBody;

    /**
     * @var string 消息句柄
     */
    public $receiptHandle;

    /**
     * 获取消息ID
     * @return string
     */
    public function getId()
    {
        return $this->messageId;
    }

    /**
     * 获取消息详情
     */
    public function getBody()
    {
        return $this->messageBody;
    }

    /**
     * 修改消息可见时间
     * @param int $delay
     */
    public function release($delay = 60)
    {
        $this->queue->changeMessageVisibility($this->queue, $delay);
    }

    /**
     * 删除消息
     * @return bool
     */
    public function delete()
    {
        return $this->queue->deleteMessage($this->receiptHandle);
    }
}