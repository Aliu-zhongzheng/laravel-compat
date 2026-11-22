# drehimself/laravel-ecommerce-example —— Laravel 6.0，1018 star

- 仓库：https://github.com/drehimself/laravel-ecommerce-example
- `laravel/framework`：`^6.0`，最后提交 2023-01-03
- 扫描：168 个 PHP 文件，目标 PHP 8.0，耗时约 2.1s
- 命中：3 条（2 条真实阻塞点 + 1 条误报）

| # | 位置 | 规则 | 判定 |
| --- | --- | --- | --- |
| 1 | `app/Product.php:42` | `RemovedFunctions.money_formatDeprecated` | 真问题 |
| 2 | `app/helpers.php:7` | `RemovedFunctions.money_formatDeprecated` | 真问题 |
| 3 | `routes/console.php:17` | `NewClosure.ThisFoundOutsideClass` | 误报（`Artisan::command` 绑定） |

**结论（约 200 字）**

三个案例里唯一有真实语言级阻塞点的一个。168 个 PHP 文件，Laravel 6.0，命中 3 条：两处 `money_format()`（`app/Product.php:42` 与 `app/helpers.php:7`），这个函数在 PHP 7.4 被弃用、8.0 直接移除，升到 8.0 后执行到这两行会立即致命错误，不是警告；第三条是 `routes/console.php` 的闭包 `$this` 误报，属于工具已知盲区。

值得注意的是两处命中都在同一件事上：一个用 `money_format()` 做货币展示的电商项目，换成 `NumberFormatter::formatCurrency()` 是一处改动、两个调用点。除此之外语言层没有别的遗留问题，升级的主要工作量同样落在 Laravel 6 到 8.x 的框架层变更上。
