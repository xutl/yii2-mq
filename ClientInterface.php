<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\message;

interface ClientInterface
{
    /**
     * 获取队列名称
     * @param string $queueName
     * @return QueueInterface
     */
    public function getQueueRef($queueName);
}