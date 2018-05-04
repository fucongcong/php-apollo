<?php

namespace Group;

class ApolloConfig
{   
    protected $configUrl;

    protected $appId;

    protected $config = [];

    public function __construct($configUrl, $appId)
    {
        $this->configUrl = $configUrl;
        $this->appId = $appId;
    }

    public function get($namespace, $key, $default = null, $cluster = "default")
    {   
        if (!isset($this->config[$namespace][$cluster])) {
            $this->pullConfig($cluster, $namespace);
        }

        return isset($this->config[$namespace][$cluster][$key]) ? $this->config[$namespace][$cluster][$key] : $default;
    }

    public function poll(int $time)
    {
        swoole_timer_tick($time * 1000, function () {
            foreach ($this->config as $namespace => $one) {
                foreach ($one as $cluster => $o) {
                    $this->pullConfig($cluster, $namespace);
                }
            }
        });
    }

    private function pullConfig($cluster, $namespace)
    {   
        $appId = $this->appId;
        $data = $this->curlGet($this->configUrl."/configfiles/json/{$appId}/{$cluster}/{$namespace}");
        if ($data && ($res = json_decode($data, true))) {
            $this->config[$namespace][$cluster] = $res;
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
