<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Smart Attendance') }}</title>

    <style>
        body {
            background: #0f172a;
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #e5e7eb;
        }
        .debug-wrapper {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .debug-box {
            background: #020617;
            border: 2px dashed #38bdf8;
            padding: 40px 60px;
            border-radius: 12px;
            box-shadow: 0 0 40px rgba(56,189,248,0.25);
            text-align: center;
            max-width: 600px;
        }
        .debug-box h1 {
            margin: 0 0 15px;
            font-size: 32px;
            color: #38bdf8;
        }
        .debug-box p {
            font-size: 16px;
            color: #cbd5f5;
            line-height: 1.6;
            white-space: pre-line;
        }
        .badge {
            display: inline-block;
            margin-top: 20px;
            padding: 6px 14px;
            background: #38bdf8;
            color: #020617;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }
    </style>
</head>

<body>
<div class="debug-wrapper">
    <div class="debug-box">
        <h1>{{ $title ?? '🚧 Under Construction' }}</h1>

        <p>
            {{ $message ?? 'This module is currently being developed.' }}
        </p>

        <span class="badge">
            {{ $badge ?? 'Development Mode' }}
        </span>
    </div>
</div>
</body>
</html>
