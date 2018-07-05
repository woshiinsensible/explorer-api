<?php
namespace App\Http\Commons;

class RedisOperate
{

    const redis_host = '*';
    const redis_port = '*';
    const redis_auth = '*';

    public function RedisGet($params)
    {
        $host  = self::redis_host;
        $port = self::redis_port;
        $auth  = self::redis_auth;


        $redis = new \Redis();
        if (! $redis->connect($host, $port)) {
            trigger_error('Redis Error', E_USER_ERROR);
        }

        $redis->auth($auth);

        $res = $redis->get($params);

        $redis->close();

        return $res;
    }

    public function RedisSet($key,$params,$timeout='')
    {
        $host  = self::redis_host;
        $port = self::redis_port;
        $auth  = self::redis_auth;


        $redis = new \Redis();
        if (! $redis->connect($host, $port)) {
            trigger_error('Redis Error', E_USER_ERROR);
        }

        $redis->auth($auth);

        if(empty($timeout)){
            $redis->set($key,$params);
        }else{
            $redis->set($key,$params,$timeout);
        }

        $redis->close();

    }

    public function RedisExist($params)
    {
        $host  = self::redis_host;
        $port = self::redis_port;
        $auth  = self::redis_auth;


        $redis = new \Redis();
        if (! $redis->connect($host, $port)) {
            trigger_error('Redis Error', E_USER_ERROR);
        }

        $redis->auth($auth);

        $res = $redis->exists($params);

        $redis->close();

        return $res;
    }

    public function RedisDel($params)
    {
        $host  = self::redis_host;
        $port = self::redis_port;
        $auth  = self::redis_auth;


        $redis = new \Redis();
        if (! $redis->connect($host, $port)) {
            trigger_error('Redis Error', E_USER_ERROR);
        }

        $redis->auth($auth);

        $res = $redis->del($params);

        $redis->close();

        return $res;
    }

    public function RedisHSet($key,$field,$value)
    {
        $host  = self::redis_host;
        $port = self::redis_port;
        $auth  = self::redis_auth;


        $redis = new \Redis();
        if (! $redis->connect($host, $port)) {
            trigger_error('Redis Error', E_USER_ERROR);
        }

        $redis->auth($auth);

        $res = $redis->hSet($key,$field,$value);


        $redis->close();

        return $res;
    }

    public function RedisHGetAll($params)
    {
        $host  = self::redis_host;
        $port = self::redis_port;
        $auth  = self::redis_auth;


        $redis = new \Redis();
        if (! $redis->connect($host, $port)) {
            trigger_error('Redis Error', E_USER_ERROR);
        }

        $redis->auth($auth);

        $res = $redis->hGetAll($params);

        $redis->close();

        return $res;
    }

    public function RedisHVals($params)
    {
        $host  = self::redis_host;
        $port = self::redis_port;
        $auth  = self::redis_auth;


        $redis = new \Redis();
        if (! $redis->connect($host, $port)) {
            trigger_error('Redis Error', E_USER_ERROR);
        }

        $redis->auth($auth);

        $res = $redis->hVals($params);

        $redis->close();

        return $res;
    }

    public function RedisHGet($key,$params)
    {
        $host  = self::redis_host;
        $port = self::redis_port;
        $auth  = self::redis_auth;


        $redis = new \Redis();
        if (! $redis->connect($host, $port)) {
            trigger_error('Redis Error', E_USER_ERROR);
        }

        $redis->auth($auth);

        $res = $redis->hGet($key,$params);

        $redis->close();

        return $res;
    }

    public function RedisHmset($key,Array $params)
    {
        $host  = self::redis_host;
        $port = self::redis_port;
        $auth  = self::redis_auth;


        $redis = new \Redis();
        if (! $redis->connect($host, $port)) {
            trigger_error('Redis Error', E_USER_ERROR);
        }

        $redis->auth($auth);

        $res = $redis->hMset($key,$params);

        $redis->close();

        return $res;
    }

    public function RedisRPush($key,$value)
    {
        $host  = self::redis_host;
        $port = self::redis_port;
        $auth  = self::redis_auth;


        $redis = new \Redis();
        if (! $redis->connect($host, $port)) {
            trigger_error('Redis Error', E_USER_ERROR);
        }

        $redis->auth($auth);

        $res = $redis->RPush($key,$value);


        $redis->close();

        return $res;
    }

    public function RedisLIndex($key,$index)
    {
        $host  = self::redis_host;
        $port = self::redis_port;
        $auth  = self::redis_auth;


        $redis = new \Redis();
        if (! $redis->connect($host, $port)) {
            trigger_error('Redis Error', E_USER_ERROR);
        }

        $redis->auth($auth);

        $res = $redis->lIndex($key,$index);


        $redis->close();

        return $res;
    }

    public function RedisLPop($key)
    {
        $host  = self::redis_host;
        $port = self::redis_port;
        $auth  = self::redis_auth;


        $redis = new \Redis();
        if (! $redis->connect($host, $port)) {
            trigger_error('Redis Error', E_USER_ERROR);
        }

        $redis->auth($auth);

        $res = $redis->lPop($key);


        $redis->close();

        return $res;
    }

    public function RedisLRange($key,$start,$end)
    {
        $host  = self::redis_host;
        $port = self::redis_port;
        $auth  = self::redis_auth;


        $redis = new \Redis();
        if (! $redis->connect($host, $port)) {
            trigger_error('Redis Error', E_USER_ERROR);
        }

        $redis->auth($auth);

        $res = $redis->lRange($key,$start,$end);


        $redis->close();

        return $res;
    }

    public function RedisIncr($key)
    {
        $host  = self::redis_host;
        $port = self::redis_port;
        $auth  = self::redis_auth;


        $redis = new \Redis();
        if (! $redis->connect($host, $port)) {
            trigger_error('Redis Error', E_USER_ERROR);
        }

        $redis->auth($auth);

        $res = $redis->incr($key);


        $redis->close();

        return $res;
    }

    public function RedisExpire($key,$time)
    {
        $host  = self::redis_host;
        $port = self::redis_port;
        $auth  = self::redis_auth;


        $redis = new \Redis();
        if (! $redis->connect($host, $port)) {
            trigger_error('Redis Error', E_USER_ERROR);
        }

        $redis->auth($auth);

        $res = $redis->expire($key,$time);


        $redis->close();

        return $res;
    }

}