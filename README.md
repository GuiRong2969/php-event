# PHP Validate

[![License](https://img.shields.io/github/license/GuiRong2969/php-validate)](LICENSE)
[![Php Version](https://img.shields.io/badge/php-%3E%3D5.5.0-brightgreen)](https://www.php.net/ChangeLog-5.php#PHP_5_5)
![GitHub tag (latest by date)](https://img.shields.io/github/v/tag/GuiRong2969/php-event)
[![Coverage Status](https://coveralls.io/repos/github/GuiRong2969/php-event/badge.svg?branch=master)](https://coveralls.io/github/GuiRong2969/php-event?branch=master)
![GitHub all releases](https://img.shields.io/github/downloads/GuiRong2969/php-event/total)

一个简洁小巧的php事件监听器。

- 事件系统主要负责应用解耦
- 单个事件可以拥有多个互不依赖的监听器
- 提供了一个简单的观察者实现
- 订阅模式下,事件订阅者是可以在自身内部订阅多个事件的类
- 事件注册 `\Guirong\Event\listen($event,$listener)`，用于 [快速注册事件监听](#event-listen)
- 事件触发 `\Guirong\Event\trigger($service,$event,$payload)`，用于 [分发事件](#event-dispatch)

> 使用助手函数 [`event()`](#event-function)

## 项目地址

- **github** <https://github.com/GuiRong2969/php-event.git>

> **注意：** 
-  版本要求 `php >= 5.5.0`
## 安装

```bash
composer require guirong/php-event
```

## 注册事件和监听器

应用中的 `\Guirong\Event\Event` 服务类为注册所有的事件监听器提供了一个便利的场所。其中， listen 属性包含了所有事件 (键) 以及事件对应的监听器 (值) 的数组。当然，你可以根据应用的需要，添加多个事件到 listen 属性包含的数组中。你可以直接使用此类作为服务提供者，也可以继承它（建议）。 举个例子，让我们来定义一个新的服务提供者，并来添加一个 OrderPayed 事件：

- 配置示例: (_本文档的服务提供者定义为 `\Guirong\Event\Event\EventService`_ )

```php
<?php

use Guirong\Event\Event;

class EventService extends Event
{
    /**
     * 应用程序的事件监听器映射
     *
     * @var array
     */
    protected $listen = [
        'App\Events\OrderPayed' => [
            'App\Listeners\NotifyBuyerListener',
            'App\Listeners\NotifyShopListener',
        ],
    ];

    /**
     * 应用程序的事件，自动注册
     */
    protected function register()
    {
        parent::register();
    }
}
```

### 定义事件

- 事件类是一个保存与事件相关信息的容器。例如，假设我们生成的 OrderPayed 事件接收一个数据集合：

```php
<?php

namespace App\Events;

class OrderPayed
{

    /**
     * 订单数据集合
     * 
     * @var array 
     */
    public $order;

    /**
     * 创建一个事件实例。
     *
     * @param  array  $order
     * @return void
     */
    public function __construct(array $order)
    {
        $this->order = $order;
    }
}
```

### 定义监听器

- 事件监听器默认在 handle 方法中接收实例，在方法中你可以执行任何必要的响应事件的操作

***`NotifyBuyerListener` 监听器：***

```php
<?php

namespace App\Listeners;

use App\Events\OrderPayed;

class NotifyBuyerListener
{

    /**
     * 创建事件监听器。
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * 处理事件。
     *
     * @param  \App\Events\OrderPayed  $event
     * @return void
     */
    public function handle(OrderPayed $event)
    {
        // 使用 $event->order 来访问 order, 处理业务 ...
        $order = $event->order;
    }
}
```

***`NotifyShopListener` 监听器：***

```php
<?php

namespace App\Listeners;

use App\Events\OrderPayed;

class NotifyShopListener
{

    /**
     * 创建事件监听器。
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * 处理事件。
     *
     * @param  \App\Events\OrderPayed  $event
     * @return void
     */
    public function handle(OrderPayed $event)
    {
        // 使用 $event->order 来访问 order, 处理业务 ...
        $order = $event->order;
    }
}
```

<a name="event-dispatch"></a>
### 分发事件

- 如果要分发事件，你可以将事件实例传递给函数 `\Guirong\Event\trigger()` 。该函数将会把事件分发到所有该事件相应的已经注册了的监听器上

```php
<?php

namespace App\Controllers;

use App\Events\OrderPayed;
use Guirong\Event\Event\EventService;

class Order
{
    /**
     * 将付款成功的订单,发送消息通知
     *
     * @param  string  $orderNo
     */
    public function payed($orderNo)
    {
        $order = [
            'orderNo' => $orderNo,
            'shopId' => 'shop_3',
            'buyerId' => 'buyer_6',
        ];

        // 订单付款成功，消息通知买家和卖家逻辑 ...

        \Guirong\Event\trigger(
            EventService::class, 
            new OrderPayed($order)
        );
    }
}
```

<a name="event-function"></a>
### 助手函数 `event()`

> `EventService` 服务类由 `Guirong\Event\Container` 容器创建并接管，不会重复创建实例 假如你觉得事件分发时每次都要传入服务类不方便，可以自己定一个简单的助手函数，引入当前的项目中

```php
<?php

if (!file_exists('event')) {
    /**
     * 事件分发助手函数
     * 
     * @param mixed $event
     * @param array $payload
     * @return void
     */
    function event($event, array $payload = [])
    {
        return \Guirong\Event\trigger(
            EventService::class,
            $event,
            $payload
        );
    }
}
```

## 事件订阅者

事件订阅者是可以在自身内部订阅多个事件处理器（事件类），订阅者定义的 `subscribe` 方法接收一个事件分发器实例。你可以调用对应事件分发器上的 `listen` 方法来注册事件监听器：

```php
<?php

namespace App\Listeners;

use App\Events\UserRegister;
use App\Events\UserDestory;

class UserEventSubscriber
{
    /**
     * 处理用户注册账户事件。
     */
    public function onUserRegister($event) {
        // 使用 $event->user 来访问用户信息, 处理业务 ...
        $user = $event->user;
    }

    /**
     * 处理用户注销账户事件。
     */
    public function onUserDestory($event) {
        // 使用 $event->user 来访问用户信息, 处理业务 ...
        $user = $event->user;
    }

    /**
     * 为订阅者注册监听器
     *
     * @param  \Guirong\Event\Dispatcher\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            UserRegister::class,
            'App\Listeners\UserEventSubscriber@onUserRegister'
        );

        $events->listen(
            UserDestory::class,
            'App\Listeners\UserEventSubscriber@onUserDestory'
        );
    }
}
```
### 定义事件

***`UserRegister` 事件：***

```php
<?php

namespace App\Events;

class UserRegister
{

    /**
     * 用户信息集合
     * 
     * @var array 
     */
    public $user;

    /**
     * 创建一个事件实例。
     *
     * @param  array  $user
     * @return void
     */
    public function __construct(array $user)
    {
        $this->user = $user;
    }
}
```

***`UserDestory` 事件：***

```php
<?php

namespace App\Events;

class UserDestory
{

    /**
     * 用户信息集合
     * 
     * @var array 
     */
    public $user;

    /**
     * 创建一个事件实例。
     *
     * @param  array  $user
     * @return void
     */
    public function __construct(array $user)
    {
        $this->user = $user;
    }
}
```

### 注册事件订阅者

在编写完订阅者之后，就可以通过事件分发器对订阅者进行注册。你可以在 EventService 中的 $subscribe 属性中注册订阅者。例如，让我们将 UserEventSubscriber 添加到数组列表中：

```php
<?php

use Guirong\Event\Event;
use App\Listeners\UserEventSubscriber;

class EventService extends Event
{
    /**
     * 应用程序的事件监听器映射
     *
     * @var array
     */
    protected $listen = [
        //
    ];

    /**
     * 需要注册的订阅者类
     *
     * @var array
     */
    protected $subscribe = [
        UserEventSubscriber::class
    ];
}
```

### 分发事件

> 使用助手函数 [`event()`](#event-function)

```php
<?php

namespace App\Controllers;

use App\Events\UserRegister;
use App\Events\UserDestory;

class User
{
    /**
     * 将注册成功的用户消息,站内信通知给后台
     *
     * @param  array  $user
     */
    public function register($user)
    {
        // 触发用户注册成功的事件
        event(new UserRegister($user));
    }

    /**
     * 将注销账号的用户信息,站内信通知给后台
     *
     * @param  array  $user
     */
    public function destory($user)
    {
        // 触发用户注销的事件
        event(new UserDestory($user));
    }
}
```

<a name="event-listen"></a>

### 手动注册事件

事件通常是在你服务类 `EventService` 的 `$listen` 数组中注册；然而，你也可以在 `EventService` 的 `register` 方法中手动注册基于闭包的这些事件：

```php
<?php

use Guirong\Event\Event;
use App\Listeners\UserEventSubscriber;

class EventService extends Event
{
    /**
     * 应用程序的事件监听器映射
     *
     * @var array
     */
    protected $listen = [
        'App\Events\OrderPayed' => [
            'App\Listeners\NotifyBuyerListener',
            'App\Listeners\NotifyShopListener',
        ],
    ];

    /**
     * 需要注册的订阅者类
     *
     * @var array
     */
    protected $subscribe = [
        UserEventSubscriber::class
    ];

    /**
     * 注册应用中的其它事件
     */
    protected function register()
    {
        parent::register();

        // 用户登录事件
        \Guirong\Event\listen('userLogin', function ($userId, $userName) {
            // 使用 payload 中传递来的 userId, userName 参数, 处理业务 ...
            
        });

        // 用户退出登录事件
        \Guirong\Event\listen('userLogout', function ($userId, $userName) {
            // 使用 payload 中传递来的 userId, userName 参数, 处理业务 ...

        });
    }
}

```

### 分发事件

```php
<?php

$userId = 'user_01';

$userName = 'jery';

// 触发用户登录的事件
event('userLogin', [$userId, $userName]);

// 触发用户登录的事件
event('userLogout', [$userId, $userName]);

```

## 结语
> *本文的事件系统只是一种代码解耦的写法，并非工具性的依赖包，感兴趣的话可以自己任意扩展。*

## License

[MIT](LICENSE)


## 我的其他项目

### `guirong/cli-message` [github](https://github.com/GuiRong2969/cli-message)

一个简单易用的，命令行输出样式工具库

### `guirong/php-router` [github](https://github.com/GuiRong2969/php-router)
 
轻量且快速的路由库

### `guirong/php-closure` [github](https://github.com/GuiRong2969/php-closure)

闭包的序列化和反序列化类库

### `guirong/php-validate` [github](https://github.com/GuiRong2969/php-validate)

一个轻量级且功能丰富的验证和过滤库
