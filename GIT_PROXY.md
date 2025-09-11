# Git 代理配置指南

本仓库提供脚本脚手架，便捷地为 Git 设置/取消/查看代理。默认仅作用于当前仓库（--local）。

## 脚本位置
- scripts/set-git-proxy.sh

如无执行权限：
```bash
chmod +x scripts/set-git-proxy.sh
```

## 常用用法
- 设置 HTTP 代理（本仓库）：
```bash
scripts/set-git-proxy.sh set http 127.0.0.1 7890 --local
```
- 设置 HTTPS 代理（全局）：
```bash
scripts/set-git-proxy.sh set https http://127.0.0.1:7890 --global
```
- 设置 SOCKS5 代理（本仓库）：
```bash
scripts/set-git-proxy.sh set socks5 127.0.0.1 1080 --local
```
- 仅为 github.com 设置代理（本仓库）：
```bash
scripts/set-git-proxy.sh set-domain github.com http://127.0.0.1:7890 --local
```
- 取消代理（本仓库）：
```bash
scripts/set-git-proxy.sh unset --local
```
- 查看当前代理：
```bash
scripts/set-git-proxy.sh show
```

## 参数说明
- --local：仅修改当前仓库 .git/config（默认）
- --global：修改全局 ~/.gitconfig
- scheme：支持 http、https、socks5、socks5h
- set 命令支持两种形式：
  - set <scheme> <host> <port>
  - set <scheme> <url>（如 http://127.0.0.1:7890）

## 扩展说明
- set 会同时设置 http.proxy 和 https.proxy。
- 可按域名设置代理：配置键为 https.<domain>.proxy，例如 https.github.com.proxy。
- 不建议直接提交 .git/config 的具体代理值到版本库，因此脚本默认只对本地配置生效。

## 手动配置示例（等价）
```bash
git config --local http.proxy http://127.0.0.1:7890
git config --local https.proxy http://127.0.0.1:7890
# 仅对 github.com 走代理
git config --local https.github.com.proxy http://127.0.0.1:7890
# 取消
git config --local --unset http.proxy || true
git config --local --unset https.proxy || true
```

## 常见问题
1) 克隆前无仓库如何设置？
- 使用 --global：
```bash
scripts/set-git-proxy.sh set http 127.0.0.1 7890 --global
```

2) 仅走 HTTPS 代理？
- 指定 scheme 为 https 并提供 URL：
```bash
scripts/set-git-proxy.sh set https https://127.0.0.1:7890 --local
```

3) 如何还原？
- 执行 unset，对全局配置则加 --global。

4) 环境变量代理
- 某些环境中还可通过环境变量控制，如：
```bash
export http_proxy=http://127.0.0.1:7890
export https_proxy=http://127.0.0.1:7890
```
但这通常作用于“当前 shell 进程及其子进程”，与 git config 互不冲突。
