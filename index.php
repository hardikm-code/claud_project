<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hello World - PHP</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 60px 80px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 90%;
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .greeting {
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 16px;
        }

        .subtitle {
            font-size: 1.1rem;
            color: #888;
            margin-bottom: 30px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-top: 30px;
        }

        .info-box {
            background: #f8f7ff;
            border-radius: 12px;
            padding: 16px;
            border-left: 4px solid #667eea;
        }

        .info-box .label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #999;
            margin-bottom: 4px;
        }

        .info-box .value {
            font-size: 0.95rem;
            font-weight: 600;
            color: #333;
        }

        .pulse {
            display: inline-block;
            width: 12px;
            height: 12px;
            background: #4caf50;
            border-radius: 50%;
            margin-right: 8px;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%   { box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.5); }
            70%  { box-shadow: 0 0 0 10px rgba(76, 175, 80, 0); }
            100% { box-shadow: 0 0 0 0 rgba(76, 175, 80, 0); }
        }

        .status-bar {
            margin-top: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            color: #555;
        }

        .php-badge {
            display: inline-block;
            background: linear-gradient(135deg, #8892BF, #4F5B93);
            color: white;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 5px 16px;
            border-radius: 50px;
            margin-bottom: 20px;
        }

        .footer {
            margin-top: 28px;
            font-size: 12px;
            color: #bbb;
        }

        .footer span {
            color: #667eea;
            font-weight: 600;
        }
    </style>
</head>
<body>

<?php
    $currentTime = date('H:i:s');
    $currentDate = date('l, F j, Y');
    $phpVersion  = phpversion();
    $serverSoft  = $_SERVER['SERVER_SOFTWARE'] ?? 'XAMPP / Apache';
    $hostname    = gethostname();
?>

<div class="card">
    <div class="php-badge">PHP Powered</div>
    <div class="greeting">Hello, World!</div>
    <p class="subtitle">Your PHP server is up and running.</p>

    <div class="info-grid">
        <div class="info-box">
            <div class="label">Date</div>
            <div class="value"><?= htmlspecialchars($currentDate) ?></div>
        </div>
        <div class="info-box">
            <div class="label">Time</div>
            <div class="value"><?= htmlspecialchars($currentTime) ?></div>
        </div>
        <div class="info-box">
            <div class="label">PHP Version</div>
            <div class="value"><?= htmlspecialchars($phpVersion) ?></div>
        </div>
        <div class="info-box">
            <div class="label">Server</div>
            <div class="value"><?= htmlspecialchars($serverSoft) ?></div>
        </div>
        <div class="info-box">
            <div class="label">Host</div>
            <div class="value"><?= htmlspecialchars($hostname) ?></div>
        </div>
    </div>

    <div class="status-bar">
        <span class="pulse"></span> Server is running
    </div>

    <div class="footer">Built with <span>PHP <?= htmlspecialchars($phpVersion) ?></span> &amp; served by <span>XAMPP</span></div>
</div>

</body>
</html>
