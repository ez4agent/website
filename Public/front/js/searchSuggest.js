$(function(){
		$("#suggest_ul").hide();
});


$(function(){
					 
	$("#suggest_input").keyup(function(){
		function createLink(){
			if(window.ActiveXObject){
				var newRequest = new ActiveXObject("Microsoft.XMLHTTP");
			}else{
				var newRequest = new XMLHttpRequest();
			}
			return newRequest;
		}
		if($("#suggest_input").val().length==0){
			$("#suggest_ul").hide();
			return;
		}
		http_request = createLink();
		if(http_request){
			var sid = $("#suggest_input").val();
			var url = Search_url;
			var data = "keywords="+sid;
			http_request.open("post",url,true);
			http_request.setRequestHeader("content-type","application/x-www-form-urlencoded");
			http_request.onreadystatechange = dealresult;
			http_request.send(data);
		}
		function dealresult(){
		if(http_request.readyState==4){
			if(http_request.status==200){
				if(http_request.responseText=="no"){
					$("#suggest_ul").hide();
					return;
					
				}
				$("#suggest_ul").show();
				var res = eval("("+http_request.responseText+")");
				var contents="";
				for(var i=0;i<res.length;i++){
					var keywords = res[i].keywords;
					contents=contents+"<li class='suggest_li"+(i+1)+"'>"+keywords+"</li>";
						
				}
				$("#suggest_ul").html(contents);
			}
		}
	}
		
		
	});
	
$(function(){
		
	$("#suggest_input").keyup(function(){
		setInterval(changehover,300);
		function changehover(){
			$("#suggest_ul li").hover(function(){ $(this).css("background","#eee");},function(){ $(this).css("background","#fff");});
			
			$("#suggest_ul li").click(function(){ $("#suggest_input").val($(this).html());});
			$("#suggest_ul li").click(function(){ $("#suggest_ul").hide(); });
		}
	});
	
	});

});