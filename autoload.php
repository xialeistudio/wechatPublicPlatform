<?php
/**
 * 自动加载器
 * @project wechatPublicPlatform
 * @date 2015-4-13
 * @author xialei <xialeistudio@gmail.com>
 */
function loader($class)
{
    $file = $class . '.php';
    if (is_file($file)) {
        require_once $file;
    }
}

spl_autoload_register('loader');