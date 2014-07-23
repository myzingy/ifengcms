$(document).ready(function(){
	if ($("#tablesorter_product").length != 0){
		$('#tablesorter_product').dataTable({
			"oLanguage":{ 
				"sProcessing": "数据加载中...", 
				"sLengthMenu": "显示_MENU_条 ", 
				"sZeroRecords": "没有您要搜索的内容", 
				"sInfo": "从 _START_ 到 _END_ 条记录—总记录数为 _TOTAL_ 条", 
				"sInfoEmpty": "记录数为0", 
				"sInfoFiltered": "(全部记录数 _MAX_  条)", 
				"sInfoPostFix": "", 
				"sSearch": "搜索", 
				"sUrl": "", 
				"oPaginate": { 
				"sFirst":    "首页", 
				"sPrevious": " 上一页 ", 
				"sNext":     " 下一页 ", 
				"sLast":     " 尾页 " 
				} 
			}, 
			"aaSorting": [[ 0, "desc" ]], 
			"bSort": true, 
			"bProcessing": true, 
			"sPaginationType": "full_numbers"//, 
			//"sAjaxSource": 'json_source.txt'
		});
	}
});