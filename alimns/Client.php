<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\message\alimns;

use Yii;
use yii\helpers\Json;
use yii\base\Component;
use yii\base\InvalidConfigException;
use xutl\message\ClientInterface;
use AliyunMNS\Queue;
use AliyunMNS\Topic;
use AliyunMNS\Config;
use AliyunMNS\AsyncCallback;
use AliyunMNS\Http\HttpClient;
use AliyunMNS\Exception\MnsException;

use AliyunMNS\Model\QueueAttributes;
use AliyunMNS\Requests\ListQueueRequest;
use AliyunMNS\Responses\ListQueueResponse;
use AliyunMNS\Requests\CreateQueueRequest;
use AliyunMNS\Responses\CreateQueueResponse;
use AliyunMNS\Requests\DeleteQueueRequest;
use AliyunMNS\Responses\DeleteQueueResponse;

use AliyunMNS\Model\TopicAttributes;
use AliyunMNS\Requests\CreateTopicRequest;
use AliyunMNS\Responses\CreateTopicResponse;
use AliyunMNS\Requests\DeleteTopicRequest;
use AliyunMNS\Responses\DeleteTopicResponse;
use AliyunMNS\Requests\ListTopicRequest;
use AliyunMNS\Responses\ListTopicResponse;

use AliyunMNS\Model\AccountAttributes;
use AliyunMNS\Requests\GetAccountAttributesRequest;
use AliyunMNS\Responses\GetAccountAttributesResponse;
use AliyunMNS\Requests\SetAccountAttributesRequest;
use AliyunMNS\Responses\SetAccountAttributesResponse;

class Client extends Component implements ClientInterface
{
    /**
     * @var  string
     */
    public $endPoint;

    /**
     * @var string
     */
    public $accessId;

    /**
     * @var string
     */
    public $accessKey;

    /**
     * @var null|string
     */
    public $securityToken = null;

    /**
     * @var null|Config
     */
    public $config = null;

    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (empty ($this->endPoint)) {
            throw new InvalidConfigException ('The "endPoint" property must be set.');
        }
        if (empty ($this->accessId)) {
            throw new InvalidConfigException ('The "accessId" property must be set.');
        }
        if (empty ($this->accessKey)) {
            throw new InvalidConfigException ('The "accessKey" property must be set.');
        }
        $this->client = new HttpClient($this->endPoint, $this->accessId, $this->accessKey, $this->securityToken, $this->config);
    }

    /**
     * 获取队列
     * @param string $queueName
     * @param bool $base64
     * @return Queue
     */
    public function getQueueRef($queueName)
    {
        return new Queue($this->client, $queueName, true);
    }
}