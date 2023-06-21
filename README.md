# Brave
## 简介
一个 WordPress 主题，实现了功能上的配置化

## 功能列表
- 响应式布局；
- 自动暗黑模式；
- 支持 schemas 标记；
- 支持部分 web app 特性；
- 日志和评论支持无限滚动加载；
- 标准（standard） 格式支持视频背景；
- 状态（status） 格式支持喜欢按钮、 支持地理位置坐标；
- 日志、评论自动记录发布者终端设备信息；
- 上传附件附加随机字符串的安全机制；
- 发布日志汉英字符间自动补空格；
- 相对完善的垃圾评论抑制机制；
- 多样的可参数化配置；
- 原生 JavaScript；
- 支持代码高亮。


## 注意事项
- 不支持古腾堡编辑器；
- 支持 PHP7.0 或更高版本。

## 如何使用
### 安装
下载安装包，解压放至 WordPress 主题目录，启用即可。

### 修改配置
打开主题目录 conf 文件夹下的 config.php 文件，所有可修改的配置项均放在此文件的 $this->config_array 后面的 array 数组里，基本每项配置都有说明。

### 使用许可
本主题采用 [GPL-3.0 license](https://github.com/yusn/Brave/blob/main/LICENSE.md) 开源协议许可。

## 更新日志

### v1.2.6.4
- Infinite Ajax Scroll 更新到3.1，相关方法优化，翻页时 url 也跟着更新
- 一些过滤器的调整
- header.php 添加了
- 调整配置文件中时区的获取方法
- 增加错误邮件发送提醒（默认开启）

### v1.2.6.1
- 修复判断是否开启评论检测的逻辑
- 增加同期日志功能

### v1.2.6.0
- 增加 Prismjs 对 PHP 语言的支持
- 增加异步邮件功能
- 调整评论来源校验逻辑，现在不控制登陆用户了

### v1.2.3.5
- 修复 Linux 无法获取评论处理文件路径的问题
- 修复 WordPress APP 无法评论的问题
- 修复日志设备信息更新逻辑问题
- 修复地理坐标不显示的问题
- 调整评论处理逻辑
- 增加视频过滤配置
- 移除评论编辑按钮

### v1.2.3.4
- 优化和增加工具函数 get_array_key, pick_array
- 日志汉英字符间自动补空格
- 兼容 PHP 7.0
- 配置优化和问题修复

### v1.2.3.5
- 修复 Linux 无法获取评论处理文件路径的问题;
- 修复 WordPress APP 无法评论的问题；
- 修复日志设备信息更新逻辑问题；
- 修复地理坐标不显示的问题；
- 调整评论处理逻辑；
- 增加视频过滤配置；
- 移除评论编辑按钮。
