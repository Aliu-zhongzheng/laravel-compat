<?php
declare(strict_types=1);

namespace LaravelCompat;

final class Report
{
    /** @param array<string,mixed> $json phpcs --report=json 的完整结果 */
    public static function render(array $json, string $target, string $phpVersion, string $generatedAt): string
    {
        $files    = $json['files'] ?? [];
        $messages = [];
        foreach ($files as $path => $info) {
            foreach ($info['messages'] ?? [] as $m) {
                $messages[] = [
                    'file'    => (string) $path,
                    'line'    => (int) ($m['line'] ?? 0),
                    'source'  => (string) ($m['source'] ?? ''),
                    'type'    => (string) ($m['type'] ?? ''),
                    'message' => (string) ($m['message'] ?? ''),
                ];
            }
        }
        usort($messages, static fn (array $a, array $b): int
            => [$a['file'], $a['line']] <=> [$b['file'], $b['line']]);

        $count   = count($messages);
        $errors  = (int) ($json['totals']['errors'] ?? 0);
        $warns   = (int) ($json['totals']['warnings'] ?? 0);
        $fileCnt = count($files);

        $lines = [];
        $lines[] = '# PHP 兼容性体检报告';
        $lines[] = '';
        $lines[] = '- 扫描目录：`' . self::rel($target, $messages) . '`';
        $lines[] = '- 检测目标：PHP ' . $phpVersion . '（PHPCompatibility 静态检查，只覆盖 PHP 语言层）';
        $lines[] = '- 扫描文件：' . $fileCnt . ' 个';
        $lines[] = '- 命中条数：' . $count . ' 条（error ' . $errors . ' / warning ' . $warns . '）';
        $lines[] = '- 生成时间：' . $generatedAt;
        $lines[] = '- 覆盖范围说明：框架层（Laravel 各版本移除的门面/方法/配置项）未覆盖，本报告只反映 PHP 语言层检测结果。';

        // Known-tool-blind-spot marker (conditional)
        $hasConsoleThisFalsePositive = false;
        foreach ($messages as $m) {
            if ($m['source'] === 'PHPCompatibility.FunctionDeclarations.NewClosure.ThisFoundOutsideClass'
                && str_contains(self::rel($target, $m['file']), 'routes/console.php')) {
                $hasConsoleThisFalsePositive = true;
                break;
            }
        }
        if ($hasConsoleThisFalsePositive) {
            $lines[] = '- 已知工具盲区：`routes/console.php` 中的闭包 `$this` 由 Laravel 的 `Artisan::command` 绑定，静态检查无法识别，此条属误报。';
        }

        if ($count === 0) {
            $lines[] = '';
            $lines[] = '未检测到与 PHP ' . $phpVersion . ' 不兼容的写法。注意：本报告只覆盖 PHP 语言层，框架层未覆盖。';
            return implode("\n", $lines) . "\n";
        }

        $lines[] = '';
        $lines[] = '| # | 文件:行 | 类型 | 规则 | 检测到的问题 |';
        $lines[] = '| --- | --- | --- | --- | --- |';
        $i = 0;
        foreach ($messages as $m) {
            $i++;
            $lines[] = '| ' . $i
                . ' | `' . self::rel($target, $m['file']) . ':' . $m['line'] . '`'
                . ' | ' . $m['type']
                . ' | `' . $m['source'] . '`'
                . ' | ' . self::cell($m['message'])
                . ' |';
        }
        $lines[] = '';
        $lines[] = '共 ' . $count . ' 条。措辞只陈述检测结果，不代表必须修改；是否影响取决于实际调用路径。';

        return implode("\n", $lines) . "\n";
    }

    /** 去掉扫描根目录前缀，输出相对路径 */
    private static function rel(string $target, mixed $path): string
    {
        if (is_array($path)) { return $target; }
        $p = str_replace('\\', '/', (string) $path);
        $t = rtrim(str_replace('\\', '/', $target), '/') . '/';
        return str_starts_with($p, $t) ? substr($p, strlen($t)) : $p;
    }

    /** 表格单元格转义：竖线会撑坏表格 */
    private static function cell(string $s): string
    {
        return str_replace(['|', "\r", "\n"], ['\\|', ' ', ' '], $s);
    }
}
