# mix-framework
> 开坑)
在这里开始我的一把梭组件开发

[x] 通过`php bin/mix.php start` 启动服务(可通过配置文件配置服务)

[x] 接入config.php配置服务

[ ] 接入一把梭API

[ ] 接入常见组件提供一键command

# 启动
`php bin/mix.php start`
```php
#!/usr/bin/env php
<?php
ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');
ini_set('error_reporting', E_ALL ^ E_NOTICE);
ini_set('memory_limit', '1G');

date_default_timezone_set('Asia/Shanghai');

! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));

require BASE_PATH . '/vendor/autoload.php';

define("APP_DEBUG", env('APP_DEBUG'));
use App\Error;

Error::register();

(function () {
    (new \Mix\Framework\Application())->run();
})();
```

# License
Apache License Version 2.0, http://www.apache.org/licenses/