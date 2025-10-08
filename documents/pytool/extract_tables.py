#!/usr/bin/env python3
import re
import sys

def extract_table_info(sql_file):
    """Extract table names and their fields from SQL file"""
    with open(sql_file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Find all CREATE TABLE statements
    # Pattern to match CREATE TABLE and capture table name and field definitions
    pattern = r'CREATE TABLE `([^`]+)` \((.*?)\) ENGINE'
    
    tables = {}
    matches = re.finditer(pattern, content, re.DOTALL)
    
    for match in matches:
        table_name = match.group(1)
        fields_block = match.group(2)
        
        # Extract field names (handle backticks)
        # Match lines that start with field definitions
        field_pattern = r'^\s*`([^`]+)`'
        fields = []
        
        for line in fields_block.split('\n'):
            field_match = re.match(field_pattern, line.strip())
            if field_match:
                field_name = field_match.group(1)
                # Skip PRIMARY KEY, UNIQUE KEY, KEY, etc.
                if not any(keyword in line.upper() for keyword in ['PRIMARY KEY', 'UNIQUE KEY', 'KEY `', 'CONSTRAINT']):
                    fields.append(field_name)
        
        if fields:
            tables[table_name] = fields
    
    return tables

def generate_markdown_table(tables):
    """Generate markdown table from tables dict"""
    print("| 表名 | 字段列表 |")
    print("| :--- | :--- |")
    
    for table_name in sorted(tables.keys()):
        fields = tables[table_name]
        # Join fields with comma and backticks
        fields_str = ', '.join([f'`{field}`' for field in fields])
        print(f"| {table_name} | {fields_str} |")

if __name__ == '__main__':
    sql_file = sys.argv[1] if len(sys.argv) > 1 else 'rylcsjk_20250907_160001.sql'
    tables = extract_table_info(sql_file)
    generate_markdown_table(tables)

