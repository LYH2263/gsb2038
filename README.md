# 茶文化网站

茶文化主题网站：约 10 页，注册/登录、茶品百科（管理员维护）、茶类与文章展示、茶品评论留言。支持 Docker Compose 一键启动。

## 技术栈

| 层级 | 技术 |
|------|------|
| Frontend | Vue 3、Vue Router、Pinia、Tailwind CSS、Axios、Vite |
| Backend | Laravel 11、Sanctum |
| Database | MySQL 8.0 |
| 部署 | Docker、Docker Compose |

## 项目结构

- `docker-compose.yml`：db、backend、frontend 三服务
- `backend/`：Laravel 11（API、认证、teas、articles、tea_types、comments）
- `frontend/`：Vue 3 单页应用，`public/images/` 下为茶品、文章、茶类本地图片

## 启动指南

1. 确保 Docker 已安装并启动。
2. 在项目根目录 `label-2038` 下执行：
   ```bash
   docker compose up --build
   ```
3. 等待数据库就绪、后端迁移与 Seed 完成、前端服务启动。

## 服务地址

- **前端**：http://localhost:3000（Vite Dev Server；`/api` 与 `/images` 通过 Vite Proxy 转发到后端）
- **后端 API**：http://localhost:8000
- **MySQL**：localhost:3306（库 `tea_culture`，用户 `tea` / 密码 `tea_secret`）

## 测试账号与权限

- **管理员**：邮箱 admin@example.com / 密码 123456，可进行茶品新增、编辑、删除。
- **普通用户**：邮箱 user@example.com / 密码 123456（或通过注册页注册），仅可浏览茶品与全站内容，不可修改茶百科；登录后可发表评论。

## 权限说明

- 茶品百科（茶品的新增/编辑/删除）：**仅管理员**可操作
- 茶品评论：
  - 发表：**任意登录用户**可在茶品详情页发表评论
  - 删除：**评论作者本人**或**管理员**可删除

## 功能与页面

- **首页**：简介、探索茶世界、精选茶品、茶之四德、名句、六大茶类、底部引导
- **茶文化概览 / 茶史 / 茶道 / 茶与健康**：长文 + 配图（含非遗、报道、获奖等）
- **茶类**：六大茶类卡片，点击进入茶类详情（介绍、产地、历史、非遗与获奖等）
- **茶品百科**：列表、详情；仅管理员登录后可新增、编辑、删除茶品；无图时显示默认图
- **茶品评论**：登录后可在茶品详情页发表评论；本人或管理员可删除评论
- **关于我们**、**登录**、**注册**（注册成功后跳转登录页）

## 图片上传

- 新增/编辑茶品：**仅支持上传图片**（JPG/PNG/GIF/WebP，≤ 2MB），上传后后端保存到 `backend/public/images/teas/` 并返回可访问路径

## Docker 说明

- **db**：MySQL 8.0，Volume 持久化，健康检查
- **backend**：等待 MySQL 可连后执行 migrate、seed，再 `php artisan serve` 监听 8000
- **frontend**：Node 容器运行 `npm run dev`（Vite），并通过 Vite Proxy 转发 `/api` 与 `/images` 到后端（`VITE_API_TARGET=http://backend:8000`）

## 其他

- Seed 含测试用户、茶品、茶类、文章（含六大茶类文章）；茶品与文章、茶类配图均在 `frontend/public/images/`，无外链。
