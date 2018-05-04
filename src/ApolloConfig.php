<?php

namespace Group;

class ApolloConfig
{   
    protected $configUrl;

    protected $config = [];

    public function __construct($configUrl)
    {
        $this->configUrl = $configUrl;
    }

    public function get($appId, $namespace, $key, $cluster = "default")
    {   
        if (!isset($this->config[$appId][$namespace][$cluster])) {
            $this->pullConfig($appId, $cluster, $namespace);
        }

        return isset($this->config[$appId][$namespace][$cluster][$key]) ? $this->config[$appId][$namespace][$cluster][$key] : null;
    }

    public function poll(int $time)
    {
        swoole_timer_tick($time * 1000, function () {
            foreach ($this->config as $appId => $val) {
                foreach ($val as $namespace => $one) {
                    foreach ($one as $cluster => $o) {
                        $this->pullConfig($appId, $cluster, $namespace);
                    }
                }
            }
        });
    }

    private function pullConfig($appId, $cluster, $namespace)
    {
        $data = $this->curlGet($this->configUrl."/configfiles/json/{$appId}/{$cluster}/{$namespace}");
        if ($data && ($res = json_decode($data, true))) {
            $this->config[$appId][$namespace][$cluster] = $res;
        }
    }

    private function curlGet($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }
}
