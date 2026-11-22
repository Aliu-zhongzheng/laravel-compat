# PHP 兼容性体检报告

- 扫描目录：`./example-laravel-app`
- 检测目标：PHP 8.0（PHPCompatibility 静态检查，只覆盖 PHP 语言层）
- 扫描文件：264 个（示例项目）
- 命中条数：3 条（error 0 / warning 3）
- 生成时间：2026-11-22
- 覆盖范围说明：框架层（Laravel 各版本移除的门面/方法/配置项）未覆盖，本报告只反映 PHP 语言层检测结果。

| # | 文件:行 | 类型 | 规则 | 检测到的问题 |
| --- | --- | --- | --- | --- |
| 1 | `app/Http/Controllers/Admin/System/DictController.php:84` | WARNING | `PHPCompatibility.ParameterValues.RemovedImplodeFlexibleParamOrder.Deprecated` | Passing the $glue and $pieces parameters in reverse order to implode has been deprecated since PHP 7.4; $glue should be the first parameter and $pieces the second |
| 2 | `app/Http/Controllers/Admin/System/DictController.php:113` | WARNING | `PHPCompatibility.ParameterValues.RemovedImplodeFlexibleParamOrder.Deprecated` | Passing the $glue and $pieces parameters in reverse order to implode has been deprecated since PHP 7.4; $glue should be the first parameter and $pieces the second |
| 3 | `routes/console.php:18` | WARNING | `PHPCompatibility.FunctionDeclarations.NewClosure.ThisFoundOutsideClass` | Closures / anonymous functions only have access to $this if used within a class or when bound to an object using bindTo(). Please verify. |

共 3 条。措辞只陈述检测结果，不代表必须修改；是否影响取决于实际调用路径。
