var url_base = "http://wwwp.cs.unc.edu/Courses/comp426-f15/users/dbobbitt/Codiad/workspace/cs426/Final/rest.php/";

$(document).ready(function() 
{
		
		$("#signupbtn").on("click", function(e) 
		{
			e.preventDefault();	
			authenticate_account();
		});
		
		//$("#retypepassword").keyup(checkPasswordMatch);
		$("input[name=retypepassword]").on('keyup', function () {
   			var retype = $(this).val()
   			var password = $("input[name=password]").val()
   			if(retype != password)
   				$("#divCheckPasswordMatch").html("Passwords do not match. Moron.");
   			else
   		        $("#divCheckPasswordMatch").html("Passwords match!");
			});
});

function authenticate_account() 
{
	$.ajax(url_base + "user/", 
        {	
        	type: "POST",
         	dataType: "json",
         	data: { 'name': $("#name").val(), 'email': $("#username").val(), 'password': $("#password").val(), 'github': $("github").val() },
         	success: function(data) {
         		window.location.replace("http://wwwp.cs.unc.edu/Courses/comp426-f15/users/dbobbitt/Codiad/workspace/cs426/Final/Frontend/login.html");
         	},
         	error: function(data) 
         			{
                		//alert("ERROR");
                	}
    	});
}