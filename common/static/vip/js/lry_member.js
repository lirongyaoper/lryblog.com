/**
 *  RyPHP-荣耀小科技
 *  lryper.com
 */

layui.use('element', function(){
	var element = layui.element;
});



//from表单
layui.use(['form', 'layedit', 'laydate'], function(){
	var form = layui.form
	,layer = layui.layer
	,layedit = layui.layedit
	,laydate = layui.laydate;

	//日期
	laydate.render({
	elem: '#date'
	});


	form.on('select(lry_problem)', function(data){
		if(data.value != 0){
		   $("#answer").removeAttr("style");
		}else{
		  $("#answer").css('display','none');
		  form.render('select');
		}
	});
  
  
	form.on('select(lry_category)', function(data){
		var catid = data.value;
		if(catid != 0) location.href = '?catid='+catid;
	});

});

function lry_del(url){
	layer.confirm('确认要删除吗？',function(index){
		window.location.href = url;
	});
}