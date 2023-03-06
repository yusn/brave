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
- 相对完善的垃圾评论抑制机制；
- 支持代码高亮；
- 原生 JavaScript；
- 上传附件附加随机字符串的安全机制；
- 多样的可参数化配置。

## 注意事项
- 不支持古腾堡编辑器；
- 仅支持 PHP7.1.0 或更高版本。

## 如何使用
### 安装
下载安装包，解压放至 WordPress 主题目录，启用即可。

### 修改配置
打开主题目录中 conf 目录下的 config.php 文件，所有的可修改配置均放在此文件的 get_brave_info() 函数内的 $config_array 数组里。

### 使用许可
本主题采用 [GPL-3.0 license](https://github.com/yusn/Brave/blob/main/LICENSE.md) 开源协议许可。
