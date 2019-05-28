## 安装

### 安装依赖
```
composer require baiy/admin-service
```

### 导入数据库
`./db.sql`

### 默认管理员
`admin/123456`

### 对应前端项目
<https://github.com/baiy/admin-ui>

### 项目配置

```php
<?php
// thinkphp 6
// 加入代码 `./route/app.php`
// 
use Baiy\Admin\Adapter\Think60\Adapter; // 加载tp适配器
use Baiy\Admin\Handle;

$handle = Handle::instance();
$handle->setAdapter(new Adapter());
//$handle->setDebug(app()->isDebug()); // 系统调试标示[可选]
//$handle->addNoCheckLoginRequestId($id); // 无需校验权限的api[]可选
//$handle->addOnlyLoginRequestId($id); // 只需登录即可访问的api[可选]
//$handle->setLogFilePath($path); // 日志文件路径[可选]
//$handle->setDbConnection($name); // 数据库连接标示[可选]
$handle->router('/api/'); // api入口路由注册 请求记住此入口url
```

```php
<?php
// Laravel 5.8
// 加入代码 在 `./app/Providers/RouteServiceProvider.php::map()` 方法中

use Baiy\Admin\Adapter\Laravel58\Adapter; // 加载Laravel适配器
use Baiy\Admin\Handle;

$handle = Handle::instance();
$handle->setAdapter(new Adapter());
//$handle->setDebug(config('app.debug')); // 系统调试标示[可选]
//$handle->addNoCheckLoginRequestId($id); // 无需校验权限的api[]可选
//$handle->addOnlyLoginRequestId($id); // 只需登录即可访问的api[可选]
//$handle->setLogFilePath($path); // 日志文件路径[可选]
//$handle->setDbConnection($name); // 数据库连接标示[可选]
$handle->router('/api/'); // api入口路由注册 请求记住此入口url
```

## 适配器开发

实现`\Baiy\Admin\Adapter\Adapter`抽象类即可






