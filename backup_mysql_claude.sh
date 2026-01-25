#!/bin/bash

# MySQL 数据库备份脚本 - 增强版（修复版）
# 适用于 Ubuntu 24.04 和 MySQL 8.0
# 导出带有完整字段名的 INSERT 语句
# 使用方法: ./backup_mysql_enhanced.sh [数据库名] [选项]

# 检查必需的命令依赖
check_dependencies() {
    local missing_commands=()
    local required_commands=("mysql" "mysqldump" "gzip" "du" "find" "awk" "sed" "mktemp" "systemctl" "date")
    
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
}

# 执行依赖检查
check_dependencies

# 配置参数
BACKUP_DIR="/home/lirongyaoper/Projects/webconf/mysql/backup"      # 备份文件存储目录
LOG_DIR="/home/lirongyaoper/Projects/webconf/mysql/log"              # 日志目录
MAX_BACKUPS=30                                    # 保留的备份文件数量（按数量保留，不是天数）
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")_$$            # 统一的时间戳，用于同一批次备份（包含进程ID防止冲突）
LOG_FILE="$LOG_DIR/mysql_backup.log"              # 日志文件
MYSQL_CONFIG="/home/lirongyaoper/.mysql.cnf"      # MySQL 配置文件路径

# 默认选项
COMPLETE_INSERT=true                              # 是否使用完整插入语句
COMPRESS_BACKUP=true                              # 是否压缩备份文件
SINGLE_TRANSACTION=true                           # 是否使用单事务
INCLUDE_ROUTINES=true                             # 是否包含存储过程和函数
INCLUDE_TRIGGERS=true                             # 是否包含触发器
EXTENDED_INSERT=false                             # 是否使用扩展插入（多行合并）
LOCK_TABLES=false                                 # 是否锁表
ADD_DROP_TABLE=true                               # 是否添加 DROP TABLE 语句

# 创建目录
mkdir -p "$BACKUP_DIR"
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
MySQL 数据库备份脚本 - 增强版

用法: $0 [数据库名] [选项]

参数:
  数据库名          要备份的数据库名称（不指定则备份所有数据库）

选项:
  -h, --help        显示此帮助信息
  --no-complete     不使用完整插入语句（默认使用）
  --no-compress     不压缩备份文件（默认压缩）
  --extended        使用扩展插入（多行合并，默认单行）
  --no-routines     不包含存储过程和函数（默认包含）
  --no-triggers     不包含触发器（默认包含）
  --lock-tables     锁定表进行备份（默认不锁定）
  --no-drop         不添加 DROP TABLE 语句（默认添加）

示例:
  $0                              # 备份所有数据库
  $0 test                         # 备份 test 数据库
  $0 test --no-compress           # 备份 test 数据库但不压缩
  $0 test --extended              # 使用扩展插入备份 test 数据库
  $0 --help                       # 显示帮助信息

配置文件: $MYSQL_CONFIG
备份目录: $BACKUP_DIR
日志文件: $LOG_FILE
保留数量: 最近 $MAX_BACKUPS 个备份文件
EOF
}

# 解析命令行参数
DATABASE=""
while [[ $# -gt 0 ]]; do
    case $1 in
        -h|--help)
            show_help
            exit 0
            ;;
        --no-complete)
            COMPLETE_INSERT=false
            shift
            ;;
        --no-compress)
            COMPRESS_BACKUP=false
            shift
            ;;
        --extended)
            EXTENDED_INSERT=true
            shift
            ;;
        --no-routines)
            INCLUDE_ROUTINES=false
            shift
            ;;
        --no-triggers)
            INCLUDE_TRIGGERS=false
            shift
            ;;
        --lock-tables)
            LOCK_TABLES=true
            SINGLE_TRANSACTION=false
            shift
            ;;
        --no-drop)
            ADD_DROP_TABLE=false
            shift
            ;;
        -*)
            log_error "未知选项: $1"
            echo "使用 $0 --help 查看帮助信息"
            exit 1
            ;;
        *)
            if [[ -z "$DATABASE" ]]; then
                DATABASE="$1"
            else
                log_error "只能指定一个数据库名"
                exit 1
            fi
            shift
            ;;
    esac
done

# 检查 MySQL 服务是否运行
if ! systemctl is-active --quiet mysql 2>/dev/null; then
    log_error "MySQL 服务未运行!"
    log_info "请启动 MySQL 服务: sudo systemctl start mysql"
    exit 1
fi

# 检查 MySQL 配置文件是否存在
if [ ! -f "$MYSQL_CONFIG" ]; then
    log_error "MySQL 配置文件 $MYSQL_CONFIG 不存在!"
    log_info "请创建配置文件，格式如下:"
    echo -e "${BLUE}[client]"
    echo "user=root"
    echo "password=your_password"
    echo -e "host=localhost${NC}"
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

# 构建 mysqldump 参数
MYSQLDUMP_ARGS="--defaults-file=$MYSQL_CONFIG"

# 基本参数
if [ "$SINGLE_TRANSACTION" = true ]; then
    MYSQLDUMP_ARGS="$MYSQLDUMP_ARGS --single-transaction"
fi

if [ "$INCLUDE_ROUTINES" = true ]; then
    MYSQLDUMP_ARGS="$MYSQLDUMP_ARGS --routines"
fi

if [ "$INCLUDE_TRIGGERS" = true ]; then
    MYSQLDUMP_ARGS="$MYSQLDUMP_ARGS --triggers"
fi

if [ "$EXTENDED_INSERT" = false ]; then
    MYSQLDUMP_ARGS="$MYSQLDUMP_ARGS --skip-extended-insert"
fi

if [ "$COMPLETE_INSERT" = true ]; then
    MYSQLDUMP_ARGS="$MYSQLDUMP_ARGS --complete-insert"
fi

if [ "$LOCK_TABLES" = true ]; then
    MYSQLDUMP_ARGS="$MYSQLDUMP_ARGS --lock-tables"
else
    MYSQLDUMP_ARGS="$MYSQLDUMP_ARGS --skip-lock-tables"
fi

if [ "$ADD_DROP_TABLE" = true ]; then
    MYSQLDUMP_ARGS="$MYSQLDUMP_ARGS --add-drop-table"
fi

# 其他有用的参数
MYSQLDUMP_ARGS="$MYSQLDUMP_ARGS --hex-blob --default-character-set=utf8mb4 --set-gtid-purged=OFF"

# 获取要备份的数据库列表
if [ -z "$DATABASE" ]; then
    # 备份所有数据库
    DATABASES=$(mysql --defaults-file="$MYSQL_CONFIG" -e "SHOW DATABASES;" 2>/dev/null | grep -Ev "(Database|information_schema|performance_schema|mysql|sys)")
    if [ -z "$DATABASES" ]; then
        log_error "未找到可备份的数据库"
        exit 1
    fi
    BACKUP_TYPE="full"
    log_info "准备备份所有数据库"
else
    # 检查指定数据库是否存在
    if ! mysql --defaults-file="$MYSQL_CONFIG" -e "USE \`$DATABASE\`" 2>/dev/null; then
        log_error "数据库 '$DATABASE' 不存在!"
        exit 1
    fi
    DATABASES=$DATABASE
    BACKUP_TYPE="single"
    log_info "准备备份数据库: $DATABASE"
fi

# 显示配置信息
log_info "备份配置:"
log_info "  完整插入语句: $([ "$COMPLETE_INSERT" = true ] && echo "是" || echo "否")"
log_info "  压缩备份: $([ "$COMPRESS_BACKUP" = true ] && echo "是" || echo "否")"
log_info "  扩展插入: $([ "$EXTENDED_INSERT" = true ] && echo "是" || echo "否")"
log_info "  包含存储过程: $([ "$INCLUDE_ROUTINES" = true ] && echo "是" || echo "否")"
log_info "  包含触发器: $([ "$INCLUDE_TRIGGERS" = true ] && echo "是" || echo "否")"
log_info "  单事务模式: $([ "$SINGLE_TRANSACTION" = true ] && echo "是" || echo "否")"

# 开始备份
log_info "开始 MySQL 数据库备份 (类型: $BACKUP_TYPE)"

TOTAL_SIZE=0
SUCCESS_COUNT=0
FAILED_COUNT=0
BACKUP_FILES=()

for DB in $DATABASES; do
    if [ "$COMPRESS_BACKUP" = true ]; then
        BACKUP_FILE="$BACKUP_DIR/${DB}_${TIMESTAMP}.sql.gz"
    else
        BACKUP_FILE="$BACKUP_DIR/${DB}_${TIMESTAMP}.sql"
    fi
    
    log_info "正在备份数据库: $DB"
    
    # 创建临时错误日志文件
    ERROR_LOG=$(mktemp)
    
    # 执行备份命令
    if [ "$COMPRESS_BACKUP" = true ]; then
        if mysqldump $MYSQLDUMP_ARGS "$DB" 2>"$ERROR_LOG" | gzip > "$BACKUP_FILE"; then
            BACKUP_SUCCESS=true
        else
            BACKUP_SUCCESS=false
        fi
    else
        if mysqldump $MYSQLDUMP_ARGS "$DB" > "$BACKUP_FILE" 2>"$ERROR_LOG"; then
            BACKUP_SUCCESS=true
        else
            BACKUP_SUCCESS=false
        fi
    fi
    
    # 检查是否有错误信息
    if [ -s "$ERROR_LOG" ]; then
        ERROR_MSG=$(cat "$ERROR_LOG")
        log_warning "备份过程中有警告: $ERROR_MSG"
    fi
    rm -f "$ERROR_LOG"
    
    if [ "$BACKUP_SUCCESS" = true ] && [ -s "$BACKUP_FILE" ]; then
        FILE_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
        FILE_SIZE_BYTES=$(du -b "$BACKUP_FILE" | cut -f1)
        
        # 验证备份文件
        VALID=true
        if [ "$COMPRESS_BACKUP" = true ]; then
            if ! gzip -t "$BACKUP_FILE" 2>/dev/null; then
                log_error "备份文件 $BACKUP_FILE 压缩格式损坏!"
                VALID=false
            fi
        fi
        
        if [ "$VALID" = true ]; then
            log_success "数据库 $DB 备份完成: $BACKUP_FILE ($FILE_SIZE)"
            TOTAL_SIZE=$((TOTAL_SIZE + FILE_SIZE_BYTES))
            SUCCESS_COUNT=$((SUCCESS_COUNT + 1))
            BACKUP_FILES+=("$BACKUP_FILE")
        else
            log_error "备份文件验证失败，删除损坏的备份: $BACKUP_FILE"
            rm -f "$BACKUP_FILE"
            FAILED_COUNT=$((FAILED_COUNT + 1))
        fi
    else
        log_error "备份数据库 $DB 失败!"
        rm -f "$BACKUP_FILE"
        FAILED_COUNT=$((FAILED_COUNT + 1))
    fi
done

# 清理旧备份 - 按数量保留，不是按天数
if [ -z "$DATABASE" ]; then
    # 全库备份模式：保留所有数据库的最近 N 个完整备份集
    log_info "清理旧备份，保留最近 $MAX_BACKUPS 个完整备份..."
    
    # 获取所有备份的时间戳（去重）
    TIMESTAMPS=$(find "$BACKUP_DIR" -type f \( -name "*.sql.gz" -o -name "*.sql" \) -printf "%f\n" 2>/dev/null | \
                 sed -E 's/^.*_([0-9]{8}_[0-9]{6}_[0-9]+)\.sql(\.gz)?$/\1/' | sort -u | sort -r)
    
    TIMESTAMP_COUNT=$(echo "$TIMESTAMPS" | wc -l)
    
    if [ "$TIMESTAMP_COUNT" -gt "$MAX_BACKUPS" ]; then
        # 获取要删除的时间戳
        OLD_TIMESTAMPS=$(echo "$TIMESTAMPS" | tail -n +$((MAX_BACKUPS + 1)))
        DELETED_COUNT=0
        
        for OLD_TS in $OLD_TIMESTAMPS; do
            DELETED=$(find "$BACKUP_DIR" -type f \( -name "*_${OLD_TS}.sql.gz" -o -name "*_${OLD_TS}.sql" \) -delete -print 2>/dev/null | wc -l)
            DELETED_COUNT=$((DELETED_COUNT + DELETED))
        done
        
        if [ $DELETED_COUNT -gt 0 ]; then
            log_info "已删除 $DELETED_COUNT 个旧备份文件"
        fi
    fi
else
    # 单库备份模式：只保留指定数据库的最近 N 个备份
    log_info "清理数据库 $DATABASE 的旧备份，保留最近 $MAX_BACKUPS 个..."
    
    OLD_BACKUPS=$(find "$BACKUP_DIR" -type f \( -name "${DATABASE}_*.sql.gz" -o -name "${DATABASE}_*.sql" \) -printf "%T@ %p\n" 2>/dev/null | \
                  sort -rn | tail -n +$((MAX_BACKUPS + 1)) | cut -d' ' -f2-)
    
    if [ -n "$OLD_BACKUPS" ]; then
        DELETED_COUNT=$(echo "$OLD_BACKUPS" | wc -l)
        echo "$OLD_BACKUPS" | xargs rm -f
        log_info "已删除 $DELETED_COUNT 个旧备份文件"
    fi
fi

# 显示备份统计
TOTAL_SIZE_MB=$(awk "BEGIN {printf \"%.2f\", $TOTAL_SIZE / 1024 / 1024}")
log_success "MySQL 数据库备份完成!"
log_info "备份统计:"
log_info "  成功: $SUCCESS_COUNT 个数据库"
log_info "  失败: $FAILED_COUNT 个数据库"
log_info "  总大小: ${TOTAL_SIZE_MB}MB"
log_info "  备份目录: $BACKUP_DIR"

if [ $FAILED_COUNT -eq 0 ]; then
    exit 0
else
    exit 1
fi
