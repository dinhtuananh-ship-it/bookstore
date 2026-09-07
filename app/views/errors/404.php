<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không tìm thấy trang | <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Baloo+2:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Roboto', system-ui, Arial, sans-serif;
            background: linear-gradient(135deg, #8f0f17, #d81b28 50%, #f28c1f);
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        body::before, body::after {
            content: '📖';
            position: absolute;
            font-size: 90px;
            opacity: 0.12;
            animation: float-up 12s linear infinite;
        }
        body::before { left: 8%; bottom: -20%; }
        body::after { right: 10%; bottom: -30%; animation-duration: 16s; }
        .error-box {
            text-align: center;
            padding: 24px;
            max-width: 520px;
            position: relative;
            animation: pop 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .error-books { font-size: 72px; animation: wobble 3s ease-in-out infinite; display: inline-block; }
        .error-code {
            font-size: 120px;
            font-weight: 800;
            font-family: 'Baloo 2', sans-serif;
            line-height: 1;
            text-shadow: 0 8px 30px rgba(0,0,0,0.35);
            background: linear-gradient(180deg, #fff, #ffd9a0);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .error-box h1 { font-size: 26px; margin: 12px 0 8px; font-weight: 800; font-family: 'Baloo 2', sans-serif; }
        .error-box p { color: rgba(255,255,255,0.85); font-size: 15px; margin-bottom: 26px; }
        .error-box a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            color: #d81b28;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 15px;
            box-shadow: 0 10px 26px rgba(0,0,0,0.28);
            transition: transform 0.25s, box-shadow 0.25s;
        }
        .error-box a:hover { transform: translateY(-3px) scale(1.04); box-shadow: 0 16px 36px rgba(0,0,0,0.35); }
        @keyframes float-up {
            0% { transform: translateY(0) rotate(0); }
            100% { transform: translateY(-120vh) rotate(30deg); }
        }
        @keyframes pop {
            from { opacity: 0; transform: scale(0.7); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes wobble {
            0%, 100% { transform: rotate(0); }
            25% { transform: rotate(-8deg); }
            75% { transform: rotate(8deg); }
        }
    </style>
</head>
<body>
    <div class="error-box">
        <div class="error-books">📚</div>
        <div class="error-code">404</div>
        <h1>Ơ kìa! Trang này bị "lạc" rồi</h1>
        <p>Trang bạn đang tìm đã bị di chuyển, xóa hoặc không tồn tại.</p>
        <a href="<?= e(url('/')) ?>">← Về trang chủ</a>
    </div>
</body>
</html>