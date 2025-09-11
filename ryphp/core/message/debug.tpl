<div id="lryphp_debug" style="<?php if(!self::$info) echo 'display:none;';?>margin:0px;padding:0px;font-size:13px;font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;line-height:1.6;text-align:left;border-top:2px solid #e74c3c;color:#2c3e50;background:#ffffff;position:fixed;_position:absolute;bottom:0;left:0;width:100%;z-index:999999;box-shadow:0 -4px 20px rgba(0,0,0,0.15);border-radius:8px 8px 0 0;">
	<div style="padding:0 20px;height:50px;line-height:50px;border-bottom:1px solid #ecf0f1;background:linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);color:#ffffff;border-radius:8px 8px 0 0;display:flex;justify-content:space-between;align-items:center;">
		<span style="font-size:15px;font-weight:500;">
			<svg style="width:16px;height:16px;vertical-align:middle;margin-right:8px;fill:currentColor;" viewBox="0 0 24 24">
				<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
			</svg>
			运行信息 ( <span style="color:#f1c40f;font-weight:bold;"><?php echo self::spent();?></span> 秒)
		</span>
		<div style="display:flex;align-items:center;">
			<span onclick="min_lryphp_debug()" style="cursor:pointer;color:#ffffff;padding:8px 12px;margin-right:8px;border-radius:4px;transition:background-color 0.2s;" title="最小化" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'" onmouseout="this.style.backgroundColor='transparent'">
				<svg style="width:14px;height:14px;fill:currentColor;" viewBox="0 0 24 24">
					<path d="M19 13H5v-2h14v2z"/>
				</svg>
			</span>
			<span onclick="close_lryphp_debug()" style="cursor:pointer;color:#ffffff;padding:8px;border-radius:4px;transition:background-color 0.2s;" title="关闭" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'" onmouseout="this.style.backgroundColor='transparent'">
				<svg style="width:14px;height:14px;fill:currentColor;" viewBox="0 0 24 24">
					<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
				</svg>
			</span>
		</div>
	</div>
	<div style="clear:both;margin:0px;padding:20px;height:250px;overflow:auto;background:#fafbfc;">
	<?php
		if(self::$info){
			echo '<div style="margin-bottom:15px;font-weight:600;color:#e74c3c;font-size:14px;border-left:3px solid #e74c3c;padding-left:10px;">［系统信息］</div>';
			foreach(self::$info as $info){
				echo '<div style="padding:8px 15px;margin:5px 0;background:#ffffff;border-radius:4px;border-left:3px solid #3498db;box-shadow:0 1px 3px rgba(0,0,0,0.1);font-family:\'Consolas\', \'Monaco\', \'Courier New\', monospace;font-size:12px;">'.$info.'</div>';
			}
		}
		if(self::$sqls) {
			echo '<div style="margin:20px 0 15px 0;font-weight:600;color:#e74c3c;font-size:14px;border-left:3px solid #e74c3c;padding-left:10px;">［SQL语句］</div>';
			foreach(self::$sqls as $sql){
				echo '<div style="padding:8px 15px;margin:5px 0;background:#ffffff;border-radius:4px;border-left:3px solid #27ae60;box-shadow:0 1px 3px rgba(0,0,0,0.1);font-family:\'Consolas\', \'Monaco\', \'Courier New\', monospace;font-size:12px;word-break:break-all;">'.$sql.'</div>';
			}
		}
		if(self::$request) {
			echo '<div style="margin:20px 0 15px 0;font-weight:600;color:#e74c3c;font-size:14px;border-left:3px solid #e74c3c;padding-left:10px;">［REQUEST请求］</div>';
			foreach(self::$request as $qe){
				$method = $qe['data'] ? 'POST' : 'GET';
				$methodColor = $qe['data'] ? '#e67e22' : '#3498db';
				$data = $qe['data'] ? '<span style="color:#e67e22;margin:0 5px;font-weight:500;">parameter：'.var_export($qe['data'], true).'</span>' : '';
				echo '<div style="padding:8px 15px;margin:5px 0;background:#ffffff;border-radius:4px;border-left:3px solid '.$methodColor.';box-shadow:0 1px 3px rgba(0,0,0,0.1);font-family:\'Consolas\', \'Monaco\', \'Courier New\', monospace;font-size:12px;"><span style="color:'.$methodColor.';font-weight:bold;">'.$method.'</span>：'.$qe['url'].$data.'</div>';
			}
		}		
		echo '<div style="margin:20px 0 15px 0;font-weight:600;color:#e74c3c;font-size:14px;border-left:3px solid #e74c3c;padding-left:10px;">［其他信息］</div>';
		echo '<div style="padding:8px 15px;margin:5px 0;background:#ffffff;border-radius:4px;border-left:3px solid #9b59b6;box-shadow:0 1px 3px rgba(0,0,0,0.1);font-size:12px;"><strong>服务器信息：</strong>'.$_SERVER['SERVER_SOFTWARE'].'</div>';
		echo '<div style="padding:8px 15px;margin:5px 0;background:#ffffff;border-radius:4px;border-left:3px solid #9b59b6;box-shadow:0 1px 3px rgba(0,0,0,0.1);font-size:12px;"><strong>路由信息：</strong>模块( <span style="color:#3498db;">'.ROUTE_M.'</span> )，控制器( <span style="color:#3498db;">'.ROUTE_C.'</span> )，方法( <span style="color:#3498db;">'.ROUTE_A.'</span> )，参数( <span style="color:#3498db;">'.$parameter.'</span> )</div>';
		if(session_id()) {
			echo '<div style="padding:8px 15px;margin:5px 0;background:#ffffff;border-radius:4px;border-left:3px solid #9b59b6;box-shadow:0 1px 3px rgba(0,0,0,0.1);font-size:12px;"><strong>会话信息：</strong>'.session_name().' = <span style="color:#e67e22;">'.session_id().'</span></div>';
		}
		echo '<div style="padding:8px 15px;margin:5px 0;background:#ffffff;border-radius:4px;border-left:3px solid #9b59b6;box-shadow:0 1px 3px rgba(0,0,0,0.1);font-size:12px;"><strong>框架版本：</strong>'.RYPHP_VERSION.' <a href="http://www.lryper.com" target="_blank" style="color:#3498db;text-decoration:none;margin-left:10px;padding:2px 8px;background:#ecf0f1;border-radius:3px;transition:background-color 0.2s;" onmouseover="this.style.backgroundColor=\'#bdc3c7\'" onmouseout="this.style.backgroundColor=\'#ecf0f1\'">查看新版</a></div>';
	?>
	</div>
</div>
<div id="lryphp_open" onclick="show_lryphp_debug()" title="查看详细" style="<?php if(self::$info) echo 'display:none;';?>height:40px;line-height:40px;border-radius:20px 0 0 20px;z-index:999998;font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;float:right;text-align:center;overflow:hidden;position:fixed;_position:absolute;bottom:20px;right:0;background:linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);color:#ffffff;font-size:13px;padding:0 15px;cursor:pointer;box-shadow:0 4px 15px rgba(231,76,60,0.3);transition:all 0.3s ease;font-weight:500;" onmouseover="this.style.transform='translateX(-5px)';this.style.boxShadow='0 6px 20px rgba(231,76,60,0.4)'" onmouseout="this.style.transform='translateX(0)';this.style.boxShadow='0 4px 15px rgba(231,76,60,0.3)'">
	<svg style="width:16px;height:16px;vertical-align:middle;margin-right:5px;fill:currentColor;" viewBox="0 0 24 24">
		<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
	</svg>
	<?php echo self::spent();?>s
</div>	
<script type="text/javascript">
	function show_lryphp_debug(){
		document.getElementById('lryphp_debug').style.display = 'block';
		document.getElementById('lryphp_open').style.display = 'none';
	}
	function min_lryphp_debug(){
		document.getElementById('lryphp_debug').style.display = 'none';
		document.getElementById('lryphp_open').style.display = 'block';
	}
	function close_lryphp_debug(){
		document.getElementById('lryphp_debug').style.display = 'none';
		document.getElementById('lryphp_open').style.display = 'none';
	}
</script>
