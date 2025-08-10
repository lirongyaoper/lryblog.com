/**
 *  RyPHP 文章顶踩插件
 *	lryper.com
 *  作者：李荣耀
 *  @lastmodify		2018-11-15
 */
 
function digg(url, mid, cid, d){
	var saveid = GetCookie('diggid');
	if (saveid == cid) {
		// 使用更友好的提示
		if(typeof layer !== 'undefined') {
			layer.msg("您太激动啦，小手有点颤抖哦", {icon: 2, time: 2000});
		} else {
			alert("您太激动啦，小手有点颤抖哦");
		}
	} else{
		// 添加点击动画效果
		var buttonId = d ? 'up' : 'down';
		var $button = $('#' + buttonId);
		var $container = $button.closest('div');
		
		// 添加点击动画
		$container.addClass('clicked');
		setTimeout(function() {
			$container.removeClass('clicked');
		}, 600);
		
		// 显示加载状态
		var originalText = $button.html();
		$button.html('<i class="fas fa-spinner fa-spin"></i>');
		$button.css('opacity', '0.7');
		
		$.ajax({
			type: 'POST',
			url: url, 
			data: 'modelid='+mid+'&id='+cid+'&digg='+d,
			dataType: "json", 
			success: function (msg) {
				if(msg.status == 1){
					// 成功动画
					$button.html(msg.message);
					$button.css('opacity', '1');
					
					// 成功提示
					var successMsg = d ? '点赞成功！' : '踩踩成功！';
					if(typeof layer !== 'undefined') {
						layer.msg(successMsg, {icon: 1, time: 1500});
					}
					
					// 数字变化动画
					animateNumber($button, originalText, msg.message);
				}else{
					// 恢复原始状态
					$button.html(originalText);
					$button.css('opacity', '1');
					
					if(typeof layer !== 'undefined') {
						layer.msg(msg.message, {icon: 2, time: 2000});
					} else {
						alert(msg.message);
					}
				}
			},
			error: function() {
				// 错误处理
				$button.html(originalText);
				$button.css('opacity', '1');
				
				if(typeof layer !== 'undefined') {
					layer.msg('网络错误，请重试', {icon: 2, time: 2000});
				} else {
					alert('网络错误，请重试');
				}
			}
		})
		
		SetCookie('diggid', cid, 1);
		return true;				  
	}
}

// 数字变化动画
function animateNumber($element, from, to) {
	var fromNum = parseInt(from) || 0;
	var toNum = parseInt(to) || 0;
	var duration = 800;
	var startTime = Date.now();
	
	function update() {
		var elapsed = Date.now() - startTime;
		var progress = Math.min(elapsed / duration, 1);
		
		// 使用缓动函数
		var easeOut = 1 - Math.pow(1 - progress, 3);
		var currentNum = Math.round(fromNum + (toNum - fromNum) * easeOut);
		
		$element.html(currentNum);
		
		if (progress < 1) {
			requestAnimationFrame(update);
		}
	}
	
	update();
}
 
function GetCookie(c_name){
    if (document.cookie.length > 0){
        c_start = document.cookie.indexOf(c_name + "=")
        if (c_start != -1){
            c_start = c_start + c_name.length + 1;
            c_end   = document.cookie.indexOf(";",c_start);
            if (c_end == -1){
                c_end = document.cookie.length;
            }
            return unescape(document.cookie.substring(c_start,c_end));
        }
    }
    return null
}
 
function SetCookie(c_name, value, expiredays){
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + expiredays);
    document.cookie = c_name + "=" +escape(value) + ((expiredays == null) ? "" : ";expires=" + exdate.toGMTString()); 
}

// SITE: lryper.com
