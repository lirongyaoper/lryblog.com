<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>系统发生错误</title>
    <link rel="Shortcut Icon" href="<?php echo STATIC_URL;?>lry_admin_center/lry_admin/images/favicon.ico" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #2c3e50;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        #msg {
            text-align: center;
            max-width: 600px;
            width: 100%;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
            padding: 60px 40px;
            animation: bounceIn 0.8s ease-out;
            position: relative;
            overflow: hidden;
        }
        
        #msg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #e74c3c, #f39c12, #f1c40f, #27ae60, #3498db, #9b59b6);
            background-size: 200% 100%;
            animation: rainbow 3s ease-in-out infinite;
        }
        
        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                opacity: 1;
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        @keyframes rainbow {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }
        
        #title {
            font-size: 52px;
            margin-bottom: 35px;
            animation: pulse 2s ease-in-out infinite;
            display: inline-block;
            color: #e74c3c;
            font-weight: 800;
            text-shadow: 3px 3px 6px rgba(231, 76, 60, 0.4);
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 2px;
            position: relative;
        }
        
        #title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #e74c3c, #f39c12);
            border-radius: 2px;
            animation: expand 2s ease-in-out infinite;
        }
        
        @keyframes expand {
            0%, 100% {
                width: 80px;
            }
            50% {
                width: 120px;
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                filter: brightness(1);
            }
            50% {
                transform: scale(1.05);
                filter: brightness(1.2);
            }
        }
        
        #body {
            font-size: 24px;
            margin-bottom: 40px;
            color: #34495e;
            font-weight: 500;
            line-height: 1.4;
            word-break: keep-all;
            word-wrap: break-word;
        }
        
        #footer {
            font-size: 14px;
            text-align: center;
            color: #7f8c8d;
            border-top: 1px solid #ecf0f1;
            padding-top: 20px;
            margin-top: 30px;
        }
        
        #footer a {
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            padding: 8px 16px;
            border-radius: 20px;
            background: #ecf0f1;
            display: inline-block;
            margin: 0 5px;
        }
        
        #footer a:hover {
            color: #ffffff;
            background: #3498db;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }
        
        #footer sup {
            color: #e74c3c;
            font-weight: bold;
            margin-left: 5px;
        }
        

        
        @media (max-width: 768px) {
            #msg {
                padding: 40px 20px;
                margin: 10px;
                border-radius: 12px;
            }
            
            #title {
                font-size: 38px;
                letter-spacing: 1px;
            }
            
            #title::after {
                width: 60px;
                height: 2px;
            }
            
            #body {
                font-size: 18px;
            }
            

        }
        
        @media (max-width: 480px) {
            #title {
                font-size: 30px;
                letter-spacing: 0.5px;
            }
            
            #title::after {
                width: 50px;
                height: 2px;
            }
            
            #body {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div id="msg">
        <div id="title">⚠️ 系统错误</div>
        <div id="body"><?php echo htmlspecialchars($msg);?></div>
        <div id="footer">
            <a href="http://www.lryper.com/" target="_blank" title="官方网站">RyPHP</a><sup><?php echo RYCMS_VERSION;?></sup>
        </div>
    </div>
</body>
</html>