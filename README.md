# 2026新年倒计时网站

一个精美的双倒计时网站，同时显示距离2026年公历新年和农历春节的剩余时间，支持用户留言祝福和互动功能。

## ✨ 功能特性

- **双倒计时显示**：同时展示距离2026年公历新年（1月1日）和农历春节（1月29日）的剩余时间
- **动态背景**：固定背景图片，支持本地和网络图片加载
- **漂浮泡泡特效**：留言以漂浮泡泡形式展示，支持开启/关闭
- **互动留言板**：
  - 支持发布新年祝福和愿望
  - 支持按昵称、内容、日期筛选留言
  - 留言分页加载
  - 敏感词过滤
- **祝福计数器**：记录并实时更新祝福人数
- **设备时钟校准**：与标准时间对比，提示时钟准确性
- **倒计时结束特效**：新年到来时自动播放烟花动画
- **响应式设计**：完美适配桌面端和移动端

## 📁 项目结构

```
├── index.php              # 主页面
├── 1.jpg                  # 默认背景图片
├── api/                   # API接口目录
├── banword/               # 敏感词过滤目录
├── word/                  # 词汇相关目录
├── config.php             # 配置文件
├── get_messages.php       # 获取留言接口
├── message.php            # 留言处理逻辑
└── submit_message.php     # 提交留言接口
```

## 🚀 快速开始

### 环境要求

- PHP 7.0+ 
- 支持MySQL数据库（用于存储留言和祝福数据）
- 支持GD库（用于图片处理，可选）

### 安装步骤

1. **克隆或下载项目**
   ```bash
   git clone https://github.com/LittleFish0324/NewYearCountdown-PHP
   cd newyear_countdown
   ```

2. **配置数据库**
   - 编辑 `config.php` 文件，配置数据库连接信息
   - 确保数据库用户具有创建表和读写权限

3. **启动服务器**
   ```bash
   # 使用PHP内置服务器（开发环境）
   php -S localhost:8000 -t .
   
   # 或部署到Apache/Nginx服务器
   # 将项目文件放入网站根目录
   ```

4. **访问网站**
   - 打开浏览器，访问 `http://localhost:8000`
   - 或访问您的域名

## 🎯 使用说明

### 查看倒计时
- 网站首页将自动显示两个倒计时卡片
- 左侧：距离2026年公历新年的时间
- 右侧：距离2026年农历春节的时间

### 发送祝福
- 点击"祝福新年快乐 🎉"按钮
- 系统将记录您的祝福，并更新祝福人数
- 每个IP限制发送次数

### 发布留言
1. 在留言板区域填写昵称和留言内容
2. 点击"提交留言"按钮
3. 留言将实时显示在留言列表中
4. 支持HTML标签过滤和敏感词检测

### 筛选留言
1. 使用右侧筛选表单
2. 可以按昵称、内容或日期范围筛选
3. 点击"筛选"按钮查看结果
4. 点击"重置"按钮清除筛选条件

### 体验倒计时结束效果
- 点击"🎆 提前体验倒计时结束效果 🎆"按钮
- 立即查看新年到来时的庆祝动画和烟花特效

### 隐藏/显示泡泡特效
- 点击"新年留言板"标题可以隐藏/显示漂浮泡泡

## ⚙️ 配置选项

### 修改倒计时目标时间
编辑 `index.php` 文件中的JavaScript部分，修改以下代码中的目标日期：

```javascript
const solarNewYear = new Date('2026-01-01T00:00:00').getTime();
const lunarNewYear = new Date('2026-01-29T00:00:00').getTime();
```

### 修改背景图片
1. 替换 `1.jpg` 文件为您想要的背景图片
2. 或在 `index.php` 中修改背景图片列表：

```javascript
const bgImages = [
    '1.jpg', // 本地图片
    'https://example.com/background2.jpg', // 网络图片
    // 添加更多图片...
];
```

### 调整敏感词过滤
- 在 `banword/` 目录下添加或修改敏感词列表
- 支持自定义敏感词过滤规则

## 📝 技术栈

- **前端**：HTML5, CSS3, JavaScript (ES6+)
- **后端**：PHP 7.0+
- **数据库**：MySQL
- **UI设计**：响应式设计，支持移动端
- **特效**：CSS动画，Canvas烟花特效

## 🤝 贡献指南

欢迎提交Issue和Pull Request！

1. Fork 本仓库
2. 创建特性分支 (`git checkout -b feature/AmazingFeature`)
3. 提交更改 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 打开Pull Request

## 📄 许可证

本项目采用 MIT 许可证 - 查看 [LICENSE](LICENSE) 文件了解详情

## 🙏 致谢

- 感谢所有为项目贡献代码和建议的开发者
- 感谢提供免费图片资源的网站
- 感谢使用和支持本项目的用户

- 提交Issue：[GitHub Issues](https://github.com/yourusername/newyear_countdown/issues)
- 邮箱：your.email@example.com

---

**祝愿大家2026新年快乐，万事如意！🎉🧧**
