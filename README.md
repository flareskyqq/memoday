# Memoday

一个简单的 PWA 日历应用，用于记录每日事件。

## 功能

- 日历视图，支持月份切换
- 自定义事件类型和颜色
- 记录特定日期的事件
- 通过 URL 参数 `?user=xxx` 支持多用户
- 可作为 PWA 安装到 iOS/Android

## 使用方法

在浏览器中打开 `memoday/index.html`。使用 `?user=xxx` 区分不同用户。

## API 接口

- `api.php?action=get_types&user=xxx` - 获取事件类型
- `api.php?action=add_type&user=xxx&name=xxx&color=xxx` - 添加事件类型
- `api.php?action=del_type&user=xxx&id=xxx` - 删除事件类型
- `api.php?action=get_records&user=xxx&date=YYYY-MM-DD` - 获取某日记录
- `api.php?action=add_record&user=xxx&event_type_id=xxx&date=YYYY-MM-DD` - 添加记录
- `api.php?action=del_record&user=xxx&id=xxx` - 删除记录
- `api.php?action=get_month_records&user=xxx&year=YYYY&month=MM` - 获取某月记录