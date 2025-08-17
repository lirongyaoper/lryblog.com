<?php
/**
 * extention.func.php   用户自定义函数库
 *
 * @author           李荣耀  
 * @license          http://www.lryper.com
 * @lastmodify       2018-03-18
 */

/**
 * 美化打印数组（支持多维数组）
 * @param mixed $data 要打印的数据
 * @param int $indent 当前缩进级别（内部使用，无需手动传参）
 */
/**
 * 打印表格化数组（特别适合结构相同的多维数组）
 * @param array $data 要打印的数组
 */
/**
 * 递归打印多维数组（HTML版，适合浏览器）
 * @param mixed $data 要打印的数据
 * @param int $level 当前递归层级（内部使用）
 * @param bool $isLast 当前元素是否是父数组的最后一项（内部使用）
 */
function Printarraylry($data, $level = 0, $isLast = true, $maxDepth = 10) {
    static $indent = 0;
    
    if ($level > $maxDepth) {
        echo "<span class='palry-max-depth'>[max depth reached]</span>";
        return;
    }

    // 使用输出缓冲减少多次echo
    ob_start();
    
    $type = gettype($data);
    switch ($type) {
        case 'array':
            $count = count($data);
            echo "<div class='palry-container'>";
            echo "<span class='palry-type-array'>Array(<span class='palry-meta'>$count</span>)</span> [";

            if ($count === 0) {
                echo " <span class='palry-null'>empty</span> ";
            } else {
                echo "<ul class='palry-list'>";
                $i = 0;
                foreach ($data as $key => $value) {
                    $i++;
                    echo "<li>";
                    echo "<span class='palry-key'>" . htmlspecialchars($key, ENT_QUOTES) . "</span> => ";
                    Printarraylry($value, $level + 1, $i === $count, $maxDepth);
                    echo "</li>";
                }
                echo "</ul>";
            }

            echo "]</div>";
            break;

        case 'string':
            echo "<span class='palry-string'>'" . htmlspecialchars($data, ENT_QUOTES) . "'</span>";
            break;

        case 'integer':
        case 'double':
            echo "<span class='palry-number'>$data</span>";
            break;

        case 'boolean':
            $val = $data ? 'true' : 'false';
            echo "<span class='palry-boolean'>$val</span>";
            break;

        case 'NULL':
            echo "<span class='palry-null'>null</span>";
            break;
            
        case 'resource':
            echo "<span class='palry-resource'>" . get_resource_type($data) . "</span>";
            break;
            
        case 'object':
            echo "<span class='palry-object'>" . get_class($data) . "</span>";
            break;

        default:
            echo "<span class='palry-unknown'>(unhandled type: $type)</span>";
    }
    
    return ob_get_clean();
}

function Palry($data, $maxDepth = 10) {
    // 添加CSS类替代内联样式
    $css = "
    <style>
    .palry-wrapper {
        background: #f8f9fa; 
        border: 1px solid #ddd; 
        padding: 15px; 
        border-radius: 4px;
        font-family: monospace;
    }
    .palry-container {
        margin-left: 15px;
    }
    .palry-list {
        list-style-type: none; 
        padding-left: 15px; 
        margin: 0;
    }
    .palry-type-array { color: #d63384; }
    .palry-key { color: #0066cc; font-weight: bold; }
    .palry-string { color: #28a745; }
    .palry-number { color: #fd7e14; }
    .palry-boolean { color: #6610f2; }
    .palry-null, .palry-meta { color: #6c757d; }
    .palry-resource { color: #17a2b8; }
    .palry-object { color: #6f42c1; }
    .palry-unknown { color: #dc3545; }
    .palry-max-depth { color: #ffc107; }
    </style>
    ";
    
    echo "<div class='palry-wrapper'>";
    echo $css;
    echo Printarraylry($data, 0, true, $maxDepth);
    echo "</div>";
}