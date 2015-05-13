/*
 *	日程计划
 *	
 *  作者：syl
 *
 */

  $(document).ready(function(){

  	  

  	  $("#sub_events_act").click(function(){ 

  	  	  var title = $("#title").val();
  	  	  var date = $("#date").val();
  	  	  var stu_id = $("#stu_id1").val();
  	  	  
  	  	  if(!title)
  	  	  { 
  	  	  	 alert('请输入主题！');
  	  	  	 return false;
  	  	  }

  	  	  if(!stu_id)
  	  	  { 
  	  	  	 alert('请选择学生！');
  	  	  	 return false;
  	  	  }

  	  	  if(!date)
  	  	  { 
  	  	  	alert('请输入日期！');
  	  	  	return false;
  	  	  }
  	  	  else
  	  	  { 
  	  	  	if(!RQcheck(date))
  	  	  	{ 
  	  	  		alert('请输入正确的日期,例如：1970/01/01');
  	  	  		return false;
  	  	  	}
  	  	  }

  	  	  var data = $("#event_form").serialize(); 
  	  	  var url =$("#events_url").val();

  	  	  ajax_submit(url,data);

  	  });

  	  //已完成未完成的操作
  	  $(".is_use").click(function(){ 

  	  		var id = $(this).attr('data_attr');
  	  		var url = $(this).attr('uri');

  	  		if(id)
  	  		{
              if(confirm('您确定已经完成！'))
              { 
      	  			$.post(url,{id:id},function(data){ 
      	  				if(data.status=='yes')
      	  				{ 
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
  	  		}
  	  		else
  	  		{ 
  	  			return false;
  	  		}
  	  });

  	  //学生选择
  	  $("#stuid").change(function(){ 

  	  		var myurl=new objURL(window.location.href);
			var value = $("#stuid").val();
			if(value>0)
			{
		    	myurl.set('stuid',value);
		    }
		    else
		    { 
		    	myurl.remove('stuid');
		    }
		    //alert(myurl.url());
		    window.location.href=myurl.url();
  	  });
  });



  