/*
 * 站内信操作
 */


 $(function() { 


	//全选
	$('.checkall').click(function(){
        $('.checkitem').prop('checked', this.checked)
    });


 	//批量删除
 	$("#Batch_del").click(function(){ 

		if($('.checkitem:checked').length == 0){    //没有选择
              alert('请选择要删除的选项！');
			  return false;
        }
		else
		{ 
			if(confirm("您确定要删除吗！"))
			{ 
				 var items = '';
            		$('.checkitem:checked').each(function(){
                	items += this.value + ',';
           		 });
				 items = items.substr(0, (items.length - 1));
				 if(items)
				 { 
				 	var url = $("#batch_del_url").val();
				 	$.post(url,{msm_id:items,type:type},function(data){
						if(data.status=='yes')
						{
							alert('删除成功！');
							window.location.reload();
							return true;
						}
						else
						{
							alert(data.msg);
							return false;
						}	
					},'json');
				 }
				 else
				 { 
				 	alert('请选择要删除的选项！');
					return false;
				 }
				 
			}
		}

 	});


 	//发站内信
 	$("#send_act").click(function(){ 

 		//收信人
 		var to = $("#to").val();
 		if(!to)
 		{ 
 			alert('请填写收件人！');
 			return false;
 		}

 		var email_title = $("#email_title").val();
 		if(!email_title || email_title=='')
 		{ 
 			alert('请填写主题！');
 			return false;
 		}

 		//submint提交
 		var url = $("#send_letter_url").val();
 		var data = $("#send_letter").serialize();

 		ajax_submit(url,data);
 	});

 })



//删除文件
function del_file(url)
{ 
	$("#file_from li").each(function (i) {
		var This = $(this);
		This.click(function () {
			var file_del_url = $("#file_del").val(); 
			$.post(file_del_url,{url:url},function(data){ 
				if(data.status=='yes')
				{ 
					This.remove();
				}
			},'json');
		});
	});
}



