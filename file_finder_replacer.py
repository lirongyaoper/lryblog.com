#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
文件查找和替换工具 - 增强版 v2.0
支持内容、文件名、文件夹名的查找与替换
新增功能：
- 备份功能
- Dry-run 预览模式
- 正则表达式支持
- 更好的错误处理和统计
- 常见目录排除
- 彩色输出
"""

import os
import re
import sys
import argparse
import shutil
from datetime import datetime
from pathlib import Path
from typing import List, Dict, Tuple

# 颜色代码（ANSI）
class Colors:
    RED = '\033[91m'
    GREEN = '\033[92m'
    YELLOW = '\033[93m'
    BLUE = '\033[94m'
    MAGENTA = '\033[95m'
    CYAN = '\033[96m'
    RESET = '\033[0m'
    BOLD = '\033[1m'

class FileFinderReplacer:
    # 默认排除的目录
    DEFAULT_EXCLUDE_DIRS = {
        '.git', '.svn', '.hg', 
        'node_modules', '__pycache__', '.pytest_cache',
        'venv', 'env', '.venv',
        'cache', '.cache',
        'dist', 'build', '.next', '.nuxt',
        'vendor'
    }
    
    def __init__(self, search_dir: str = ".", exclude_dirs: set = None, use_regex: bool = False):
        self.search_dir = Path(search_dir).resolve()
        self.results = []  # 文件内容匹配
        self.name_results = []  # 文件名/文件夹名匹配
        self.exclude_dirs = exclude_dirs if exclude_dirs else self.DEFAULT_EXCLUDE_DIRS
        self.use_regex = use_regex
        self.skipped_files = []  # 跳过的文件（编码错误等）
        self.stats = {
            'files_scanned': 0,
            'dirs_scanned': 0,
            'files_skipped': 0,
            'matches_found': 0
        }

    def _should_match(self, text: str, pattern: str) -> Tuple[bool, List[Tuple[int, int]]]:
        """检查文本是否匹配模式（支持正则和字面文本）"""
        if self.use_regex:
            try:
                matches = list(re.finditer(pattern, text))
                if matches:
                    return True, [(m.start(), m.end()) for m in matches]
                return False, []
            except re.error as e:
                print(f"{Colors.RED}正则表达式错误: {e}{Colors.RESET}")
                sys.exit(1)
        else:
            # 字面文本搜索，找出所有匹配位置
            positions = []
            start = 0
            while True:
                pos = text.find(pattern, start)
                if pos == -1:
                    break
                positions.append((pos, pos + len(pattern)))
                start = pos + 1
            return len(positions) > 0, positions

    def find_files_with_text(self, search_text: str, file_extensions: List[str] = None) -> List[Dict]:
        """查找文件内容中的匹配"""
        self.results = []
        self.skipped_files = []
        self.stats = {'files_scanned': 0, 'dirs_scanned': 0, 'files_skipped': 0, 'matches_found': 0}
        
        if not self.search_dir.exists():
            print(f"{Colors.RED}错误：目录 {self.search_dir} 不存在{Colors.RESET}")
            return []
        
        print(f"{Colors.CYAN}扫描目录: {self.search_dir}{Colors.RESET}")
        
        for root, dirs, files in os.walk(self.search_dir):
            # 排除指定目录
            dirs[:] = [d for d in dirs if d not in self.exclude_dirs and not d.startswith('.')]
            self.stats['dirs_scanned'] += 1
            
            for file in files:
                if file.startswith('.'):
                    continue
                    
                if file_extensions:
                    file_ext = Path(file).suffix.lower()
                    if file_ext not in file_extensions:
                        continue
                
                file_path = Path(root) / file
                self.stats['files_scanned'] += 1
                
                try:
                    with open(file_path, 'r', encoding='utf-8') as f:
                        lines = f.readlines()
                    
                    matches = []
                    for line_num, line in enumerate(lines, 1):
                        is_match, positions = self._should_match(line, search_text)
                        if is_match:
                            for pos_start, pos_end in positions:
                                matches.append({
                                    'line_num': line_num,
                                    'line_content': line.rstrip(),
                                    'column': pos_start + 1,
                                    'match_start': pos_start,
                                    'match_end': pos_end
                                })
                    
                    if matches:
                        self.stats['matches_found'] += len(matches)
                        self.results.append({
                            'file_path': str(file_path),
                            'relative_path': str(file_path.relative_to(self.search_dir)),
                            'matches': matches
                        })
                        
                except UnicodeDecodeError:
                    self.stats['files_skipped'] += 1
                    self.skipped_files.append((str(file_path), '编码错误'))
                except PermissionError:
                    self.stats['files_skipped'] += 1
                    self.skipped_files.append((str(file_path), '权限不足'))
                except IOError as e:
                    self.stats['files_skipped'] += 1
                    self.skipped_files.append((str(file_path), f'IO错误: {str(e)}'))
        
        return self.results

    def find_names(self, search_text: str) -> List[Dict]:
        """查找文件名和文件夹名中包含search_text的项"""
        self.name_results = []
        
        for root, dirs, files in os.walk(self.search_dir):
            # 排除指定目录（修改原列表以避免遍历）
            dirs[:] = [d for d in dirs if d not in self.exclude_dirs and not d.startswith('.')]
            
            # 文件夹名匹配
            for d in dirs:
                is_match, _ = self._should_match(d, search_text)
                if is_match:
                    abs_path = str(Path(root) / d)
                    rel_path = str(Path(root).joinpath(d).relative_to(self.search_dir))
                    self.name_results.append({
                        'type': 'dir',
                        'name': d,
                        'abs_path': abs_path,
                        'rel_path': rel_path
                    })
            
            # 文件名匹配（跳过隐藏文件）
            for f in files:
                if f.startswith('.'):
                    continue
                is_match, _ = self._should_match(f, search_text)
                if is_match:
                    abs_path = str(Path(root) / f)
                    rel_path = str(Path(root).joinpath(f).relative_to(self.search_dir))
                    self.name_results.append({
                        'type': 'file',
                        'name': f,
                        'abs_path': abs_path,
                        'rel_path': rel_path
                    })
        
        return self.name_results
    
    def print_stats(self):
        """打印统计信息"""
        print(f"\n{Colors.BOLD}=== 扫描统计 ==={Colors.RESET}")
        print(f"扫描目录数: {Colors.CYAN}{self.stats['dirs_scanned']}{Colors.RESET}")
        print(f"扫描文件数: {Colors.CYAN}{self.stats['files_scanned']}{Colors.RESET}")
        print(f"跳过文件数: {Colors.YELLOW}{self.stats['files_skipped']}{Colors.RESET}")
        print(f"找到匹配数: {Colors.GREEN}{self.stats['matches_found']}{Colors.RESET}")
        
        if self.skipped_files:
            print(f"\n{Colors.YELLOW}跳过的文件:{Colors.RESET}")
            for file_path, reason in self.skipped_files[:10]:  # 只显示前10个
                print(f"  - {file_path} ({reason})")
            if len(self.skipped_files) > 10:
                print(f"  ... 还有 {len(self.skipped_files) - 10} 个文件被跳过")

    def save_results_to_file(self, search_text: str) -> str:
        """保存搜索结果到文件"""
        if not self.results and not self.name_results:
            print(f"{Colors.YELLOW}没有找到匹配的内容/名称{Colors.RESET}")
            return ""
        
        safe_filename = re.sub(r'[<>:"/\\|?*]', '_', search_text)
        if len(safe_filename) > 50:
            safe_filename = safe_filename[:50]
        
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        output_file = f"{safe_filename}_search_results_{timestamp}.txt"
        
        with open(output_file, 'w', encoding='utf-8') as f:
            f.write(f"搜索文本: {search_text}\n")
            f.write(f"搜索模式: {'正则表达式' if self.use_regex else '字面文本'}\n")
            f.write(f"搜索目录: {self.search_dir}\n")
            f.write(f"搜索时间: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n")
            
            # 统计信息
            f.write(f"\n[统计信息]\n")
            f.write(f"扫描目录数: {self.stats['dirs_scanned']}\n")
            f.write(f"扫描文件数: {self.stats['files_scanned']}\n")
            f.write(f"跳过文件数: {self.stats['files_skipped']}\n")
            f.write(f"找到匹配数: {self.stats['matches_found']}\n")
            
            f.write(f"\n[文件内容匹配]\n")
            f.write(f"找到 {len(self.results)} 个文件，共 {self.stats['matches_found']} 处匹配\n")
            f.write("=" * 80 + "\n\n")
            
            for result in self.results:
                f.write(f"文件: {result['relative_path']}\n")
                f.write(f"完整路径: {result['file_path']}\n")
                f.write("-" * 60 + "\n")
                for match in result['matches']:
                    f.write(f"行号: {match['line_num']}, 列号: {match['column']}\n")
                    f.write(f"内容: {match['line_content']}\n")
                    f.write("\n")
                f.write("=" * 80 + "\n\n")
            
            f.write(f"\n[文件名/文件夹名匹配]\n")
            f.write(f"找到 {len(self.name_results)} 个文件名/文件夹名包含匹配内容\n")
            f.write("=" * 80 + "\n\n")
            
            for item in self.name_results:
                f.write(f"类型: {'文件夹' if item['type']=='dir' else '文件'}\n")
                f.write(f"名称: {item['name']}\n")
                f.write(f"相对路径: {item['rel_path']}\n")
                f.write(f"完整路径: {item['abs_path']}\n")
                f.write("=" * 80 + "\n\n")
        
        print(f"{Colors.GREEN}搜索结果已保存到: {output_file}{Colors.RESET}")
        return output_file
    
    def create_backup(self, file_path: Path, backup_dir: str = "backup") -> str:
        """创建文件备份"""
        backup_path = Path(backup_dir)
        backup_path.mkdir(exist_ok=True)
        
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        relative_path = file_path.relative_to(self.search_dir)
        backup_file = backup_path / f"{relative_path.name}.{timestamp}.bak"
        
        # 如果有重名，添加编号
        counter = 1
        while backup_file.exists():
            backup_file = backup_path / f"{relative_path.name}.{timestamp}_{counter}.bak"
            counter += 1
        
        shutil.copy2(file_path, backup_file)
        return str(backup_file)

    def replace_text_interactive(self, search_text: str, replace_text: str, 
                                 create_backup: bool = False, backup_dir: str = "backup") -> int:
        """交互式替换文件内容"""
        if not self.results:
            print(f"{Colors.YELLOW}没有找到要替换的文件内容{Colors.RESET}")
            return 0
        
        total_replacements = 0
        files_modified = 0
        print(f"\n{Colors.BOLD}开始交互式内容替换:{Colors.RESET}")
        print(f"搜索: {Colors.RED}{search_text}{Colors.RESET}")
        print(f"替换: {Colors.GREEN}{replace_text}{Colors.RESET}")
        
        for result in self.results:
            file_path = Path(result['file_path'])
            
            try:
                with open(file_path, 'r', encoding='utf-8') as f:
                    content = f.read()
                    lines = content.splitlines(keepends=True)
                
                file_modified = False
                new_lines = lines.copy()
                processed_lines = set()  # 跟踪已处理的行号
                
                for match in result['matches']:
                    line_num = match['line_num']
                    line_idx = line_num - 1
                    
                    # 跳过已处理的行
                    if line_idx in processed_lines:
                        continue
                    
                    original_line = lines[line_idx]
                    
                    # 检查该行是否仍包含搜索文本
                    is_match, _ = self._should_match(new_lines[line_idx], search_text)
                    if not is_match:
                        continue
                    
                    print(f"\n{Colors.CYAN}文件: {result['relative_path']}{Colors.RESET}")
                    print(f"{Colors.YELLOW}行号: {line_num}{Colors.RESET}")
                    print(f"原内容: {original_line.rstrip()}")
                    
                    # 计算替换后的内容
                    if self.use_regex:
                        preview_line = re.sub(search_text, replace_text, original_line)
                    else:
                        preview_line = original_line.replace(search_text, replace_text)
                    print(f"新内容: {preview_line.rstrip()}")
                    
                    while True:
                        response = input(f"{Colors.BOLD}是否替换? (y/n/s=跳过此文件/q=退出): {Colors.RESET}").lower().strip()
                        if response in ['y', 'n', 's', 'q']:
                            break
                        print("请输入 y, n, s 或 q")
                    
                    if response == 'y':
                        # 计算这一行有多少处匹配
                        if self.use_regex:
                            count = len(re.findall(search_text, original_line))
                        else:
                            count = original_line.count(search_text)
                        
                        new_lines[line_idx] = preview_line
                        file_modified = True
                        total_replacements += count
                        processed_lines.add(line_idx)
                        print(f"{Colors.GREEN}✓ 已替换 {count} 处{Colors.RESET}")
                        
                    elif response == 's':
                        print(f"{Colors.YELLOW}跳过此文件{Colors.RESET}")
                        break
                    elif response == 'q':
                        print(f"{Colors.YELLOW}用户取消操作{Colors.RESET}")
                        return total_replacements
                    else:
                        print(f"{Colors.YELLOW}跳过这一行{Colors.RESET}")
                
                if file_modified:
                    # 创建备份
                    if create_backup:
                        backup_file = self.create_backup(file_path, backup_dir)
                        print(f"{Colors.BLUE}备份已创建: {backup_file}{Colors.RESET}")
                    
                    # 写入修改
                    with open(file_path, 'w', encoding='utf-8') as f:
                        f.writelines(new_lines)
                    files_modified += 1
                    print(f"{Colors.GREEN}✓ 文件已更新: {result['relative_path']}{Colors.RESET}")
                    
            except (UnicodeDecodeError, PermissionError, IOError) as e:
                print(f"{Colors.RED}错误处理文件 {file_path}: {str(e)}{Colors.RESET}")
                continue
        
        print(f"\n{Colors.BOLD}内容替换完成:{Colors.RESET}")
        print(f"  修改文件数: {Colors.GREEN}{files_modified}{Colors.RESET}")
        print(f"  替换总数: {Colors.GREEN}{total_replacements}{Colors.RESET}")
        return total_replacements

    def replace_text_auto(self, search_text: str, replace_text: str, 
                          create_backup: bool = False, backup_dir: str = "backup", 
                          dry_run: bool = False) -> int:
        """自动替换文件内容"""
        if not self.results:
            print(f"{Colors.YELLOW}没有找到要替换的文件内容{Colors.RESET}")
            return 0
        
        total_replacements = 0
        files_modified = 0
        
        if dry_run:
            print(f"\n{Colors.BOLD}{Colors.YELLOW}=== DRY RUN 模式（仅预览，不会实际修改文件）==={Colors.RESET}")
        else:
            print(f"\n{Colors.BOLD}开始自动内容替换:{Colors.RESET}")
        
        print(f"搜索: {Colors.RED}{search_text}{Colors.RESET}")
        print(f"替换: {Colors.GREEN}{replace_text}{Colors.RESET}\n")
        
        for result in self.results:
            file_path = Path(result['file_path'])
            
            try:
                with open(file_path, 'r', encoding='utf-8') as f:
                    content = f.read()
                    lines = content.splitlines(keepends=True)
                
                file_modified = False
                new_lines = lines.copy()
                processed_lines = set()
                file_replacement_count = 0
                
                for match in result['matches']:
                    line_num = match['line_num']
                    line_idx = line_num - 1
                    
                    if line_idx in processed_lines:
                        continue
                    
                    original_line = lines[line_idx]
                    is_match, _ = self._should_match(new_lines[line_idx], search_text)
                    
                    if not is_match:
                        continue
                    
                    # 计算这一行的匹配数量
                    if self.use_regex:
                        count = len(re.findall(search_text, original_line))
                        new_line = re.sub(search_text, replace_text, original_line)
                    else:
                        count = original_line.count(search_text)
                        new_line = original_line.replace(search_text, replace_text)
                    
                    new_lines[line_idx] = new_line
                    file_modified = True
                    file_replacement_count += count
                    processed_lines.add(line_idx)
                
                if file_modified:
                    if dry_run:
                        print(f"{Colors.CYAN}[预览] {result['relative_path']}: {file_replacement_count} 处替换{Colors.RESET}")
                    else:
                        # 创建备份
                        if create_backup:
                            backup_file = self.create_backup(file_path, backup_dir)
                            print(f"{Colors.BLUE}  备份: {backup_file}{Colors.RESET}")
                        
                        # 写入修改
                        with open(file_path, 'w', encoding='utf-8') as f:
                            f.writelines(new_lines)
                        
                        print(f"{Colors.GREEN}✓ {result['relative_path']}: {file_replacement_count} 处替换{Colors.RESET}")
                    
                    total_replacements += file_replacement_count
                    files_modified += 1
                    
            except (UnicodeDecodeError, PermissionError, IOError) as e:
                print(f"{Colors.RED}错误处理文件 {file_path}: {str(e)}{Colors.RESET}")
                continue
        
        print(f"\n{Colors.BOLD}{'[DRY RUN] ' if dry_run else ''}内容替换完成:{Colors.RESET}")
        print(f"  {'将会修改' if dry_run else '已修改'}文件数: {Colors.GREEN}{files_modified}{Colors.RESET}")
        print(f"  {'将会替换' if dry_run else '已替换'}总数: {Colors.GREEN}{total_replacements}{Colors.RESET}")
        return total_replacements

    def replace_names_interactive(self, search_text: str, replace_text: str):
        """交互式重命名文件/文件夹"""
        if not self.name_results:
            print(f"{Colors.YELLOW}没有找到要重命名的文件名/文件夹名{Colors.RESET}")
            return 0
        
        count = 0
        print(f"\n{Colors.BOLD}开始交互式重命名:{Colors.RESET}")
        print(f"搜索: {Colors.RED}{search_text}{Colors.RESET}")
        print(f"替换: {Colors.GREEN}{replace_text}{Colors.RESET}\n")
        
        # 从深到浅排序，避免父目录被重命名后子项路径失效
        for item in sorted(self.name_results, key=lambda x: -len(x['abs_path'])):
            old_path = Path(item['abs_path'])
            
            # 如果路径已不存在（可能父目录被重命名了），跳过
            if not old_path.exists():
                print(f"{Colors.YELLOW}跳过（路径已不存在）: {item['abs_path']}{Colors.RESET}")
                continue
            
            if self.use_regex:
                new_name = re.sub(search_text, replace_text, item['name'])
            else:
                new_name = item['name'].replace(search_text, replace_text)
            
            new_path = old_path.parent / new_name
            
            # 检查目标是否已存在
            if new_path.exists():
                print(f"\n{Colors.RED}警告: 目标已存在{Colors.RESET}")
                print(f"类型: {'文件夹' if item['type']=='dir' else '文件'}")
                print(f"原名称: {item['name']}")
                print(f"新名称: {new_name}")
                print(f"{Colors.RED}目标路径已存在，跳过{Colors.RESET}\n")
                continue
            
            print(f"\n类型: {Colors.CYAN}{'文件夹' if item['type']=='dir' else '文件'}{Colors.RESET}")
            print(f"原名称: {Colors.YELLOW}{item['name']}{Colors.RESET}")
            print(f"路径: {item['rel_path']}")
            print(f"新名称: {Colors.GREEN}{new_name}{Colors.RESET}")
            
            while True:
                response = input(f"{Colors.BOLD}是否重命名? (y/n/q=退出): {Colors.RESET}").lower().strip()
                if response in ['y', 'n', 'q']:
                    break
                print("请输入 y, n 或 q")
            
            if response == 'y':
                try:
                    old_path.rename(new_path)
                    print(f"{Colors.GREEN}✓ 已重命名: {new_name}{Colors.RESET}")
                    count += 1
                except Exception as e:
                    print(f"{Colors.RED}重命名失败: {e}{Colors.RESET}")
            elif response == 'q':
                print(f"{Colors.YELLOW}用户取消操作{Colors.RESET}")
                return count
            else:
                print(f"{Colors.YELLOW}跳过{Colors.RESET}")
        
        print(f"\n{Colors.BOLD}重命名完成，共重命名了 {count} 个{Colors.RESET}")
        return count

    def replace_names_auto(self, search_text: str, replace_text: str, dry_run: bool = False):
        """自动重命名文件/文件夹"""
        if not self.name_results:
            print(f"{Colors.YELLOW}没有找到要重命名的文件名/文件夹名{Colors.RESET}")
            return 0
        
        count = 0
        skipped = 0
        
        if dry_run:
            print(f"\n{Colors.BOLD}{Colors.YELLOW}=== DRY RUN 模式（仅预览，不会实际重命名）==={Colors.RESET}")
        else:
            print(f"\n{Colors.BOLD}开始自动重命名:{Colors.RESET}")
        
        print(f"搜索: {Colors.RED}{search_text}{Colors.RESET}")
        print(f"替换: {Colors.GREEN}{replace_text}{Colors.RESET}\n")
        
        # 先重命名文件，再重命名文件夹（从深到浅）
        for item in sorted(self.name_results, key=lambda x: (-len(x['abs_path']), x['type'] == 'dir')):
            old_path = Path(item['abs_path'])
            
            # 如果路径已不存在（可能父目录被重命名了），跳过
            if not dry_run and not old_path.exists():
                skipped += 1
                continue
            
            if self.use_regex:
                new_name = re.sub(search_text, replace_text, item['name'])
            else:
                new_name = item['name'].replace(search_text, replace_text)
            
            new_path = old_path.parent / new_name
            
            # 检查目标是否已存在
            if new_path.exists() and old_path != new_path:
                print(f"{Colors.RED}✗ 目标已存在，跳过: {item['rel_path']} -> {new_name}{Colors.RESET}")
                skipped += 1
                continue
            
            try:
                if dry_run:
                    print(f"{Colors.CYAN}[预览] {item['rel_path']} -> {new_name}{Colors.RESET}")
                else:
                    old_path.rename(new_path)
                    print(f"{Colors.GREEN}✓ {item['rel_path']} -> {new_name}{Colors.RESET}")
                count += 1
            except Exception as e:
                print(f"{Colors.RED}✗ 重命名失败 {item['rel_path']}: {e}{Colors.RESET}")
                skipped += 1
        
        print(f"\n{Colors.BOLD}{'[DRY RUN] ' if dry_run else ''}重命名完成:{Colors.RESET}")
        print(f"  {'将会重命名' if dry_run else '已重命名'}: {Colors.GREEN}{count}{Colors.RESET}")
        if skipped > 0:
            print(f"  跳过: {Colors.YELLOW}{skipped}{Colors.RESET}")
        return count


def main():
    parser = argparse.ArgumentParser(
        description='文件查找和替换工具 v2.0（内容+文件名+文件夹名）',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
使用示例:
  # 查找包含 'db_host' 的内容、文件名、文件夹名
  python3 file_finder_replacer.py -s db_host
  
  # 查找并保存结果
  python3 file_finder_replacer.py -s db_host -o
  
  # 查找特定类型文件内容
  python3 file_finder_replacer.py -s db_host -e .php,.py
  
  # 使用正则表达式查找
  python3 file_finder_replacer.py -s "db_\\w+" --regex
  
  # 查找并交互式替换内容和重命名（带备份）
  python3 file_finder_replacer.py -s old_text -r new_text --backup
  
  # 查找并自动替换内容和重命名（带备份）
  python3 file_finder_replacer.py -s old_text -r new_text -a --backup
  
  # Dry-run 模式（仅预览，不实际修改）
  python3 file_finder_replacer.py -s old_text -r new_text -a --dry-run
  
  # 在指定目录中查找，排除特定目录
  python3 file_finder_replacer.py -d /path/to/dir -s db_host --exclude cache,tmp
  
  # 正则表达式替换示例
  python3 file_finder_replacer.py -s "db_(\\w+)" -r "database_\\1" --regex -a
        """
    )
    parser.add_argument('-d', '--dir', default='.', help='搜索目录 (默认: 当前目录)')
    parser.add_argument('-s', '--search', required=True, help='要搜索的文本或正则表达式')
    parser.add_argument('-r', '--replace', help='要替换为的文本')
    parser.add_argument('-e', '--extensions', help='文件扩展名过滤 (如: .py,.txt)')
    parser.add_argument('-o', '--output', action='store_true', help='保存搜索结果到文件')
    parser.add_argument('-a', '--auto', action='store_true', help='自动替换/重命名模式（无需确认）')
    parser.add_argument('-i', '--interactive', action='store_true', help='交互式模式（默认）')
    parser.add_argument('--regex', action='store_true', help='使用正则表达式搜索')
    parser.add_argument('--backup', action='store_true', help='替换前创建备份文件')
    parser.add_argument('--backup-dir', default='backup', help='备份文件目录 (默认: backup)')
    parser.add_argument('--dry-run', action='store_true', help='预览模式，不实际修改文件')
    parser.add_argument('--exclude', help='排除的目录（逗号分隔，如: cache,tmp）')
    parser.add_argument('--no-color', action='store_true', help='禁用彩色输出')
    args = parser.parse_args()
    # 禁用颜色输出
    if args.no_color:
        for attr in dir(Colors):
            if not attr.startswith('_'):
                setattr(Colors, attr, '')
    
    # 处理文件扩展名
    file_extensions = None
    if args.extensions:
        file_extensions = [ext.strip().lower() for ext in args.extensions.split(',')]
        file_extensions = [ext if ext.startswith('.') else f'.{ext}' for ext in file_extensions]
    
    # 处理排除目录
    exclude_dirs = FileFinderReplacer.DEFAULT_EXCLUDE_DIRS.copy()
    if args.exclude:
        custom_excludes = {d.strip() for d in args.exclude.split(',') if d.strip()}
        exclude_dirs.update(custom_excludes)
    
    # 创建查找器
    finder = FileFinderReplacer(args.dir, exclude_dirs=exclude_dirs, use_regex=args.regex)
    
    # 显示配置
    print(f"{Colors.BOLD}=== 文件查找和替换工具 v2.0 ==={Colors.RESET}")
    print(f"搜索目录: {Colors.CYAN}{finder.search_dir}{Colors.RESET}")
    print(f"搜索模式: {Colors.CYAN}{'正则表达式' if args.regex else '字面文本'}{Colors.RESET}")
    print(f"排除目录: {Colors.YELLOW}{', '.join(sorted(exclude_dirs))}{Colors.RESET}")
    if file_extensions:
        print(f"文件类型: {Colors.CYAN}{', '.join(file_extensions)}{Colors.RESET}")
    print()
    
    # 查找内容
    results = finder.find_files_with_text(args.search, file_extensions)
    # 查找文件名/文件夹名
    name_results = finder.find_names(args.search)
    
    # 显示统计
    finder.print_stats()
    
    if not results and not name_results:
        print(f"\n{Colors.YELLOW}没有找到包含指定文本的内容、文件名或文件夹名{Colors.RESET}")
        return
    
    # 显示搜索结果摘要
    print(f"\n{Colors.BOLD}=== 搜索结果 ==={Colors.RESET}")
    print(f"文件内容匹配: {Colors.GREEN}{len(results)}{Colors.RESET} 个文件")
    print(f"文件名/文件夹名匹配: {Colors.GREEN}{len(name_results)}{Colors.RESET} 个\n")
    
    # 显示文件内容匹配详情
    if results:
        print(f"{Colors.BOLD}[文件内容匹配]{Colors.RESET}")
        for result in results[:20]:  # 只显示前20个
            print(f"  {Colors.CYAN}{result['relative_path']}{Colors.RESET} ({len(result['matches'])} 处匹配)")
        if len(results) > 20:
            print(f"  {Colors.YELLOW}... 还有 {len(results) - 20} 个文件{Colors.RESET}")
    
    # 显示文件名/文件夹名匹配详情
    if name_results:
        print(f"\n{Colors.BOLD}[文件名/文件夹名匹配]{Colors.RESET}")
        for item in name_results[:20]:  # 只显示前20个
            icon = '📁' if item['type'] == 'dir' else '📄'
            print(f"  {icon} {Colors.CYAN}{item['rel_path']}{Colors.RESET}")
        if len(name_results) > 20:
            print(f"  {Colors.YELLOW}... 还有 {len(name_results) - 20} 个{Colors.RESET}")
    
    # 保存结果到文件
    if args.output:
        finder.save_results_to_file(args.search)
    
    # 替换操作
    if args.replace:
        print(f"\n{Colors.BOLD}=== 开始替换操作 ==={Colors.RESET}")
        
        # Dry-run 警告
        if args.dry_run:
            print(f"{Colors.YELLOW}注意: 这是预览模式，不会实际修改文件{Colors.RESET}\n")
        elif args.backup:
            print(f"{Colors.BLUE}备份已启用，修改的文件将被备份到 {args.backup_dir}/ 目录{Colors.RESET}\n")
        else:
            print(f"{Colors.RED}警告: 未启用备份，文件将直接被修改！{Colors.RESET}")
            if not args.auto:
                response = input(f"{Colors.BOLD}是否继续? (y/n): {Colors.RESET}").lower().strip()
                if response != 'y':
                    print(f"{Colors.YELLOW}操作已取消{Colors.RESET}")
                    return
        
        if args.auto:
            # 自动模式
            finder.replace_text_auto(args.search, args.replace, 
                                    create_backup=args.backup, 
                                    backup_dir=args.backup_dir, 
                                    dry_run=args.dry_run)
            finder.replace_names_auto(args.search, args.replace, dry_run=args.dry_run)
        else:
            # 交互式模式
            finder.replace_text_interactive(args.search, args.replace, 
                                           create_backup=args.backup, 
                                           backup_dir=args.backup_dir)
            finder.replace_names_interactive(args.search, args.replace)
    
    print(f"\n{Colors.GREEN}{Colors.BOLD}操作完成！{Colors.RESET}")

if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\n\n用户中断操作")
    except Exception as e:
        print(f"\n发生错误: {e}")
        sys.exit(1) 