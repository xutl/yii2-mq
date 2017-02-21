<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\mq;

use yii\base\Object;

/**
 * Class Message
 * @package xutl\mq
 */
class Message extends Object
{

    /**
     * @var string 消息事件
     */
    public $event;

    /**
     * @var string 消息ID
     */
    public $messageId;

    /**
     * @var array|string 解析后的消息内容
     */
    public $messageBody;

    /**
     * @var string 消息内容
     */
    public $receiptHandle;

    /**
     * @var string 所属队列
     */
    public $queue;
}