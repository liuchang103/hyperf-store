<?php

declare(strict_types=1);
namespace HyperfStore;

use Closure;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Database\Exception\QueryException;

class Repository
{
    // 模型
    protected $model;
    
    // 依赖注入
    public static function make(Model $model = null)
    {
        $repository = ApplicationContext::getContainer()->make(static::class);
        
        // 放入模型
        $repository->model($model ?? $repository->build());
        
        return $repository;
    }
    
    // 放入或使用模型
    public function model(Model $model = null)
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

    // 获取数据
    public function __get($name)
    {
        return $this->model->$name;
    }
    
    // 捕捉错误
    protected function exception(Closure $function)
    {
        $function = $function->bindTo($this);
        
        try
        {
            return $function();
        }
        catch(QueryException $e)
        {
            return false;
        }
    }
    
    // 保存
    protected function save($data)
    {
        return $this->model->fill($data)->save();
    }
}
