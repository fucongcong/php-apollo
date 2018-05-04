<?php

use Group\ApolloConfig;

require 'vendor/autoload.php';

$config = new ApolloConfig("http://localhost:8080");
//每2秒轮询一次
$config->poll(2);

swoole_timer_tick(1000, function () use ($config) {
    $val = $config->get("groupco", "application", "environment");
    var_dump($val);
});