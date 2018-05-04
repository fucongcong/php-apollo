# php-apollo

#### 环境依赖
- php > 5.3 
- swoole 

#### composer安装

    composer require group-co/php-apollo

#### 使用

    <?php

    use Group\ApolloConfig;

    require 'vendor/autoload.php';

    $config = new ApolloConfig("http://localhost:8080", "groupco");
    //每2秒轮询一次
    $config->poll(2);

    swoole_timer_tick(1000, function () use ($config) {
        $val = $config->get("application", "environment1", "2");
        var_dump($val);
    });