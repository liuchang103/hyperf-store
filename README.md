## hyperf-store
Hyperf 常用分层及抽象，Repository 仓库层，Query 查询层等

## 安装

#### 引入包
```
composer require liuchang103/hyperf-store
```

## Repository 层

#### 继承
```
namespace App\Repository;

use App\Model\Admin;
use App\Server\Message;

class AdminRepository extends \HyperfStore\Repository
{
    public $message;

    // 注入消息依赖
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    // 放入模型
    public function build()
    {
        return new Admin;
    }

    // 禁止登陆
    public function close()
    {
        $this->model->close = false;
        $this->model->save();

        // 发送消息
        $this->message->send('close');
    }
}
```

#### 使用
空 Model
```
AdminRepository::make();
```
操作指定 Model
```
$admin = AdminRepository::make(Admin::find(1));
// or
$admin = AdminRepository::find(1);

// 禁止登陆
$admin->close();
```
获取当前 Model
```
$admin->model();
```
更换当前 Model
```
$admin->model(Admin::find(2));
```
使用 Model 字段
```
$admin->username;
```
保存 Model
```
// 新建 or 更新用户
$admin->save([
    'username' => 'admin',
    'password' => 'admin
]);
```
sql 查询异常不抛出

如果异常，将返回 false，用于 唯一索引 插入失败 
```
$admin->exception(function() use($admin){
    $admin->save([
        'username' => 'admin',
        'password' => 'admin
    ]);
});
```
批量操作 Model
```
$adminAll = Admin::get();

$admin->modelMap($adminAll, function() {
    
    // 关闭所有
    $this->close();
})
```

## Query 层

#### 继承
```
namespace App\Query;

use App\Model\Admin;

class AdminQuery extends \HyperfStore\Query
{
    // 构建模型
    protected static function build()
    {
        return Admin::orderBy('id', 'asc');
    }

    // 渴望加载
    protected static function with()
    {
        return ['log'];
    }
}
```
#### 使用
普通查询
```
AdminQuery::model()->get();

// 同等
Admin::orderBy('id', 'asc')->with('log')->get();
```
#### 批量条件查询
```
AdminQuery::query(
    AdminQuery::model(),
    [ 'username' => 'admin', 'close' => 1 ]
);

// 同等
Admin::orderBy('id', 'asc')
    ->with('log')
    ->where('username', 'admin)
    ->where('close', 1);
```
#### 批量查询自定义处理
定义一个处理方法
```
class AdminQuery extends \HyperfStore\Query
{
    // 自定义处理条件 
    protected static function queryTime($model, $data)
    {
        $time = expload('-', $data);

        return $model->where('start_time', '>', $time[0])
            ->where('end_time', '<', $time[1]);
    }
}
```
查询使用
```
AdminQuery::query(
    AdminQuery::model(),
    [ 'time' => '2020/02/02-2020/03/03' ]
);
```

## Route 路由
基于官方 Route 封装的一个更清晰的类

#### 命令空间
```
use HyperfStore\Route\Route;

Route::namespace('App\Controller')->get('/', 'IndexController@index');
```

#### 中间件
前往 config/autoload/middlewares.php 增加中间件命名
```
return [
    // 中间件
    'alias' => [
        'auth' => App\Middleware\Authentication::class
    ]
];
```
路由增加中间件
```
Route::middleware('auth')->get('/', 'IndexController@index');

// 多个中间件
Route::middleware('auth', 'permissions')->get('/', 'IndexController@index');
```

#### 分组
```
Route::middleware('auth')->group(function(){
    Route::get('/', 'IndexController@index');
    Route::get('/user', 'IndexController@user');
});
```
载入路由文件
```
Route::middleware('auth')->group(__DIR__ . '/auth.php');

// auth.php

Route::get('/', 'IndexController@index');
Route::get('/user', 'IndexController@user');
```
嵌套
```
Route::namespace('App\Controller')->group(function(){
    Route::get('/', 'IndexController@index');

    Route::namespace('User')->middleware('auth')->group(function(){
        
        Route::get('/user', 'IndexController@index');
    });
});
```

#### 分组前缀
```
Route::namespace('User')->prefix('/user')->group(function(){
    Route::get('/', 'IndexController@index');
});
```
#### 增加路由
```
Route::get($uri, $callback);
Route::post($uri, $callback);
Route::put($uri, $callback);
Route::patch($uri, $callback);
Route::delete($uri, $callback);
Route::head($uri, $callback);
```
#### 多个请求方法
```
Route::add(['GET', 'POST'], $uri, $callback);
```