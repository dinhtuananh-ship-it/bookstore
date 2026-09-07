<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Lỗi máy chủ | <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Baloo+2:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Roboto', system-ui, Arial, sans-serif;
            background: linear-gradient(135deg, #1c2531, #232f40 55%, #b1151f);
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        body::before {
            content: '';
            position: absolute;
            inset: -50%;
            background: radial-gradient(circle at 30% 30%, rgba(245,179,1,0.15) 0, transparent 45%),
                        radial-gradient(circle at 70% 70%, rgba(255,255,255,0.08) 0, transparent 40%);
            animation: drift 14s ease-in-out infinite alternate;
        }
        .error-box { text-align: center; padding: 24px; max-width: 520px; position: relative; z-index: 2; animation: pop 0.8s cubic-bezier(0.34, 1.56, 0.64, 1); }
        .error-gear { font-size: 64px; display: inline-block; animation: spin 8s linear infinite; }
        .error-code {
            font-size: 120px;
            font-weight: 800;
            font-family: 'Baloo 2', sans-serif;
            line-height: 1;
            text-shadow: 0 8px 30px rgba(0,0,0,0.4);
            background: linear-gradient(180deg, #fff, #ff9d8f);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .error-box h1 { font-size: 26px; margin: 12px 0 8px; font-weight: 800; font-family: 'Baloo 2', sans-serif; }
        .error-box p { color: rgba(255,255,255,0.82); font-size: 15px; margin-bottom: 26px; }
        .error-box a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #d81b28, #b1151f);
            color: #fff;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 15px;
            box-shadow: 0 10px 26px rgba(0,0,0,0.3);
            transition: transform 0.25s, box-shadow 0.25s;
        }
        .error-box a:hover { transform: translateY(-3px) scale(1.04); box-shadow: 0 16px 36px rgba(0,0,0,0.38); }
        @keyframes drift {
            0% { transform: translate(0, 0) rotate(0); }
            100% { transform: translate(2%, -3%) rotate(6deg); }
        }
        @keyframes spin { 0% { transform: rotate(0); } 100% { transform: rotate(360deg); } }
        @keyframes pop {
            from { opacity: 0; transform: scale(0.7); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="error-box">
        <div class="error-gear">⚙️</div>
        <div class="error-code">500</div>
        <h1>Hệ thống đang gặp trục trặc</h1>
        <p>Vui lòng thử lại sau hoặc liên hệ quản trị viên.</p>
        <a href="<?= e(url('/')) ?>">← Về trang chủ</a>
    </div>
</body>
</html>