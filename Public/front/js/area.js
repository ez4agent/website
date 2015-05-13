/*
	CRM系统--（国家、省州、城市联动）
*/

$(document).ready(function(){
						   
	//点击国家
	$('#country_id').change(function(){
	
		var country_id = $("#country_id").val();
		$('#area_id').empty();
		$('#city_id').empty();
		$('#area_id').append("<option value='0'>==请选择==</option>");
		$('#city_id').append("<option value='0'>==请选择==</option>");
		get_area(country_id,0);
	});
	//点击省/州
	$('#area_id').change(function(){
		var area_id = $("#area_id").val();
		$('#city_id').empty();
		get_city(area_id,0);
	})					   
});


function get_area(country_id,select_area_id)
{
	if(country_id){
		
		$.post( area_url,{country_id:country_id,select_area_id:select_area_id},function(result){
   			 if(result.status=='ok')
			 {
				 $('#area_id').empty();
				 $("#area_id").append(result.info);
			 }
			 else
			 {
				 return false;
			 }
  		});
		
	}else{
		
		return false;
	}
}

function get_city(area_id,city_select_id)
{
	if(area_id){
		
		$.post( city_url,{area_id:area_id,city_select_id:city_select_id},function(result){
   			 if(result.status=='ok')
			 {
				 $('#city_id').empty();
				 $("#city_id").append(result.info);
			 }
			 else
			 {
				 return false;
			 }
  		});
		
	}else{
		
		return false;
	}
}