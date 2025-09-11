<!DOCTYPE html>
<html lang="zh-CN">	
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>系统发生错误</title>
	<link rel="Shortcut Icon" href="<?php echo STATIC_URL;?>admin/lry_admin/images/favicon.ico" />
	<style type="text/css">
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}
		
		body {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
			color: #2c3e50;
			line-height: 1.6;
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 20px;
		}
		
		::selection {
			background-color: #e74c3c;
			color: white;
		}
		
		::-moz-selection {
			background-color: #e74c3c;
			color: white;
		}
		
		#container {
			background: #ffffff;
			border-radius: 12px;
			box-shadow: 0 20px 40px rgba(0,0,0,0.1);
			overflow: hidden;
			max-width: 600px;
			width: 100%;
			animation: slideIn 0.6s ease-out;
		}
		
		@keyframes slideIn {
			from {
				opacity: 0;
				transform: translateY(30px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}
		
		h1 {
			background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
			color: #ffffff;
			font-size: 24px;
			font-weight: 600;
			margin: 0;
			padding: 25px 30px;
			text-align: center;
			position: relative;
		}
		
		h1::before {
			content: '';
			position: absolute;
			top: 50%;
			left: 30px;
			transform: translateY(-50%);
			width: 24px;
			height: 24px;
			background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23ffffff"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>') no-repeat center;
			background-size: contain;
		}
		
		#body {
			padding: 30px;
		}
		
		.error-message {
			background: #f8f9fa;
			border-left: 4px solid #e74c3c;
			padding: 15px 20px;
			margin: 20px 0;
			border-radius: 0 6px 6px 0;
			font-size: 14px;
			color: #495057;
		}
		
		.error-details {
			background: #2c3e50;
			color: #ecf0f1;
			font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
			font-size: 13px;
			padding: 20px;
			border-radius: 6px;
			margin: 20px 0;
			overflow-x: auto;
			white-space: pre-wrap;
			word-break: break-word;
			line-height: 1.5;
		}
		
		.error-type {
			display: inline-block;
			background: #e74c3c;
			color: #ffffff;
			padding: 4px 12px;
			border-radius: 20px;
			font-size: 12px;
			font-weight: 600;
			margin-bottom: 15px;
			text-transform: uppercase;
			letter-spacing: 0.5px;
		}
		
		p.footer {
			text-align: center;
			font-size: 13px;
			border-top: 1px solid #ecf0f1;
			line-height: 60px;
			padding: 0 30px;
			margin: 0;
			color: #7f8c8d;
		}
		
		p.footer a {
			color: #3498db;
			text-decoration: none;
			font-weight: 600;
			transition: color 0.2s;
		}
		
		p.footer a:hover {
			color: #2980b9;
		}
		
		@media (max-width: 768px) {
			#container {
				margin: 10px;
				border-radius: 8px;
			}
			
			h1 {
				font-size: 20px;
				padding: 20px;
			}
			
			#body {
				padding: 20px;
			}
			
			.error-details {
				font-size: 12px;
				padding: 15px;
			}
		}
	</style>
</head>
<body>
	<div id="container">
		<h1><?php echo $type==1 ? 'PHP' : 'MySQL';?> FatalError!</h1>
		<div id="body">
			<div class="error-type"><?php echo $type==1 ? 'PHP' : 'MySQL';?> 错误</div>
			<div class="error-message">
				<strong>错误信息：</strong><br>
				<?php echo $msg;?>
			</div>
			<div class="error-details"><?php echo $detailed ? $detailed : $msg;?></div>
		</div>
		<p class="footer">Powered by <a href="http://www.lryper.com/" target="_blank">RyPHP</a> Version <strong><?php echo RYPHP_VERSION;?></strong></p>
	</div>
</body>
</html>