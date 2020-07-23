Cadmin php 服务端 

> 项目地址: [[github](https://github.com/baiy/Cadmin-server-php)] [[gitee](https://gitee.com/baiy/Cadmin-server-php)]
>
> 在线文档地址: <https://baiy.github.io/Cadmin/>

### 特点

1. 为便于给现有系统加入后台管理功能和加快新系统开发, 后台核心系统尽可能的减少依赖, 不侵入外层业务系统.
2. 对请求处理按照请求类型可自定义`请求调度类`,便于不用业务系统使用和开发. 系统内置`Thinkphp`/`Laravel`框架的`请求调度类`


### 安装
```
composer require baiy/cadmin
```

### 数据库

详见 [数据库结构](https://baiy.github.io/Cadmin/#/server/db) 一章

### 使用方法
> 在代码安装和数据库导入完毕后, 接下来需要将后台系统的入口代码嵌入当前系统的合适位置, 并进行相应的配置

#### 入口代码说明

```php
<?php
$admin = \Baiy\Cadmin\Admin::instance();
$admin->setPdo($pdo); // 设置数据库操作对象
// $admin->setTablePrefix(); // [可选] 设置系统内置数据表前缀 设置后注意修改表名
// $admin->registerDispatcher(); // [可选] 注册自定义请求调度器
// $admin->registerPassword(); // [可选] 注册自定义用户密码生成器
// $admin->addNoCheckLoginRequestId($id); // [可选] 无需校验权限的api
// $admin->addOnlyLoginRequestId($id); // [可选] 只需登录即可访问的api
// $admin->setInputActionName($name); // [可选] 设置请求标识变量名
// $admin->setInputTokenName($name); // [可选] 设置请求token变量名
// $admin->setLogCallback(function(\Baiy\Cadmin\Log $log){}); // [可选] 请求日志记录回调函数

// [可选] 运行时SQL监听 便于日志记录 需要根据实际项目使用sql监听方法进行对应调用
// $admin->getContext()->addListenSql($sql, $time);

// 获取业务处理响应结果 后续根据实际项目进行`json`输出
$response = $admin->run();
```

#### Thinkphp 6.0

##### 代码插入位置
```
/route/app.php
```
> 请求根据实际路由配置文件添加代码

##### 示例代码 
```php
<?php
use think\facade\Db;
use think\facade\Log;
use think\facade\Route;

// 客户端api路由入口 请求记住此url这是提供给客户端api地址
Route::any('/api/admin/', function (){
    $admin = \Baiy\Cadmin\Admin::instance();
    // 临时方案: tp 需要先查询一次数据库 才能获取到pdo对象
    Db::connect()->execute("select 1");
    $admin->setPdo(Db::connect()->getPdo()); // 设置数据库操作对象
    $admin->registerDispatcher(new \Baiy\Cadmin\Dispatch\Thinkphp60()); // [可选] 注册内置的thinkphp调用类
    // 其他配置省略 见上方[入口代码说明] ..... 

    //  [可选] 设置请求日志记录回调函数
    // $admin->setLogCallback(function (\Baiy\Cadmin\Log $log) {
    //    Log::write($log->toJson(), 'notice');
    // });

    // 运行时SQL监听 便于日志记录
    // Db::listen(function ($sql, $time, $master) use ($admin) {
    //    $admin->getContext()->addListenSql($sql, $time);
    // });

    // 运行
    return response($admin->run()->toArray(), 200,[],'json');
})->allowCrossDomain();
```

#### Laravel 5.8

##### 代码插入位置
```
/routes/api.php
```
> 请求根据实际路由配置文件添加代码
>
> 添加到`/routes/web.php` 注意添加[CSRF 白名单](https://learnku.com/docs/laravel/5.8/csrf/3892)

##### 示例代码 
```php
<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

// 客户端api路由入口 请求记住此url这是提供给客户端api地址
Route::any('/api/admin/', function () {
    // 前后端分离项目一般会有跨域问题 自行处理
    header('Access-Control-Allow-Origin: *');
    $admin = \Baiy\Cadmin\Admin::instance();
    $admin->setPdo(Db::connection()->getPdo()); // 设置数据库操作对象
    $admin->registerDispatcher(new \Baiy\Cadmin\Dispatch\Laravel58()); // [可选] 注册内置的thinkphp调用类
    // 其他配置省略 见上方[入口代码说明] .....

    // [可选] 设置请求日志记录回调函数
    // $admin->setLogCallback(function (\Baiy\Cadmin\Log $log) {
    //    Log::info($log->toJson());
    // });

    // 运行时SQL监听 便于日志记录
    // Db::listen(function ($query) use ($admin) {
    //    $admin->getContext()->addListenSql(
    //        sprintf(str_replace("?","%s",$query->sql),...$query->bindings),
    //        $query->time
    //    );
    // });

    // 运行
    return response()->json($admin->run()->toArray());
});
```
### 自定义用户密码生成策略

1. 实现 `Baiy\Cadmin\Password\Password` 接口
2. 注册密码生成器,使用`\Baiy\Cadmin\Admin::registerPassword()`

系统内置密码生成器: <https://github.com/baiy/Cadmin-server-php/blob/master/src/Password/PasswrodDefault.php>

> 内置密码生成规则: `base64_encode(hash('sha256',hash("sha256", $password.$salt,FALSE).$salt,FALSE).'|'.$salt);`

### 自定义请求调度器开发

1. 实现 `\Baiy\Cadmin\Dispatch\Dispatch` 接口
2. 注册调度器,使用`\Baiy\Cadmin\Admin::registerDispatcher()`

系统内置调度器: <https://github.com/baiy/Cadmin-server-php/tree/master/src/Dispatch>