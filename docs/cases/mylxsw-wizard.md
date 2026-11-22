# mylxsw/wizard —— Laravel 5.8，2267 star

- 仓库：https://github.com/mylxsw/wizard
- `laravel/framework`：`5.8.*`，最后提交 2023-03-22
- 扫描：194 个 PHP 文件，目标 PHP 8.0，耗时约 2.2s
- 命中：1 条，`routes/console.php:17`

**结论（约 200 字）**

wizard 是三个案例里代码量最大的一个：194 个 PHP 文件、Laravel 5.8、仍有 2267 star，但已近三年没有新提交。按 PHP 8.0 扫描，只命中 1 条，且这条是工具盲区——`routes/console.php` 里的闭包使用 `$this`，Laravel 的 `Artisan::command` 会把闭包绑定到命令实例，静态检查识别不到这层绑定，属于误报。

也就是说，在这个项目的语言层没有找到 PHP 8.0 会直接打断代码的写法：没有 `each()`、`money_format()`、PHP4 式构造器这类遗留。真正的升级风险不在语言层，而在 Laravel 5.8 到 8.x 的框架层破坏性变更——门面别名、契约签名、调度器方法，这些本工具不覆盖，只能对照官方 upgrade guide 逐条走。这份报告能给的确定结论是：语言层不是阻塞点。
