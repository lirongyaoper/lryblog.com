<!DOCTYPE html>
<html lang="zh-CN">	
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <?php if(!$stop){?>
    <meta http-equiv="refresh" content="<?php echo $limittime;?>;URL=<?php echo $gourl;?>" />
    <?php }?>
    <title>RyPHP<?php echo L('message_tips');?></title>
    <link rel="Shortcut Icon" href="<?php echo STATIC_URL;?>admin/lry_admin/images/favicon.ico" />
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #2c3e50;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .lry-msg {
            max-width: 500px;
            width: 100%;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
            overflow: hidden;
            animation: slideInUp 0.6s ease-out;
            position: relative;
        }
        
        .lry-msg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #27ae60, #2ecc71, #3498db, #9b59b6);
            background-size: 200% 100%;
            animation: rainbow 3s ease-in-out infinite;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
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
        
        .lry-msg-title {
            height: 60px;
            line-height: 60px;
            color: #ffffff;
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            position: relative;
        }
        
        .lry-msg-title::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 20px;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23ffffff"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>') no-repeat center;
            background-size: contain;
        }
        
        .lry-msg-body {
            padding: 30px;
            text-align: center;
        }
        
        .lry-info {
            margin-bottom: 25px;
            word-break: break-all;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #27ae60;
            font-size: 15px;
            line-height: 1.6;
            color: #495057;
        }
        
        .lry-msg-body p {
            font-size: 14px;
            color: #6c757d;
            margin: 15px 0;
        }
        
        .lry-msg-body p a {
            font-size: 14px;
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 25px;
            background: #ecf0f1;
            display: inline-block;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .lry-msg-body p a:hover {
            color: #ffffff;
            background: #3498db;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }
        
        .countdown {
            color: #e74c3c;
            font-weight: bold;
            margin: 0 5px;
            font-size: 16px;
        }
        
        .success-icon {
            display: inline-block;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            border-radius: 50%;
            margin-bottom: 20px;
            position: relative;
            animation: bounce 2s ease-in-out infinite;
        }
        
        .success-icon::before {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
        
        .progress-bar {
            width: 100%;
            height: 4px;
            background: #ecf0f1;
            border-radius: 2px;
            margin: 20px 0;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #27ae60, #2ecc71);
            border-radius: 2px;
            transition: width 1s linear;
            animation: progress 1s ease-out;
        }
        
        @keyframes progress {
            from {
                width: 0%;
            }
            to {
                width: 100%;
            }
        }
        
        @media (max-width: 768px) {
            .lry-msg {
                margin: 10px;
                border-radius: 12px;
            }
            
            .lry-msg-title {
                height: 50px;
                line-height: 50px;
                font-size: 16px;
            }
            
            .lry-msg-body {
                padding: 20px;
            }
            
            .lry-info {
                padding: 15px;
                font-size: 14px;
            }
            
            .success-icon {
                width: 40px;
                height: 40px;
            }
            
            .success-icon::before {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="lry-msg">        	
        <div class="lry-msg-title"><?php echo L('message_tips');?></div>
        <div class="lry-msg-body">
            <div class="success-icon"></div>
            <div class="lry-info"><?php echo $msg;?></div>
            <?php if(!$stop){?>
            <div class="progress-bar">
                <div class="progress-fill" style="width: 0%;" id="progress"></div>
            </div>
            <p>本页面将在 <span class="countdown" id="countdown"><?php echo $limittime;?></span> 秒后跳转...</p>
            <?php }else{?>
            <p><a href="<?php echo htmlspecialchars(HTTP_REFERER); ?>" title="<?php echo L('click_return');?>"><?php echo L('click_return');?></a></p>
            <?php }?>
        </div>
    </div>
    
    <?php if(!$stop){?>
    <script type="text/javascript">
        (function() {
            var countdown = <?php echo $limittime;?>;
            var countdownElement = document.getElementById('countdown');
            var progressElement = document.getElementById('progress');
            var totalTime = countdown;
            
            function updateCountdown() {
                if (countdown > 0) {
                    countdownElement.textContent = countdown;
                    var progress = ((totalTime - countdown) / totalTime) * 100;
                    progressElement.style.width = progress + '%';
                    countdown--;
                    setTimeout(updateCountdown, 1000);
                }
            }
            
            updateCountdown();
        })();
    </script>
    <?php }?>
</body>
</html>