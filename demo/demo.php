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