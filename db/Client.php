<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\mq\db;

use Yii;
use yii\base\Object;
use yii\db\Connection;
use yii\base\InvalidConfigException;
use xutl\mq\ClientInterface;

/**
 * Class Client
 * @package xutl\mq\db
 */
class Client extends Object implements ClientInterface
{
    /**
     * @var array|string|Connection
     */
    public $db = 'db';

    /**
     * @var integer
     */
    public $expire = 60;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (is_string($this->db)) {
            $this->db = Yii::$app->get($this->db);
        } elseif (is_array($this->db)) {
            if (!isset($this->db['class'])) {
                $this->db['class'] = Connection::className();
            }
            $this->db = Yii::createObject($this->db);
        }
        if (!$this->db instanceof Connection) {
            throw new InvalidConfigException("Queue::db must be application component ID of a SQL connection.");
        }
    }

    /**
     * 获取队列
     * @param string $queueName
     * @return Queue
     */
    public function getQueueRef($queueName)
    {
        return new Queue([
            'db' => $this->db,
            'queueName' => $queueName,
            'expire' => $this->expire
        ]);
    }
}