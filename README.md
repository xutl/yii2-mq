# yii2-mns

适用于Yii2的消息服务组件，支持阿里云的MNS，AWS的SQS，以及Redis。

特别说明：非任务队列，也不是短消息那种私信组件，这是个纯消息组件。本来我是想做队列，我看了laravel,以及yii2其他人做的队列任务组件，我发现，
他们下发任务的时候要么发个闭包，要么发个序列化的类，包括我之前做的一个队列组件也是这么做的，后来我看了阿里云的消息队列服务的开发者
文档我觉得，消息服务本质上就是个纯消息服务，没必要把任务也放里面，一条消息就是一个普通的JSON字符串就行了，就像微信的公众号接收
服务端消息一样，接到消息干什么，怎么干我觉得是客户端的事。


###队列说明

之前看yiisoft上那个队列半成品给我带到沟里了，且它自带的redis的一直有bug,常年不维护。

下面是队列说明：

1、插入队列的消息，可以是数组或者是json,不要直接把任务对象放入队列。
2、消费消息时，该消息只是进入了保留期，大概1分钟后又会重新进入队列。
3、如果你消费消息后，处理该消息失败，或者其他原因需要修改保留期有相应的方法修改。
4、在消息消费完，你需要手动删除该消息。

以上概念是按照 阿里云的 
https://help.aliyun.com/document_detail/27414.html 实现的

## 安装


The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist xutl/yii2-mq
```

or add

```
"xutl/yii2-mq": "~1.0.0"
```

to the require section of your `composer.json` file.
   
## 组件配置

````php
//使用Redis
'queue' => [
    'class' => 'xutl\mq\redis\Client',
                'redis' => [
                    'scheme' => 'tcp',
                    'host' => '127.0.0.1',
                    'port' => 6379,
                    //'password' => '1984111a',
                    'db' => 0
                ],
],

//使用AWS SQS
'queue' => [
    'class' => 'xutl\mq\awsqs\Client',
                'sqs' => [
                    //etc
                ],
],
//使用阿里MNS
'queue' => [
   'class' => 'xutl\mq\alimns\Client',
               'endPoint' => '',
               'accessId'=>'',
               'accessKey'=>'',
],        
````

## 使用

```php

//入队
/** @var \xutl\mq\Queue $queue */
$queue = Yii::$app->queue->getQueueRef('mailer');
for ($i = 0; $i < 500; $i++) {
    $queue->sendMessage([
        'aaa'=>'bbb',
    ]);
}

//出队
/** @var \xutl\mq\Queue $queue */
$queue = Yii::$app->queue->getQueueRef('mailer');
/** @var \xutl\mq\Message $message */
while (($message = $queue->receiveMessage()) !== false) {
    $body = $message->getBody();
    var_dump($body);
    $message->delete();
}  
```

知乎上这篇甩锅我给我很大的启发。
https://zhuanlan.zhihu.com/p/25192112