<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\mq;

/**
 * Interface ClientInterface
 * @package xutl\mq
 */
interface ClientInterface
{
    /**
     * 获取队列名称
     * @param string $queueName
     * @return Queue
     */
    public function getQueueRef($queueName);
}