<?php

declare(strict_types=1);
namespace HyperfStore\Route;

use Hyperf\HttpServer\Router\Router;

class Handle
{
    // 中间件
    protected $middlewares = [];
    
    // 命名空间缓存
    protected $namespace;
    
    // 前缀
    protected $prefix = '';

    // 命名空间
    public function namespace($namespace)
    {
        // 可以让命名空间叠加
        $this->namespace = Route::setNamespace(
            Route::makeNamespace($namespace)
        );
        
        return $this;
    }
    
    // 中间件
    public function middleware(...$middlewares)
    {
        foreach($middlewares as $name)
        {
            $this->middlewares[] = config('middlewares.alias.' . $name);
        }
        
        return $this;
    }
    
    // 前缀
    public function prefix($prefix)
    {
        $this->prefix = $prefix;
        
        return $this;
    }
    
    // 分组
    public function group($group)
    {
        if(is_string($group))
        {
            $group = function() use($group) { require($group); };
        }
        
        $this->router('addGroup', $this->prefix, $group, [
            'middleware' => $this->middlewares
        ]);
    }
    
    public function get($url, $controller)
    {
        $this->add('GET', $url, $controller);
    }
    
    public function post($url, $controller)
    {
        $this->add('POST', $url, $controller);
    }
    
    public function put($url, $controller)
    {
        $this->add('PUT', $url, $controller);
    }
    
    public function delete($url, $controller)
    {
        $this->add('DELETE', $url, $controller);
    }
    
    public function patch($url, $controller)
    {
        $this->add('PATCH', $url, $controller);
    }
    
    public function head($url, $controller)
    {
        $this->add('HEAD', $url, $controller);
    }
    
    // 路由方法
    public function add($http, $url, $controller)
    {
        $this->router('addRoute', $http, $url, Route::makeNamespace($controller), [
            'middleware' => $this->middlewares
        ]);
    }
    
    // 对接路由
    protected function router($method, ...$arguments)
    {
        forward_static_call_array([Router::class, $method], $arguments);
        
        // 命名空间缓存还原
        Route::setNamespace($this->namespace);
    }
}
