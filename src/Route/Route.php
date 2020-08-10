<?php

declare(strict_types=1);
namespace HyperfStore\Route;

use Closure;

class Route
{
    // 当前命名空间
    protected static $namespace = '';
    
    // 使用空间空间
    public static function makeNamespace($name)
    {
        return static::$namespace . '\\' . $name;
    }
    
    // 设置命名空间
    public static function setNamespace($namespace = null)
    {
        // 缓存一份
        $temp = static::$namespace;
            
        if(isset($namespace))
        {
            // 更新
            static::$namespace = $namespace;
        }
        
        return $temp;
    }
    
    // 静态方法
    public static function __callStatic($method, $arguments)
    {
        return (new Handle)->$method(...$arguments);
    }
}
