<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\mq;

use yii\base\Object;
use yii\helpers\Json;

/**
 * Class Message
 * @package xutl\mq
 */
class Message extends Object
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
        return Json::decode($this->messageBody);
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