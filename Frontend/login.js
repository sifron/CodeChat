var url_base = "http://wwwp.cs.unc.edu/Courses/comp426-f15/users/dbobbitt/Codiad/workspace/cs426/Final/rest.php/";

$(document).ready(function () {
	$("#submit").on("click", function(e) {
		e.preventDefault();	
		authenticate_user();
	});
	
	$("#password").keypress(function(event) {
    	if (event.which == 13) {
    		event.preventDefault();
        	authenticate_user();
    	}
	});
});

authenticate_user = function() {
	$.ajax(url_base + "auth", 
                   {type: "POST",
                    data: {'email': $("#email").val(), 'password': $("#password").val()},
                    success: function(data) {
                        window.location.replace("http://wwwp.cs.unc.edu/Courses/comp426-f15/users/dbobbitt/Codiad/workspace/cs426/Final/Frontend/main.php");
                    },
                    error: function(data) {
                        //alert("ERROR");
                    }
                });
}