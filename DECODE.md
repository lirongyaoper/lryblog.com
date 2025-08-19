我先在你的项目里创建一个小工具脚本，用来按步骤静态解码这些 pack/hex 混淆；创建后我会直接在目标文件上运行它并输出一个已解码副本。
Ran tool

我先检查项目根目录下是否已有 `tools` 目录可用于放置解码脚本，然后创建脚本文件。
Ran tool

让我先创建一个解混淆工具脚本到项目的 tools 目录，然后在疑似被混淆的控制器文件上运行它并生成一个已解码的副本。接下来我会先确认哪个文件包含你给出的混淆内容，并直接解码它。
Ran tool
Ran tool

### 目标
把你贴的这段 PHP 混淆代码，静态复原成可读源码（不执行其逻辑），一步一步来。

### 步骤 0：准备与备份
- 建议在一个独立目录操作，避免误执行。
- 把混淆源码保存为一个文件，例如：
  - 输入文件：`/home/lirongyao0916/Projects/test.com/scratch/obf_index.php`

### 步骤 1：创建解混淆脚本
在你的项目里放一个专用脚本（仅做静态替换、不执行业务逻辑）：
- 脚本路径：`/home/lirongyao0916/Projects/test.com/tools/deobfuscate.php`
- 脚本内容如下，直接复制粘贴保存：

```php
<?php
/**
 * Static deobfuscator for code using $GLOBALS[...] + pack('H*', ...) dictionary patterns.
 * Usage:
 *   php deobfuscate.php -i /abs/path/to/input.php -o /abs/path/to/output.php [--decode-base64]
 */
ini_set('display_errors', 'stderr');
error_reporting(E_ALL);

$options = getopt('i:o:', ['decode-base64']);
if (!isset($options['i']) || !isset($options['o'])) {
    fwrite(STDERR, "Usage: php deobfuscate.php -i INPUT -o OUTPUT [--decode-base64]\n");
    exit(2);
}
$inputFile = $options['i'];
$outputFile = $options['o'];
$shouldDecodeBase64 = array_key_exists('decode-base64', $options);

if (!is_file($inputFile)) {
    fwrite(STDERR, "Input file not found: {$inputFile}\n");
    exit(2);
}
$source = file_get_contents($inputFile);
if ($source === false) {
    fwrite(STDERR, "Failed to read input file: {$inputFile}\n");
    exit(2);
}

/**
 * 1) Extract the explode(...) payload to build dictionary
 * 支持：$GLOBALS[...] = explode('DELIM','PAYLOAD'); 也支持双引号
 */
$explodeRegex = '~\$GLOBALS\s*\[[^\]]+\]\s*=\s*explode\(\s*(["\'])(.*?)\1\s*,\s*(["\'])(.*?)\3\s*\)\s*;~s';
if (!preg_match($explodeRegex, $source, $m)) {
    // 失败时给出简短诊断：截取包含 $GLOBALS 和 explode 的邻域文本
    $snippet = '';
    if (preg_match('~\$GLOBALS[^\n]{0,200}explode\([^\n]{0,200}~s', $source, $mm)) {
        $snippet = "\nNear: " . substr($mm[0], 0, 200);
    }
    fwrite(STDERR, "Could not locate the obfuscation explode(...) payload in the input file." . $snippet . "\n");
    exit(3);
}
$delimiter = $m[2];   // 分隔符，如 | |@|4
$payload   = $m[4];   // 长串，首个元素通常是 H*

$parts = explode($delimiter, $payload);
if (count($parts) < 2) {
    fwrite(STDERR, "Explode payload seems malformed.\n");
    exit(3);
}
$format = $parts[0];  // 通常是 H*
$decodedTokens = [];
$decodedTokens[0] = $format;
for ($i = 1; $i < count($parts); $i++) {
    $decodedTokens[$i] = @pack($format, $parts[$i]); // hex -> ascii
}

// 2) Helpers
function evaluateArithmeticIndex(string $expr): int {
    $candidate = trim($expr);
    if (!preg_match('/^[0-9\s\-\+*\/()%]+$/', $candidate)) {
        throw new RuntimeException("Disallowed characters in index expression: {$expr}");
    }
    // phpcs:ignore Squiz.PHP.Eval.Discouraged
    $val = eval('return (int)(' . $candidate . ');');
    if (!is_int($val)) {
        throw new RuntimeException("Failed to evaluate index: {$expr}");
    }
    return $val;
}
function phpSingleQuote(string $s): string {
    return "'" . str_replace(["\\", "'"], ["\\\\", "\\'"], $s) . "'";
}

// 3) Replace dynamic pack(...) to literals
$replacePack = function (array $mm) use ($decodedTokens): string {
    $hexExpr = $mm[2]; // 第二个捕获组是字典索引表达式
    try {
        $hexIdx = evaluateArithmeticIndex($hexExpr);
    } catch (Throwable $e) {
        return $mm[0];
    }
    if (!array_key_exists($hexIdx, $decodedTokens)) {
        return $mm[0];
    }
    $decoded = $decodedTokens[$hexIdx];
    return phpSingleQuote($decoded);
};

$patterns = [
    // call_user_func_array('pack', array($GLOBALS[...][i], $GLOBALS[...][j]))
    "~call_user_func_array\(\s*['\"]pack['\"]\s*,\s*array\(\s*\$GLOBALS\[[^\]]+\]\[\s*([^\]]+)\s*\]\s*,\s*\$GLOBALS\[[^\]]+\]\[\s*([^\]]+)\s*\]\s*\)\s*\)~s",
    // call_user_func('pack', $GLOBALS[...][i], $GLOBALS[...][j])
    "~call_user_func\(\s*['\"]pack['\"]\s*,\s*\$GLOBALS\[[^\]]+\]\[\s*([^\]]+)\s*\]\s*,\s*\$GLOBALS\[[^\]]+\]\[\s*([^\]]+)\s*\]\s*\)~s",
    // pack($GLOBALS[...][i], $GLOBALS[...][j])
    "~pack\(\s*\$GLOBALS\[[^\]]+\]\[\s*([^\]]+)\s*\]\s*,\s*\$GLOBALS\[[^\]]+\]\[\s*([^\]]+)\s*\]\s*\)~s",
];

$replaced = $source;
foreach ($patterns as $p) {
    $replaced = preg_replace_callback($p, $replacePack, $replaced);
}

// 4) Optional: decode base64_decode('...') when literal
if ($shouldDecodeBase64) {
    $replaced = preg_replace_callback(
        "~base64_decode\(\s*(['\"])(.*?)\1\s*\)~s",
        function (array $mm): string {
            $decoded = base64_decode($mm[2], true);
            return $decoded === false ? $mm[0] : phpSingleQuote($decoded);
        },
        $replaced
    );
}

// 5) Write output
if (file_put_contents($outputFile, $replaced) === false) {
    fwrite(STDERR, "Failed to write output file: {$outputFile}\n");
    exit(4);
}
fwrite(STDOUT, "Deobfuscated file written: {$outputFile}\n");
```

### 步骤 2：运行脚本做第一轮还原
- 输出文件：`/home/lirongyao0916/Projects/test.com/scratch/obf_index.deob.php`
- 命令：

```bash
php /home/lirongyao0916/Projects/test.com/tools/deobfuscate.php \
  -i /home/lirongyao0916/Projects/test.com/scratch/obf_index.php \
  -o /home/lirongyao0916/Projects/test.com/scratch/obf_index.deob.php \
  --decode-base64
```

- 这一步会把类似 `pack($GLOBALS[...] , $GLOBALS[...])` 和 `call_user_func('pack', ...)`、`call_user_func_array('pack', ...)` 的“动态解码”全替换成字面量字符串（如 `'defined'`、`'IN_YZMPHP'` 等），并尝试把 `base64_decode('...')` 变成明文。

### 步骤 3：把动态方法/静态方法调用改为直接调用
第一轮结果里还会出现大括号包着的动态标识符，把它们批量替换：
- 查找替换 1（对象方法）：
  - 查找正则：`->\{\s*'([a-zA-Z0-9_]+)'\s*\}\(`
  - 替换为：`->$1(`
- 查找替换 2（静态方法）：
  - 查找正则：`::\{\s*'([a-zA-Z0-9_]+)'\s*\}\(`
  - 替换为：`::$1(`
- 查找替换 3（call_user_func 仍存在的情况）：
  - 查找正则：`call_user_func\(\s*'([a-zA-Z0-9_]+)'\s*,`
  - 替换为：`$1(`

完成后保存为：`/home/lirongyao0916/Projects/test.com/scratch/obf_index.deob.v2.php`

### 步骤 4：去掉 goto 与整理控制流（可选但推荐）
- 多余的 `goto`/label 大多是混淆，可按实际 if/else 逻辑改写成早返回。
- 用格式化工具美化：
```bash
php -l /home/lirongyao0916/Projects/test.com/scratch/obf_index.deob.v2.php
```
- 如需自动格式化，可用 PHP CS Fixer 或 phpfmt（任选其一）。

### 步骤 5：验证关键语义点
打开 v2 文件，检查这些是否已是明文：
- 文件开头是否为：
  - `defined('IN_YZMPHP') || exit('Access Denied');`
- 控制器注册：
  - `yzm_base::load_controller('common', 'admin', 0);`
- 登录逻辑是否可读：
  - `is_username($username)`、`is_password($password)`、`return_json(['status'=>...,'message'=>...])`
- 其它方法：
  - `public_logout()`、`public_home()`、`public_clear_log()`、`public_lock_screen()`、`public_unlock_screen()` 等
- 尾部隐藏文案是否已展开（base64/十六进制中文）

### 步骤 6：抽查计算与手工核验（帮助你理解还原过程）
- 例如混淆里出现：`$GLOBALS[_A_AA_AA_][(-383+25*16-15)]`
  - 计算：25*16=400；-383+400-15=2 → 索引 2
  - `$parts[2]` 为十六进制 `494E5F595A4D504850`，`pack('H*', ...)` → `IN_YZMPHP`
- 再如：`646566696E6564` → `defined`
- 你可以快速试验：
```bash
php -r 'echo pack("H*", "646566696E6564"), PHP_EOL;'   # defined
php -r 'echo pack("H*", "494E5F595A4D504850"), PHP_EOL;' # IN_YZMPHP
```
- 对中文十六进制同理：
```bash
php -r 'echo pack("H*", "E6ADA4E6938DE4BD9CE4BB85E99990E4BA8EE8B685E7BAA7E7AEA1E79086E59198EFBC81"), PHP_EOL;'
```
- 对 base64 文本：
```bash
php -r 'echo base64_decode("PGRpdiBzdHlsZT0iZGlzcGxheTpub25lIj5Qb3dlcmVkIGJ5IDxhIGhyZWY9Imh0dHA6Ly93d3cueXptY21zLmNvbSIgdGFyZ2V0PSJfYmxhbmsiPll6bUNNUzwvYT48L2Rpdj4="), PHP_EOL;'
```

### 步骤 7：收尾
- 全局搜索是否还遗留 `pack($GLOBALS` 或 `call_user_func('pack'`；有则重复步骤 2~3。
- 最终跑一次语法检查：
```bash
php -l /home/lirongyao0916/Projects/test.com/scratch/obf_index.deob.v2.php
```

### 说明
- 你项目中的 `application/admin/controller/index.class.php` 已经是“解密后”的可读版本，上述步骤主要是教你如何把你贴的那段“混淆版”还原到现在仓库里的这种可读形态。
- 全过程只做“静态字符串与索引替换”，不执行业务逻辑，安全。

如果你愿意，我也可以直接在你的工作区创建上述脚本并对你给的混淆文件跑一遍，生成已解码副本。只要告诉我混淆文件的实际保存路径。