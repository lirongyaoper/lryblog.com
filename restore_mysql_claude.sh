#!/bin/bash

# MySQL 数据库恢复脚本
# 适用于 Ubuntu 24.04 和 MySQL 8.0
# 支持压缩(.sql.gz)和非压缩(.sql)数据库备份文件恢复
# 使用方法: ./restore_mysql.sh <备份文件> [目标数据库名] [选项]

# 检查必需的命令依赖
check_dependencies() {
    local missing_commands=()
    local required_commands=("mysql" "gzip" "gunzip" "du" "grep" "sed" "mktemp" "systemctl" "date" "file")
    
    for cmd in "${required_commands[@]}"; do
        if ! command -v "$cmd" &> /dev/null; then
            missing_commands+=("$cmd")
        fi
    done
    
    if [ ${#missing_commands[@]} -gt 0 ]; then
        echo "错误: 缺少必需的命令: ${missing_commands[*]}"
        echo "请安装缺失的工具后再运行此脚本"
        exit 1
    fi
    
    # 检查可选命令 pv（进度显示）
    if ! command -v pv &> /dev/null; then
        echo "提示: 未安装 pv 命令，将不显示恢复进度（可选功能）"
        echo "安装方法: sudo apt install pv"
    fi
}

# 执行依赖检查
check_dependencies

# 配置参数
LOG_DIR="/home/lirongyaoper/Projects/webconf/mysql/log"
LOG_FILE="${LOG_DIR}/mysql_restore.log"  # 日志文件
MYSQL_CONFIG="/home/lirongyaoper/.mysql.cnf"       # MySQL 配置文件路径

# 默认选项
FORCE_RESTORE=false  # 是否强制恢复（不询问确认）
DROP_DATABASE=false  # 是否先删除数据库

# 创建日志目录
mkdir -p "$LOG_DIR"

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 日志函数 - 分离屏幕输出和文件输出
log() {
    local message="$1"
    local plain_message=$(echo -e "$message" | sed 's/\x1b\[[0-9;]*m//g')
    echo -e "[$(date +'%Y-%m-%d %H:%M:%S')] $message"
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $plain_message" >> "$LOG_FILE"
}

log_success() {
    log "${GREEN}✓ $1${NC}"
}

log_warning() {
    log "${YELLOW}⚠ $1${NC}"
}

log_error() {
    log "${RED}✗ $1${NC}"
}

log_info() {
    log "${BLUE}ℹ $1${NC}"
}

# 显示帮助信息
show_help() {
    cat << EOF
MySQL 数据库恢复脚本

用法: $0 <备份文件> [目标数据库名] [选项]

参数:
  备份文件          要恢复的备份文件路径
  目标数据库名      可选，不指定则从备份文件名推断

选项:
  -h, --help        显示此帮助信息
  -f, --force       强制恢复，不询问确认
  -d, --drop        恢复前先删除目标数据库（谨慎使用）

支持的文件格式:
  - 压缩文件: .sql.gz
  - 非压缩文件: .sql

示例:
  $0 mydb_20240101_120000.sql.gz
  $0 mydb_20240101_120000.sql.gz target_db
  $0 mydb_backup.sql.gz target_db --force
  $0 mydb_backup.sql.gz target_db --drop --force

配置文件: $MYSQL_CONFIG
日志文件: $LOG_FILE
EOF
}

# 检查文件类型函数
check_file_type() {
    local file="$1"
    
    # 检查文件扩展名
    if [[ "$file" == *.sql.gz ]]; then
        echo "compressed"
    elif [[ "$file" == *.sql ]]; then
        echo "uncompressed"
    else
        # 通过file命令检查文件内容类型
        local file_type=$(file -b "$file" 2>/dev/null)
        if [[ "$file_type" == *"gzip compressed"* ]]; then
            echo "compressed"
        elif [[ "$file_type" == *"ASCII text"* ]] || [[ "$file_type" == *"UTF-8"* ]]; then
            echo "uncompressed"
        else
            echo "unknown"
        fi
    fi
}

# 从备份文件名提取数据库名
extract_db_name() {
    local file="$1"
    local basename_file=$(basename "$file")
    
    # 移除扩展名
    basename_file="${basename_file%.sql.gz}"
    basename_file="${basename_file%.sql}"
    
    # 尝试移除时间戳部分 (假设格式为 dbname_YYYYMMDD_HHMMSS 或 dbname_YYYYMMDD_HHMMSS_PID)
    # 匹配最后的 _数字_数字 或 _数字_数字_数字 模式
    if [[ "$basename_file" =~ ^(.+)_[0-9]{8}_[0-9]{6}(_[0-9]+)?$ ]]; then
        echo "${BASH_REMATCH[1]}"
    else
        # 如果没有匹配到时间戳格式，返回整个文件名
        echo "$basename_file"
    fi
}

# 解析命令行参数
BACKUP_FILE=""
TARGET_DB=""

while [[ $# -gt 0 ]]; do
    case $1 in
        -h|--help)
            show_help
            exit 0
            ;;
        -f|--force)
            FORCE_RESTORE=true
            shift
            ;;
        -d|--drop)
            DROP_DATABASE=true
            shift
            ;;
        -*)
            log_error "未知选项: $1"
            echo "使用 $0 --help 查看帮助信息"
            exit 1
            ;;
        *)
            if [[ -z "$BACKUP_FILE" ]]; then
                BACKUP_FILE="$1"
            elif [[ -z "$TARGET_DB" ]]; then
                TARGET_DB="$1"
            else
                log_error "参数过多"
                echo "使用 $0 --help 查看帮助信息"
                exit 1
            fi
            shift
            ;;
    esac
done

# 检查是否提供了备份文件
if [ -z "$BACKUP_FILE" ]; then
    log_error "缺少备份文件参数"
    echo "使用 $0 --help 查看帮助信息"
    exit 1
fi

# 检查备份文件是否存在
if [ ! -f "$BACKUP_FILE" ]; then
    log_error "备份文件 $BACKUP_FILE 不存在!"
    exit 1
fi

# 检查文件类型
FILE_TYPE=$(check_file_type "$BACKUP_FILE")
if [ "$FILE_TYPE" == "unknown" ]; then
    log_error "无法识别备份文件格式! 支持的格式: .sql 或 .sql.gz"
    exit 1
fi

log_info "检测到备份文件类型: $FILE_TYPE"

# 检查 MySQL 服务是否运行
if ! systemctl is-active --quiet mysql 2>/dev/null; then
    log_error "MySQL 服务未运行!"
    log_info "请启动 MySQL 服务: sudo systemctl start mysql"
    exit 1
fi

# 检查 MySQL 配置文件
if [ ! -f "$MYSQL_CONFIG" ]; then
    log_error "MySQL 配置文件 $MYSQL_CONFIG 不存在!"
    log_info "请创建配置文件，格式如下:"
    echo "[client]"
    echo "user=root"
    echo "password=your_password"
    echo "host=localhost"
    exit 1
fi

# 检查配置文件权限
CONFIG_PERMS=$(stat -c %a "$MYSQL_CONFIG" 2>/dev/null)
if [ "$CONFIG_PERMS" != "600" ] && [ "$CONFIG_PERMS" != "400" ]; then
    log_warning "MySQL 配置文件权限不是 600，建议执行: chmod 600 $MYSQL_CONFIG"
fi

# 测试 MySQL 连接
if ! mysql --defaults-file="$MYSQL_CONFIG" -e "SELECT 1" >/dev/null 2>&1; then
    log_error "无法连接到 MySQL 数据库！请检查配置文件中的用户名和密码"
    exit 1
fi

# 从备份文件名提取数据库名 (如果没有指定目标数据库)
if [ -z "$TARGET_DB" ]; then
    TARGET_DB=$(extract_db_name "$BACKUP_FILE")
    log_info "未指定目标数据库，从备份文件名推断为: $TARGET_DB"
fi

# 验证数据库名称格式
if [[ ! "$TARGET_DB" =~ ^[a-zA-Z0-9_]+$ ]]; then
    log_error "数据库名称 '$TARGET_DB' 包含无效字符! 只允许字母、数字和下划线"
    exit 1
fi

# 检查数据库是否存在
DB_EXISTS=$(mysql --defaults-file="$MYSQL_CONFIG" -e "SHOW DATABASES LIKE '$TARGET_DB';" 2>/dev/null | grep -c "$TARGET_DB")

log_info "开始恢复数据库 $TARGET_DB 从备份文件 $BACKUP_FILE"

# 临时解压目录和错误日志
TEMP_DIR=$(mktemp -d)
ERROR_LOG=$(mktemp)
trap 'rm -rf "$TEMP_DIR" "$ERROR_LOG"' EXIT INT TERM

# 根据文件类型处理备份文件
log_info "处理备份文件..."
if [ "$FILE_TYPE" == "compressed" ]; then
    log_info "解压压缩备份文件..."
    
    # 先验证压缩文件完整性
    if ! gzip -t "$BACKUP_FILE" 2>/dev/null; then
        log_error "备份文件损坏，无法解压!"
        exit 1
    fi
    
    if ! gunzip -c "$BACKUP_FILE" > "$TEMP_DIR/restore.sql" 2>/dev/null; then
        log_error "解压备份文件失败!"
        exit 1
    fi
elif [ "$FILE_TYPE" == "uncompressed" ]; then
    log_info "使用非压缩备份文件..."
    if ! cp "$BACKUP_FILE" "$TEMP_DIR/restore.sql" 2>/dev/null; then
        log_error "复制备份文件失败!"
        exit 1
    fi
fi

# 检查处理后的SQL文件
if [ ! -s "$TEMP_DIR/restore.sql" ]; then
    log_error "处理后的SQL文件为空!"
    exit 1
fi

# 获取SQL文件大小信息
SQL_SIZE=$(du -h "$TEMP_DIR/restore.sql" | cut -f1)
SQL_SIZE_BYTES=$(du -b "$TEMP_DIR/restore.sql" | cut -f1)
log_info "SQL文件大小: $SQL_SIZE"

# 验证SQL文件内容
if ! grep -qiE "CREATE|INSERT|DROP|USE" "$TEMP_DIR/restore.sql"; then
    log_error "SQL文件可能不包含有效的数据库备份内容"
    if [ "$FORCE_RESTORE" = false ]; then
        read -p "确认继续恢复操作吗? [y/n] " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            log_info "用户取消恢复操作"
            exit 0
        fi
    else
        log_warning "强制模式：跳过SQL内容验证警告"
    fi
fi

# 处理数据库存在的情况
if [ "$DB_EXISTS" -gt 0 ]; then
    if [ "$DROP_DATABASE" = true ]; then
        log_warning "将删除已存在的数据库 $TARGET_DB"
        if [ "$FORCE_RESTORE" = false ]; then
            read -p "确认要删除数据库 $TARGET_DB 吗? [y/n] " -n 1 -r
            echo
            if [[ ! $REPLY =~ ^[Yy]$ ]]; then
                log_info "用户取消恢复操作"
                exit 0
            fi
        fi
        
        log_info "删除数据库 $TARGET_DB..."
        if ! mysql --defaults-file="$MYSQL_CONFIG" -e "DROP DATABASE IF EXISTS \`$TARGET_DB\`;" 2>/dev/null; then
            log_error "删除数据库 $TARGET_DB 失败!"
            exit 1
        fi
        DB_EXISTS=0
    else
        log_warning "数据库 $TARGET_DB 已存在，将覆盖数据!"
        if [ "$FORCE_RESTORE" = false ]; then
            read -p "确认要覆盖数据库 $TARGET_DB 吗? [y/n] " -n 1 -r
            echo
            if [[ ! $REPLY =~ ^[Yy]$ ]]; then
                log_info "用户取消恢复操作"
                exit 0
            fi
        fi
    fi
fi

# 创建数据库 (如果不存在)
if [ "$DB_EXISTS" -eq 0 ]; then
    log_info "数据库 $TARGET_DB 不存在，正在创建..."
    if ! mysql --defaults-file="$MYSQL_CONFIG" -e "CREATE DATABASE \`$TARGET_DB\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null; then
        log_error "创建数据库 $TARGET_DB 失败!"
        exit 1
    fi
    log_success "数据库 $TARGET_DB 创建成功"
fi

# 恢复数据库
log_info "正在恢复数据库 $TARGET_DB..."
start_time=$(date +%s)

# 使用管道方式恢复，提供更好的错误处理
# 添加 pv 进度显示（如果可用）
if command -v pv &> /dev/null && [ -t 1 ]; then
    log_info "使用进度显示..."
    if ! pv "$TEMP_DIR/restore.sql" | mysql --defaults-file="$MYSQL_CONFIG" "$TARGET_DB" 2>"$ERROR_LOG"; then
        log_error "恢复数据库 $TARGET_DB 失败!"
        if [ -s "$ERROR_LOG" ]; then
            log_error "错误信息: $(cat "$ERROR_LOG")"
        fi
        exit 1
    fi
else
    if ! mysql --defaults-file="$MYSQL_CONFIG" "$TARGET_DB" < "$TEMP_DIR/restore.sql" 2>"$ERROR_LOG"; then
        log_error "恢复数据库 $TARGET_DB 失败!"
        if [ -s "$ERROR_LOG" ]; then
            log_error "错误信息: $(cat "$ERROR_LOG")"
        fi
        exit 1
    fi
fi

# 检查是否有警告信息
if [ -s "$ERROR_LOG" ]; then
    log_warning "恢复过程中有警告: $(cat "$ERROR_LOG")"
fi

end_time=$(date +%s)
duration=$((end_time - start_time))

# 格式化耗时显示
if [ $duration -ge 3600 ]; then
    duration_str=$(printf "%d小时%d分%d秒" $((duration/3600)) $((duration%3600/60)) $((duration%60)))
elif [ $duration -ge 60 ]; then
    duration_str=$(printf "%d分%d秒" $((duration/60)) $((duration%60)))
else
    duration_str="${duration}秒"
fi

# 获取恢复后的统计信息
TABLE_COUNT=$(mysql --defaults-file="$MYSQL_CONFIG" -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$TARGET_DB';" 2>/dev/null | tail -n 1)

# 获取数据库大小
DB_SIZE=$(mysql --defaults-file="$MYSQL_CONFIG" -e "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size_MB' FROM information_schema.tables WHERE table_schema='$TARGET_DB';" 2>/dev/null | tail -n 1)

log_success "数据库 $TARGET_DB 恢复成功!"
log_info "恢复统计信息:"
log_info "  - 恢复耗时: $duration_str"
log_info "  - 备份文件大小: $(du -h "$BACKUP_FILE" | cut -f1)"
log_info "  - SQL文件大小: $SQL_SIZE"
log_info "  - 恢复后表数量: ${TABLE_COUNT:-0}"
log_info "  - 数据库大小: ${DB_SIZE:-0} MB"

log_success "恢复操作完成"
exit 0

