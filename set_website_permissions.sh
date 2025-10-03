#!/bin/bash
# set_website_permissions.sh - 优化的网站权限设置脚本
# 版本: 2.0
# 作者: 针对 lryblog.com 项目优化

set -e  # 遇到错误立即退出

# 配置变量
PROJECTS_DIR="/home/lirongyaoper/Projects"
CURRENT_USER="lirongyaoper"
WEB_USER="www-data"
WEB_GROUP="www-data"
BACKUP_DIR="/tmp/permissions_backups"

# 创建备份目录
mkdir -p "$BACKUP_DIR"

# 日志函数
log_info() {
    echo "[INFO] $(date '+%Y-%m-%d %H:%M:%S') - $1"
}

log_error() {
    echo "[ERROR] $(date '+%Y-%m-%d %H:%M:%S') - $1" >&2
}

log_warn() {
    echo "[WARN] $(date '+%Y-%m-%d %H:%M:%S') - $1"
}

# 错误处理函数
handle_error() {
    local exit_code=$?
    log_error "脚本执行失败，退出码: $exit_code"
    exit $exit_code
}

# 设置错误处理
trap handle_error ERR

# 检查必要的命令是否存在
check_dependencies() {
    local commands=("sudo" "find" "stat" "chmod" "chown")
    for cmd in "${commands[@]}"; do
        if ! command -v "$cmd" &> /dev/null; then
            log_error "缺少必要命令: $cmd"
            exit 1
        fi
    done
}

# 检查用户是否存在
check_user_exists() {
    if ! id "$CURRENT_USER" &> /dev/null; then
        log_error "用户 $CURRENT_USER 不存在"
        exit 1
    fi
    
    if ! id "$WEB_USER" &> /dev/null; then
        log_error "用户 $WEB_USER 不存在"
        exit 1
    fi
}

# 创建权限备份
create_backup() {
    local site_path="$1"
    local site_name="$2"
    local backup_file="$BACKUP_DIR/permissions_backup_${site_name}_$(date +%Y%m%d_%H%M%S).txt"
    
    log_info "为 $site_name 创建权限备份: $backup_file"
    
    if find "$site_path" -exec stat -c "%a %U:%G %n" {} \; > "$backup_file" 2>/dev/null; then
        log_info "权限备份已保存到: $backup_file"
        return 0
    else
        log_warn "无法创建权限备份: $backup_file"
        return 1
    fi
}

# 设置基础目录权限
set_base_permissions() {
    local site_path="$1"
    local site_name="$2"
    
    log_info "设置 $site_name 的基础权限"
    
    # 设置站点根目录所有者和组
    if ! sudo chown -R "$CURRENT_USER:$WEB_GROUP" "$site_path"; then
        log_error "无法设置 $site_name 的所有者权限"
        return 1
    fi
    
    # 设置站点根目录和文件的默认权限 (750/640 - 更安全)
    if ! sudo find "$site_path" -type d -exec chmod 750 {} \; 2>/dev/null; then
        log_warn "设置目录权限时遇到问题，继续执行..."
    fi
    
    if ! sudo find "$site_path" -type f -exec chmod 640 {} \; 2>/dev/null; then
        log_warn "设置文件权限时遇到问题，继续执行..."
    fi
    
    log_info "基础权限设置完成"
    return 0
}

# 处理特殊目录
process_special_directories() {
    local site_path="$1"
    local site_name="$2"
    
    # 定义需要特殊权限的目录及其权限
    # 格式: "目录名:目录权限:文件权限:所有者:组"
    declare -A SPECIAL_DIRS=(
        ["logs"]="750:640:$WEB_USER:$WEB_GROUP"
        ["cache"]="770:660:$WEB_USER:$WEB_GROUP"
        ["uploads"]="770:660:$WEB_USER:$WEB_GROUP"
        ["tmp"]="770:660:$WEB_USER:$WEB_GROUP"
        ["storage"]="770:660:$WEB_USER:$WEB_GROUP"
        ["sessions"]="770:660:$WEB_USER:$WEB_GROUP"
        # 针对 lryblog.com 项目的特殊目录
        ["common/data"]="770:660:$WEB_USER:$WEB_GROUP"
        ["common/static/plugin/ueditor/php/upload"]="770:660:$WEB_USER:$WEB_GROUP"
        ["common/static/plugin/ueditor/php/uploadfile"]="770:660:$WEB_USER:$WEB_GROUP"
        ["common/static/plugin/ueditor/php/uploadimage"]="770:660:$WEB_USER:$WEB_GROUP"
        ["common/static/plugin/ueditor/php/uploadvideo"]="770:660:$WEB_USER:$WEB_GROUP"
        ["common/static/plugin/ueditor/php/uploadfile"]="770:660:$WEB_USER:$WEB_GROUP"
    )
    
    log_info "处理 $site_name 的特殊目录"
    
    for special_dir in "${!SPECIAL_DIRS[@]}"; do
        special_dir_path="$site_path/$special_dir"
        
        if [ -d "$special_dir_path" ]; then
            # 解析特殊目录配置
            IFS=':' read -r dir_perm file_perm special_owner special_group <<< "${SPECIAL_DIRS[$special_dir]}"
            
            log_info "  处理 $special_dir 目录 (权限: $dir_perm/$file_perm, 所有者: $special_owner:$special_group)"
            
            # 设置目录所有者
            if ! sudo chown -R "$special_owner:$special_group" "$special_dir_path"; then
                log_warn "无法设置 $special_dir 的所有者权限"
                continue
            fi
            
            # 设置目录权限（递归）
            if ! sudo find "$special_dir_path" -type d -exec chmod "$dir_perm" {} \; 2>/dev/null; then
                log_warn "设置 $special_dir 目录权限时遇到问题"
            fi
            
            # 设置文件权限（递归）
            if ! sudo find "$special_dir_path" -type f -exec chmod "$file_perm" {} \; 2>/dev/null; then
                log_warn "设置 $special_dir 文件权限时遇到问题"
            fi
            
            # 为特殊目录设置 setgid 位，确保新建文件继承组权限
            if ! sudo chmod g+s "$special_dir_path" 2>/dev/null; then
                log_warn "无法为 $special_dir 设置 setgid 位"
            fi
            
            # 对于需要写权限的目录，创建必要的文件
            if [[ "$special_dir" == "logs" ]]; then
                sudo touch "$special_dir_path/access.log" "$special_dir_path/error.log" 2>/dev/null || true
                sudo chown "$WEB_USER:$WEB_GROUP" "$special_dir_path"/*.log 2>/dev/null || true
                sudo chmod 660 "$special_dir_path"/*.log 2>/dev/null || true
            fi
            
            # 对于 cache 目录，确保有适当的权限
            if [[ "$special_dir" == "cache" ]]; then
                # 创建缓存目录的索引文件（如果需要）
                sudo touch "$special_dir_path/.gitkeep" 2>/dev/null || true
                sudo chown "$WEB_USER:$WEB_GROUP" "$special_dir_path/.gitkeep" 2>/dev/null || true
            fi
            
            log_info "  $special_dir 目录处理完成"
        else
            log_info "  $special_dir 目录不存在，跳过"
        fi
    done
}

# 动态检测其他可能需要写权限的目录
detect_write_directories() {
    local site_path="$1"
    local site_name="$2"
    
    log_info "动态检测 $site_name 的其他写权限目录"
    
    # 更精确的目录匹配模式
    local patterns=(
        "-name logs"
        "-name cache" 
        "-name uploads"
        "-name tmp"
        "-name storage"
        "-name sessions"
        "-path */data"
        "-path */upload"
        "-path */uploadfile"
        "-path */uploadimage"
        "-path */uploadvideo"
        "-path */temp"
        "-path */temporary"
    )
    
    # 构建 find 命令
    local find_cmd="find \"$site_path\" -type d"
    for pattern in "${patterns[@]}"; do
        find_cmd="$find_cmd -o $pattern"
    done
    
    # 执行查找并处理结果
    eval "$find_cmd" | while read -r found_dir; do
        # 跳过已经处理过的目录和根目录
        dir_name=$(basename "$found_dir")
        if [[ "$found_dir" != "$site_path" ]] && [[ ! -d "$site_path/$dir_name" ]] || [[ "$found_dir" == "$site_path" ]]; then
            continue
        fi
        
        # 检查是否已经在特殊目录列表中处理过
        local is_special=false
        for special_dir in "${!SPECIAL_DIRS[@]}"; do
            if [[ "$found_dir" == "$site_path/$special_dir" ]]; then
                is_special=true
                break
            fi
        done
        
        if [[ "$is_special" == false ]]; then
            log_info "    检测到可能需要写权限的目录: $found_dir"
            if ! sudo chown -R "$WEB_USER:$WEB_GROUP" "$found_dir"; then
                log_warn "无法设置 $found_dir 的所有者权限"
                continue
            fi
            
            if ! sudo find "$found_dir" -type d -exec chmod 770 {} \; 2>/dev/null; then
                log_warn "设置 $found_dir 目录权限时遇到问题"
            fi
            
            if ! sudo find "$found_dir" -type f -exec chmod 660 {} \; 2>/dev/null; then
                log_warn "设置 $found_dir 文件权限时遇到问题"
            fi
        fi
    done
}

# 验证权限设置
verify_permissions() {
    local site_path="$1"
    local site_name="$2"
    
    log_info "验证 $site_name 的权限设置"
    
    # 检查关键目录的权限
    local critical_dirs=("logs" "cache" "uploads" "tmp" "storage" "sessions")
    for dir in "${critical_dirs[@]}"; do
        if [ -d "$site_path/$dir" ]; then
            local owner=$(stat -c "%U:%G" "$site_path/$dir" 2>/dev/null || echo "N/A")
            local perm=$(stat -c "%a" "$site_path/$dir" 2>/dev/null || echo "N/A")
            log_info "  $dir: 所有者=$owner, 权限=$perm"
        fi
    done
    
    # 检查 PHP 文件的可执行权限
    local php_files=$(find "$site_path" -name "*.php" -type f | head -5)
    if [ -n "$php_files" ]; then
        log_info "  PHP 文件权限检查:"
        echo "$php_files" | while read -r php_file; do
            local perm=$(stat -c "%a" "$php_file" 2>/dev/null || echo "N/A")
            log_info "    $(basename "$php_file"): $perm"
        done
    fi
}

# 显示权限摘要
show_permissions_summary() {
    local sites=("$@")
    
    log_info "=== .com 站点权限设置完成 ==="
    echo "处理的站点列表和特殊目录状态:"
    
    for site in "${sites[@]}"; do
        site_path="$PROJECTS_DIR/$site"
        if [ -d "$site_path" ]; then
            local owner=$(stat -c "%U:%G" "$site_path" 2>/dev/null || echo "N/A")
            local permissions=$(stat -c "%a" "$site_path" 2>/dev/null || echo "N/A")
            echo "  $site -> 所有者: $owner, 权限: $permissions"
            
            # 显示特殊目录状态
            local special_dirs=("logs" "cache" "uploads" "tmp" "storage" "sessions" "common/data")
            for special_dir in "${special_dirs[@]}"; do
                if [ -d "$site_path/$special_dir" ]; then
                    local special_owner=$(stat -c "%U:%G" "$site_path/$special_dir" 2>/dev/null || echo "N/A")
                    local special_perm=$(stat -c "%a" "$site_path/$special_dir" 2>/dev/null || echo "N/A")
                    echo "    └─ $special_dir: $special_owner, $special_perm"
                fi
            done
        fi
    done
    
    echo ""
    echo "权限说明:"
    echo "  - 普通目录: 750 (drwxr-x---)"
    echo "  - 普通文件: 640 (-rw-r-----)"
    echo "  - 特殊目录(cache/uploads等): 770 (drwxrwx---)"
    echo "  - 特殊文件: 660 (-rw-rw----)"
    echo "  - 日志文件: 660 (-rw-rw----)"
    echo ""
    echo "备份文件位置: $BACKUP_DIR"
}

# 主函数
main() {
    log_info "开始执行网站权限设置脚本"
    
    # 检查依赖
    check_dependencies
    check_user_exists
    
    # 设置基础目录权限
    if ! sudo chmod 755 "$PROJECTS_DIR"; then
        log_error "无法设置 $PROJECTS_DIR 的权限"
        exit 1
    fi
    
    # 获取 Projects 目录下所有以 .com 结尾的目录
    log_info "扫描 $PROJECTS_DIR 目录下的 .com 站点..."
    local sites=($(find "$PROJECTS_DIR" -maxdepth 1 -type d -name "*.com" ! -name ".*" -exec basename {} \;))
    
    if [ ${#sites[@]} -eq 0 ]; then
        log_error "在 $PROJECTS_DIR 目录下未找到任何 .com 站点"
        exit 1
    fi
    
    log_info "找到以下 .com 站点: ${sites[*]}"
    echo ""
    
    # 处理每个站点
    for site in "${sites[@]}"; do
        site_path="$PROJECTS_DIR/$site"
        
        log_info "正在处理站点: $site"
        
        # 创建备份
        create_backup "$site_path" "$site"
        
        # 设置基础权限
        if ! set_base_permissions "$site_path" "$site"; then
            log_error "设置 $site 基础权限失败，跳过此站点"
            continue
        fi
        
        # 处理特殊目录
        process_special_directories "$site_path" "$site"
        
        # 动态检测其他目录
        detect_write_directories "$site_path" "$site"
        
        # 验证权限
        verify_permissions "$site_path" "$site"
        
        log_info "完成处理: $site"
        echo ""
    done
    
    # 显示摘要
    show_permissions_summary "${sites[@]}"
    
    log_info "所有 .com 站点权限设置完成！"
}

# 执行主函数
main "$@"
