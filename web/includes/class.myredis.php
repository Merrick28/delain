<?php

class myredis
{
    var $redis;

    function __construct()
    {
        $this->connect();
    }

    function connect()
    {
        //Connecting to Redis server on localhost
        $this->redis = new Redis();
        $this->redis->connect(REDIS_SERVER, 6379);

    }

    function store($key, $val)
    {
        $this->redis->set($key, $val);
    }

    function get($key)
    {
        return $this->redis->get($key);
    }

    function delete($key)
    {
        return $this->redis->del($key);
    }

    function listallkeys()
    {
        $arList = $this->redis->keys("*");
        return $arList;
    }


}
