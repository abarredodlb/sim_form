//jQuery time
var current_fs, next_fs, previous_fs; //fieldsets
var left, opacity, scale; //fieldset properties which we will animate
var animating; //flag to prevent quick multi-click glitches


function isNumberKey(evt)

{

	var charCode = (evt.which) ? evt.which : evt.keyCode;
	if (charCode != 46 && charCode > 31
			&& (charCode < 48 || charCode > 57))
		return false;
	return true;
}


function getFormData($form){
	var unindexed_array = $form.serializeArray();
	var indexed_array = {};

	$.map(unindexed_array, function(n, i){
		indexed_array[n['name']] = n['value'];
	});

	return indexed_array;
}




$(document).ready(function() {
	//console.log("ddd");
	$("#date").datepicker({
		minDate: 0,
		beforeShowDay: function(date) {
				       var day = date.getDay();
				       return [(day != 0), ''];
			       }

	});
	$( "#date" ).datepicker("setDate", new Date());
        $('#date').bind('keypress', function(e) {
                e.preventDefault();
        });

	// $("#fnext").attr('disabled', true);

});


function clearVal(i){
	//console.log("Clearval");

	document.getElementById(i).value = null;
	//        document.getElementById('pass2').value = null;
	//	               document.getElementById('visa').value = null;

}


function checkSerialno(e){
//	console.log("Inside func");
	$("#name_status").html('');
	setTimeout(check_num,5000);
//	check_num(e);
}
function check_num(e){
	$('#details').html('');
	//console.log("Key pressed");
	var number = document.getElementById("serialNo").value;
//	console.log(number);

	if(number)
	{	
		$.ajax({
			type: 'post',
			url: 'checkvalue.php',
			dataType: "json",	
			data: { serial_no : number  },		
			success : function(response)
		{
	//		$("#name_status").html(response.msg);
			//console.log("Treu Response "+response.msg);
			if(response.code==200)	
		{
			//console.log(response.code);
			$("#name_status").html('');
			return true;
		}
			else
		{
			//console.log("Cheeee  "+response.code);	     
			$("#name_status").html(response.msg); 
			return false;	
		}
		}	

		});	

	}
	else
	{
		$( '#name_status' ).html("");
		return false;		       
	}



}


$("#fnext").click(function(e){
	e.preventDefault();
	var number = document.getElementById("serialNo").value;
	//var invalid = false;

	if(number.length>=5)
{
	var invalid = false;

	$.ajax({
		type: 'post',
		url: 'checkvalue.php',
		dataType: "json",
		async:false,
		data: { serial_no : number  },
		success : function(response)
	{
	//	$("#name_status").html(response.msg);
		//		console.log("Treu Response "+response.code);
		//	var str = "OK";
		if(response.code==200)
	{
		
		//console.log(response.code);
	//	console.log(response.msg);
		if(response.msg==='usa')
		{
	//	console.log("Its in usa");	
		var ht = "<b>Zip code (Optional)</b>  <input type='text' name='zipcode' id='zipcode'  placeholder='Zip code' value='' />";	
		$('#zipcodes').html(ht);	
		}
		else
		{
	//	 console.log("Its Not in usa");
		$('#zipcodes').html(''); 
		}
		return true;
	}
		else if(response.code==203)
	{
		//console.log(response.code);
		//console.log(response.msg);
		//console.log(response.msg['emailadd']);
		var value = "<h3>Your Activation Record is already with us</h3><br>";
		value +="<h5><b>Sim Serial Number : </b>"+response.msg['sim_phone_no']+"</h5>";
		value +="<h5><b>Name : </b>"+response.msg['clientname']+"</h5>";
		value +="<h5><b>Email : </b>"+response.msg['emailadd']+"</h5>";
		value +="<h5><b>Activation Date : </b>"+response.msg['from_date'].replace(' 00:00:00','')+"</h5>";
		if(response.msg['add_pinno'])
		{	
		 value +="<h5><b>ZIP Code : </b>"+response.msg['add_pinno']+"</h5>";
		}
		value +="<h5><b>Your Contact Number : </b>"+response.msg['mobno']+"</h5>";	
		$('#details').html(value);
		invalid = true;
		return false;
	}
		else
		{
		//	console.log("response code  "+response.code);
			$("#name_status").html(response.msg);
			invalid = true;
			return false;

		}

	}

	});
	if(invalid)
	{
		return false;
	}		

}
else
{
	$( '#name_status' ).html("El número de serie no es válido");
	return false;

}


if(animating) return false;
animating = true;

//console.log("Next clicked");
current_fs = $(this).parent();
next_fs = $(this).parent().next();

//activate next step on progressbar using the index of next_fs
$("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

//show the next fieldset
next_fs.show(); 
//hide the current fieldset with style
current_fs.animate({opacity: 0}, {
	step: function(now, mx) {
		      //as the opacity of current_fs reduces to 0 - stored in "now"
		      //1. scale current_fs down to 80%
		      scale = 1 - (1 - now) * 0.2;
		      //2. bring next_fs from the right(50%)
		      left = (now * 50)+"%";
		      //3. increase opacity of next_fs to 1 as it moves in
		      opacity = 1 - now;
		      current_fs.css({
			      'transform': 'scale('+scale+')',
				      'position': 'absolute'
				      });
			      next_fs.css({'left': left, 'opacity': opacity});
			      }, 
			      duration: 800, 
			      complete: function(){
				      current_fs.hide();
				      animating = false;
			      }, 
			      //this comes from the custom easing plugin
			      easing: 'easeInOutBack'
			      });

})


$(".previous").click(function(){
	if(animating) return false;
	animating = true;

	current_fs = $(this).parent();
	previous_fs = $(this).parent().prev();

	//de-activate current step on progressbar
	$("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

	//show the previous fieldset
	previous_fs.show(); 
	//hide the current fieldset with style
	current_fs.animate({opacity: 0}, {
		step: function(now, mx) {
			      //as the opacity of current_fs reduces to 0 - stored in "now"
			      //1. scale previous_fs from 80% to 100%
			      scale = 0.8 + (1 - now) * 0.2;
			      //2. take current_fs to the right(50%) - from 0%
			      left = ((1-now) * 50)+"%";
			      //3. increase opacity of previous_fs to 1 as it moves in
			      opacity = 1 - now;
			      current_fs.css({'left': left});
			      previous_fs.css({'transform': 'scale('+scale+')', 'opacity': opacity});
				      }, 
				      duration: 800, 
				      complete: function(){
					      current_fs.hide();
					      animating = false;
				      }, 
				      //this comes from the custom easing plugin
				      easing: 'easeInOutBack'
				      });
			      });


$('#msform').on('submit', function (e) {
	//	e.preventDefault();
	$(".overlay").show();
	if (!e.isDefaultPrevented()) {
		var $form = $("#msform");
		var datas = getFormData($form);	
		//console.log(datas);
		//console.log($("#msform").serializeArray());
		$.ajax({
			url:'checkvalue.php',
			type:'POST',
			data:{json: JSON.stringify({datas})},
			dataType:'json',
			success: function(result) {
				// check result object for what you returned
				//console.log("Success");
				$(".overlay").hide();
				//alert(result["msg"]);

				if(result["code"]==200)
		{
			swal("Good job!",result["msg"], "success").then((value) => {
				$("#msform")[0].reset();
				location.reload();

			});
		}
				else if(result["code"]==404)
		{
			swal("Oops!",result["msg"], "error").then((value) => {
				$("#msform")[0].reset();
				location.reload();

			});
		}

				//$("#msform")[0].reset();
				//location.reload(); 
				//	//console.log(result);
			},
			error: function(error) {
				       // check error object or return error
				       //console.log(error);
				       swal("Oops!","There was an error while submitting the form. Please try again later", "error").then((value) => {
					       $(".overlay").hide();
					       $("#msform")[0].reset();
					       location.reload();

				       });

				       /*				       alert("There was an error while submitting the form. Please try again later");
									       $(".overlay").hide();
									       $("#msform")[0].reset();
									       location.reload();*/
			       }	
		});
		return false;
	}

})
