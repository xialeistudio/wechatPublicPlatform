<?php

/**
 * @project wechatPublicPlatform
 * @date 2015-4-13
 * @author xialei <xialeistudio@gmail.com>
 */
class Cache
{

    function __construct()
    {
        throw new Exception('不可被实例化');
    }

    /**
     * 设置缓存
     * @param string $key
     * @param string $value
     * @param int $expires 0为不过期
     * @return int
     */
    public static function set($key, $value, $expires = 0)
    {
        $cachePath = __DIR__ . '/cache';
        if (!is_dir($cachePath)) {
            mkdir($cachePath, 666);
        }
        $data = [
            'key' => $key,
            'value' => $value,
            'expires' => $expires
        ];
        return file_put_contents($cachePath . '/' . md5($key), json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取缓存
     * @param string $key
     * @return bool
     */
    public static function get($key)
    {
        $cacheFile = __DIR__ . '/cache/' . md5($key);
        //检测是否存在
        if (!is_file($cacheFile)) {
            return false;
        }
        //检测是否过期
        $data = json_decode(file_get_contents($cacheFile), true);
        //获取最近修改时间
        $mtime = filemtime($cacheFile);
        if (time() > $mtime + $data['expires']) {
            unlink($cacheFile);
            return false;
        }
        return $data['value'];
    }

    /**
     * 删除缓存
     * @param string $key
     * @return bool
     */
    public static function delete($key)
    {
        $cacheFile = __DIR__ . '/cache/' . md5($key);
        if (is_file($cacheFile)) {
            return unlink($cacheFile);
        }
        return true;
    }
}