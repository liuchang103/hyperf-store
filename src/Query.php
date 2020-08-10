<?php

declare(strict_types=1);
namespace HyperfStore;

use Hyperf\Utils\Str;

class Query
{
    // 模型构建
    protected static function build()
    {
        throw new RuntimeException('Query Default model empty');
    }
    
    // 渴望加载
    protected static function with()
    {
        return [];
    }
    
    // 获取 model
    public static function model($model = null)
    {
        if(!$model)
        {
            return static::build();
        }

        if($model instanceof Repository)
        {
            return $model->model();
        }

        return $model->with(static::with());
    }

    // 筛选器
    public static function query($model, $where = [])
    {
        foreach($where as $name => $value)
        {
            if(isset($value))
            {
                // 自定义过滤器
                $function = 'query' . Str::studly($name);

                $model = method_exists(static::class, $function) ? static::$function($model, $value) : $model->where($name, $value);
            }
        }

        return $model;
    }
}