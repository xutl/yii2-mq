<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\mq;

use yii\base\Arrayable;
use yii\base\Object;
use yii\base\ArrayableTrait;

/**
 * Class Message
 * @package xutl\mq
 */
class Message extends Object implements Arrayable
{
    use ArrayableTrait;

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