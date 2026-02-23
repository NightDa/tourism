<?php
class JsonCache
{
    private $cache_dir;
    private $ttl;

    public function __construct($ttl = 300)
    { // 5 minutes default
        $this->cache_dir = __DIR__ . '/../cache/';
        $this->ttl = $ttl;

        if (!file_exists($this->cache_dir)) {
            mkdir($this->cache_dir, 0777, true);
        }
    }

    public function get($key)
    {
        $cache_file = $this->cache_dir . md5($key) . '.cache';

        if (file_exists($cache_file)) {
            $age = time() - filemtime($cache_file);
            if ($age < $this->ttl) {
                return json_decode(file_get_contents($cache_file), true);
            }
        }

        return null;
    }

    public function set($key, $data)
    {
        $cache_file = $this->cache_dir . md5($key) . '.cache';
        file_put_contents($cache_file, json_encode($data));
    }

    public function clear($key = null)
    {
        if ($key) {
            $cache_file = $this->cache_dir . md5($key) . '.cache';
            if (file_exists($cache_file)) {
                unlink($cache_file);
            }
        } else {
            array_map('unlink', glob($this->cache_dir . '*.cache'));
        }
    }
}
