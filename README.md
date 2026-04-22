# Memoday

A simple PWA calendar app for recording daily events.

## Features

- Calendar view with month navigation
- Event types with customizable colors
- Record events to specific dates
- Multi-user support via URL parameter `?user=xxx`
- Installable as PWA on iOS/Android

## Usage

Open `memoday/index.html` in a browser. Use `?user=xxx` for different users.

## API Endpoints

- `api.php?action=get_types&user=xxx` - Get event types
- `api.php?action=add_type&user=xxx&name=xxx&color=xxx` - Add event type
- `api.php?action=del_type&user=xxx&id=xxx` - Delete event type
- `api.php?action=get_records&user=xxx&date=YYYY-MM-DD` - Get records for a date
- `api.php?action=add_record&user=xxx&event_type_id=xxx&date=YYYY-MM-DD` - Add record
- `api.php?action=del_record&user=xxx&id=xxx` - Delete record
- `api.php?action=get_month_records&user=xxx&year=YYYY&month=MM` - Get records for a month