<?php

declare(strict_types=1);
namespace HyperfStore;

use Closure;
use RuntimeException;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Utils\ApplicationContext;

class Repository
{
    // 模型
    protected $model;
    
    // 依赖注入
    public static function make()
    {
        return ApplicationContext::getContainer()->make(static::class);
    }
    
    // 放入或使用模型
    public function model($model = null)
    {
        if($model)
        {
            $this->model = $model;
        }
        
        return $this->model;
    }
    
    // 多模型处理
    public function modelMap($models, Closure $function)
    {
        $function = $function->bindTo($this);
        
        foreach($models as $model)
        {
            $this->model($model);
            
            $function();
        }
    }
}
