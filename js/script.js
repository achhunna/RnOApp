var nonEditMode = "off";

$(document).ready(function($){
	
	$("#userOption").change(function(){
		optionQuery();
	});
	$("#fcstOption").change(function(){
		optionQuery();
	});
	
	//project search filter
	$("#projectSearch").bind("keyup click", function(){
		var data = this.value.split();
		var title = $("#outputTable").find("input[name*='title']");
		//Recusively filter the jquery object to get results
		var countNum = 1;
		title.filter(function(i,v){
			var $t = $(this);
			for (var d = 0; d < data.length; d++){
				if($t.val().toLowerCase().indexOf(data[d].toLowerCase()) <= -1){
					var rowNum = $t.attr("id").split("_").pop();
					$("#tr_"+rowNum).hide();
					//return true;
				}else{
					$("#tr_"+countNum).show();
				}
			}
			countNum +=1;
			//return false;
		});
		calcSubtotal();
		alternate("outputTable");
	}).focus(function(){
		if(this.value == "Search project"){
			this.value = "";
		}
	});
	
	$("#fiscalOption").change(function(){
		var data = $(this).val();
		var fiscal= $("#outputTable").find("select[name*='fiscal']");
		
		//Recusively filter the jquery object to get results
		var countNum = 1;
		fiscal.filter(function(i,v){
			var $t = $(this);
			if($t.val() != data){
				var rowNum = $t.attr("id").split("_").pop();
				$("#tr_"+rowNum).hide();
				//return true;
			}else{
				$("#tr_"+countNum).show();
			}
			countNum +=1;
			//return false;
		});
		calcSubtotal();
	});
	optionQuery();
});
$(window).load(jqueryFunctions); 

function calcFullyear(id){
	var fyTotal = 0;
	for(i=1; i<5; i++){
		fyTotal += parseFloat(document.getElementById('q'+i+'_'+id).value.split(',').join(''));
	}
	document.getElementById('fy_'+id).value = fyTotal;
}
function calcSubtotal(){
	var q1 = $("#outputTable").find("input[name*='q1']");
	var q2 = $("#outputTable").find("input[name*='q2']");
	var q3 = $("#outputTable").find("input[name*='q3']");
	var q4 = $("#outputTable").find("input[name*='q4']");	
	
	var q1Total = q2Total = q3Total = q4Total = fyTotal = 0;
	q1.filter(function(i,v){
		if($(this).is(":visible")){
			var rowNum = $(this).attr("id").split("_").pop();
			q1Total += parseFloat($(this).val().replace(",",""));
			q2Total += parseFloat($("#q2_"+rowNum).val().replace(",",""));
			q3Total += parseFloat($("#q3_"+rowNum).val().replace(",",""));
			q4Total += parseFloat($("#q4_"+rowNum).val().replace(",",""));
			fyTotal = q1Total + q2Total + q3Total + q4Total;
		}
	});
	$("#q1Total").text(formatNumber(q1Total));
	$("#q2Total").text(formatNumber(q2Total));
	$("#q3Total").text(formatNumber(q3Total));
	$("#q4Total").text(formatNumber(q4Total));
	$("#fyTotal").text(formatNumber(fyTotal));
}
function formatNumber (num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
}
function disableButtons(bool){
	var els = document.getElementsByTagName('input');
	for(var i = 0; i < els.length; i++){
		if(els[i].type == 'button'||els[i].type == 'submit'){
			if(bool == 'true'){
				els[i].setAttribute('disabled', bool);
			}else{
				els[i].removeAttribute('disabled');
			}
		}
	}
}

function edit(id,fields,outputCount){
	if(nonEditMode == "off"){
		disableButtons('true');
		document.getElementById("submitButton_"+id).disabled = false;
		for(j=1; j<Number(outputCount)+1; j++){
			if(j==id){
				for(i=0; i<fields.length; i++){
					if(fields[i] == "account_"){
						document.getElementById(fields[i]+id).disabled = false;
					}else if(fields[i] == "fiscal_"){
						document.getElementById(fields[i]+id).disabled = false;
					}else{
						document.getElementById(fields[i]+id).readOnly = false;
					}
				}
			}else{
				for(i=0; i<fields.length; i++){
					document.getElementById(fields[i]+j).readOnly = true;
				}
			}
		}
		nonEditMode = "on";
	}else if(nonEditMode == "on"){
		disableButtons('false');
		for(i=0; i<fields.length; i++){
			if(fields[i] == "account_"){
				document.getElementById(fields[i]+id).disabled = true;
			}else if(fields[i] == "fiscal_"){
				document.getElementById(fields[i]+id).disabled = false;
			}else{
				document.getElementById(fields[i]+id).readOnly = true;
			}
		}
		nonEditMode = "off";
	}
}

function alternate(table){
	 if(document.getElementsByTagName){  
		var table = document.getElementById(table);   
		var rows = table.getElementsByTagName("tr");
		//Assign counter
		var counter = false;
		for(i = 0; i < rows.length; i++){           
			//check for display
			if(rows[i].style.display != "none"){
				if(counter){ 
					rows[i].className = "even";
					counter = false;
				}else{ 
					rows[i].className = "odd";
					counter = true;
				}
			}
		}
	} 
}
function searchQuery(){
	var userOption = $("#userOption").val();
	var fcstOption = $("#fcstOption").val();
	var projectTerm = $("#projectSearch").val();
	$.ajax({
		type:"post",
		url:"ajax.php",
		data:"userOption=" + userOption + "&fcstOption=" + fcstOption + "&projectTerm=" + projectTerm + "&action=updateDisplay",
		async:true,
		success:function(data){
			$("#outputTable").html(data);
			alternate("outputTable");
		}
	});
}
function optionQuery(){
	$("#projectSearch").val("Search project");
	var userOption = $("#userOption").val();
	var fcstOption = $("#fcstOption").val();

	$.ajax({
		type:"post",
		url:"ajax.php",
		data:"userOption=" + userOption + "&fcstOption=" + fcstOption + "&action=updateDisplay",
		async:true,
		success:function(data){
			$("#outputTable").html(data);
			alternate("outputTable");
			nonEditMode = "off"; //reset edit mode
		}
	});
}
function jqueryFunctions(){
	//title autocomplete jQuery
	$("input[name*='title']").autocomplete({
		source: 'autocomplete.php?field=title&',
		minLength: 1,
	});
	
	//bu autocomplete jQuery
	$("input[name*='bu']").autocomplete({
		source: 'autocomplete.php?field=bu&',
		minLength: 1,
	});
	
	//dept autocomplete jQuery
	$("input[name*='dept']").autocomplete({
		source: 'autocomplete.php?field=dept&',
		minLength: 1,
	});
	
	//notes autocomplete jQuery
	$("input[name*='notes']").autocomplete({
		source: 'autocomplete.php?field=notes&',
		minLength: 1,
	});
	
	//button ajax functions
	$("input[name*='deleteButton']").click(function(){
		if(confirm("Are you sure you want to delete?")){
			var id = "_"+this.id.split("_").pop();
			var idKey = $("#idKey"+id).val();
			
			$.ajax({
				type: "post",
				url: "ajax.php",
				data: "&idKey=" + idKey + "&action=delete",
				success:function(data){
					//location.reload();
					searchQuery();
				}
			});
		}
	});
	$("input[name*='submitButton']").click(function(){
	
		var id = "_"+this.id.split("_").pop();
		var user = $("#user"+id).val();
		var fcst = $("#fcst"+id).val();
		var fiscal = $("#fiscal"+id).val();
		var title = $("#title"+id).val();
		var bu = $("#bu"+id).val();
		var dept = $("#dept"+id).val();
		var account = $("#account"+id).val();
		var notes = $("#notes"+id).val();
		var q1 = $("#q1"+id).val();
		var q2 = $("#q2"+id).val();
		var q3 = $("#q3"+id).val();
		var q4 = $("#q4"+id).val();
		var fy = $("#fy"+id).val();
		var targetchange = $("#targetchange"+id).val();
		var idKey = $("#idKey"+id).val();

		if((title.length && bu.length && dept.length && notes.length) > 0 && (targetchange.valueOf() == "Y" || targetchange.valueOf() == "N") ){
			$.ajax({
				type: "post",
				url: "ajax.php",
				data: "user=" + user + "&fcst=" + fcst + "&fiscal=" + fiscal + "&title=" + encodeURIComponent(title) + "&bu=" + bu + "&dept=" + dept + "&account=" + encodeURIComponent(account) + "&notes=" + encodeURIComponent(notes) + "&q1=" + q1 + "&q2=" + q2 + "&q3=" + q3 + "&q4=" + q4 + "&fy=" + fy + "&targetchange=" + targetchange + "&idKey=" + idKey + "&action=post",
				success:function(data){
					nonEditMode = "off";
					searchQuery();
				}
			});
		}
	});

}