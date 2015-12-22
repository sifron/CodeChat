var url_base = "http://wwwp.cs.unc.edu/Courses/comp426-f15/users/dbobbitt/Codiad/workspace/cs426/Final/rest.php/";

var user_id = getCookie("codechat_session");
var msg_count = 0;
var group_id = null;
var group_name = null;
var last_msg_user_id = null;
var scrollCount = 6;

$(document).ready(function () {
    var groupWindow = false;
    var settingsWindow = false;
    
    $.get(url_base + "user/" + user_id + "/group", function(data, status) {
    	if(status=="success") {
    		$('#grouplist').empty();
        	group_id = data[0];
    		for (var groupid in data) {
    			$.get(url_base + "group/" + data[groupid], function(groupdata, groupstatus) {
    				if (group_id == groupdata["id"]) {
    					group_name = groupdata["name"];
    					$("#groupname").html("<b>" + group_name + "<br>");
    				}
    				var group = $("<div>" + groupdata["name"] + "</div>");
    				group.addClass("group");
    				group.attr("group_id", groupdata["id"]);
    				$('#grouplist').append(group);
    			});
    		}
    	}
    	updateChat(group_id);
    });
     
    setInterval(function() {
    	updateChat(group_id);
    	if (scrollCount > 0) {
    		scrollToBottom();
    		scrollCount--;
    	}
	}, 100);

	$("#chatInput").keypress(function() {
		document.title = "CodeChat";	
	});
	
	$("#chatInput").on("click", function() {
		document.title = "CodeChat";	
	});
    
    $("#groupbutton").on("click", function(e) {
       e.preventDefault();
       if(!groupWindow) {
           $("#groupbutton").css("background-color", "#545454");
           $("#groupcontainer").removeClass("slide-out");
           $("#groupcontainer").addClass("slide-in");
           groupWindow = true;
       } else {
           $("#groupbutton").removeAttr("style");
           $("#groupcontainer").removeClass("slide-in");
           $("#groupcontainer").addClass("slide-out");
           groupWindow = false;
       }
       
    }); 
    $("#settingsbutton").on("click", function(e) {
       e.preventDefault();
       if(!settingsWindow) {
           $("#settingsbutton").css("background-color", "#545454");
           $("#settingscontainer").removeClass("slide-up");
           $("#settingscontainer").addClass("slide-down");
           settingsWindow = true;
       } else {
           $("#settingsbutton").removeAttr("style");
           $("#settingscontainer").removeClass("slide-down");
           $("#settingscontainer").addClass("slide-up");
           settingsWindow = false;
       }
       
   }); 
   
   $("#codeWin").on("click", function(e) {
        e.preventDefault();                  
        document.title = "CodeChat";
        $("#overlay, #codeprompt").fadeIn(300);
    });
    
    $(".settingoption").on("click", function(e) {
        e.preventDefault();                  
        $("#overlay, #accountPrefs").fadeIn(300);
    });
    
    $("#overlay").on("click", function(e) {
        e.preventDefault();
        $("#overlay, #codeprompt, #newGroup, #newMember, #accountPrefs").fadeOut(300);
    });
    
    $("#chatSend").on("click", function(e) {
        e.preventDefault();
        document.title = "CodeChat";
        if($("#chatInput").val()!="") {
            $.ajax(url_base + "message/", 
                   {type: "POST",
                    dataType: "json",
                    data: {'user_id': user_id, 'group_id': group_id, 'text': $("#chatInput").val(), 'code_flag': 0},
                    success: function(data) {
                        // alert("SUCCESS");
                    },
                    error: function(data) {
                        alert("ERROR");
                    }
                });
        }
        $("#chatInput").val("");
        updateChat(group_id);
    });
    
    $("#codeSend").on("click", function(e) {
        e.preventDefault();
        document.title = "CodeChat";
        if(CKEDITOR.instances.codeinput.getData()!="") {
            $.ajax(url_base + "message/", 
                   {type: "POST",
                    dataType: "json",
                    data: {'user_id': user_id, 'group_id': group_id, 'text': "<pre><code>" + CKEDITOR.instances.codeinput.getData() + "</code></pre>", 'code_flag': 1},
                    success: function(data) {
                        // alert("SUCCESS");
                    },
                    error: function(data) {
                        alert("ERROR");
                    }
                });
        }
        // // $("#messageList").html("<pre><code>" + CKEDITOR.instances.codeinput.getData() + "</code></pre>");
        hljs.configure({useBR: true});
        // $('#messageList').each(function(i, block) {
        //     hljs.highlightBlock(block);
        // });
        $("#overlay, #codeprompt").fadeOut(300);
        CKEDITOR.instances.codeinput.setData("");
    });
  
  	$("#grouplist").on("click", "div", null, function(e) {
    	e.preventDefault();
      	if (group_id != $(this).attr("group_id")) {
      		group_name = $(this).text();
      		$("#groupname").html("<b>" + group_name + "<b>");
      		last_msg_user_id = null;
      		$("#messageList").empty();
          	group_id = $(this).attr("group_id");
      		msg_count = 0;
			setTimeout(function(){
  				updateChat(group_id);
			}, 500); 
        }
//       	$.get(url_base + "group/" + group_id + "/message", function(data, status) {
//         		alert(data);
//         });
    });
    
    $("#signoutbtn").on("click",function(e) {
		document.cookie = 'codechat_session' + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
		window.location.href = 'http://wwwp.cs.unc.edu/Courses/comp426-f15/users/dbobbitt/Codiad/workspace/cs426/Final/Frontend/login.html';
	});
    
    $("#createGroup").on("click", function(e) {
    	e.preventDefault();                  
        $("#overlay, #newGroup").fadeIn(300);
    });
    
    $("#addMember").on("click", function(e) {
    	e.preventDefault();                  
        $("#overlay, #newMember").fadeIn(300);
    });
    
    $("#newGroupSubmit").on("click", function(e) {
    	e.preventDefault();                  
        $.ajax(url_base + "group/", 
                   {type: "POST",
                    dataType: "json",
                    data: {'name': $("#newGroupName").val(), 'description': $("#newGroupDesc").val(), 'github_url': $("#newGroupGit").val() },
                    success: function(data) {
                    	$.ajax(url_base + "/member/", 
                   				{type: "POST",
                   				 dataType: "json",
                    			 data: {'user_id': user_id, 'group_id': data["id"]},
                    			 success: function(data) {
                    			 	$.get(url_base + "user/" + user_id + "/group", function(data, status) {
    										if(status=="success") {
    											$('#grouplist').empty();
        										group_id = data[0];
    											for (var groupid in data) {
    												$.get(url_base + "group/" + data[groupid], function(groupdata, groupstatus) {
    													if (group_id == groupdata["id"]) {
    														group_name = groupdata["name"];
    														$("#groupname").html("<b>" + group_name + "<br>");
    													}
    													var group = $("<div>" + groupdata["name"] + "</div>");
    													group.addClass("group");
    													group.attr("group_id", groupdata["id"]);
    													$('#grouplist').append(group);
    												});
    										}
    									}
    								$("#overlay, #codeprompt, #newGroup, #newMember, #accountPrefs").fadeOut(300);
    								updateChat(group_id);
    								});
                    			 },
                    			 error: function(data) {
                        			alert("ERROR - could not add user to group");
                    			 }
                		});
                    },
                    error: function(data) {
                        alert("ERROR - could not create group");
                    }
                });
    });
    
    $("#newMemberSubmit").on("click", function(e) {
    	e.preventDefault();
    	$.ajax(url_base + "member/", 
            {type: "POST",
            dataType: "json",
            data: {'group_id': group_id, 'email': $("#newMemberEmail").val()},
            success: function(data) { $("#overlay, #codeprompt, #newGroup, #newMember, #accountPrefs").fadeOut(300); },
            error: function(data) {
                alert("ERROR - could not add new member");
            }
        });
   	});
   	
   	$("#accountPrefSubmit").on("click", function(e) {
   		e.preventDefault();
   		
   		var newName = ($("#newName").val() !== "") ? $("#newName").val() : "";
   		var newEmail = ($("#newEmail").val() !== "") ? $("#newEmail").val() : "";
   		//var newPassword = ($("#newPassword").val() !== "") ? $("#newPassword").val() : "";
   		var newGit = ($("#newGit").val() !== "") ? $("#newGit").val() : "";
   		
   		$.ajax(url_base + "user/" + user_id, 
            {type: "POST",
            dataType: "json",
            data: {'name': newName, 'email': newEmail, 'github': newGit},
            success: function(data) { $("#overlay, #codeprompt, #newGroup, #newMember, #accountPrefs").fadeOut(300); },
            error: function(data) {
                alert("ERROR - could not update prefences");
            }
        });
   	});
   	/*
   	<input type="text" id="newName" placeholder="Name"> <br>
        	<input type="text" id="newEmail" placeholder="Email Address"> <br>
        	<input type="text" id="newPassword" placeholder="Password"> <br>
        	<input type="text" id="newGit" placeholder="Github"> <br>
        	<button id="accountPrefSubmit">Add</button>*/
});

updateChat = function(curr_group_ID) {
	if(curr_group_ID === null){
		return;
	} else {
		$.get(url_base + "group/" + group_id + "/message", function(data, status) {
	    	if(status=="success") {
	        	var new_msg_count = data.length;
	        	for(var i = new_msg_count - msg_count - 1; i >= 0; i--) {
	        		$.get(url_base + "message/" + data[i], function(data, status) {
	    				if(status=="success") {
	    					if(data["code_flag"]==1) {
	    						if (last_msg_user_id != data["user_id"]) {
	    							$("#messageList").append("<div class=\"message\">" + "<b>" + data["user_name"] + "</b>" + ": <span style=\"float:right; color:gray;\">"+ data["timestamp"] +"</span> " + "</div>");
	    						}
	    						var code = $("<div></div>");
	    						code.addClass("code");
	    						code.html(data["text"]);
	    						$("#messageList").append(code);
	    						hljs.configure({useBR: true});
    							$('.code').each(function(i, block) {
            						hljs.highlightBlock(block);
        						});
        						last_msg_user_id = null;
        						document.title = "New messages in " + group_name;
        						scrollCount = 6;
	    					} else {
	    						if (last_msg_user_id == data["user_id"]) {
	    							var message = $("<div></div>");
	    							message.addClass("sameUser");
	    							message.html(data["text"]);
	    							$("#messageList").append(message)
	    						} else {
	    							var message = $("<div></div>");
	    							message.addClass("message");
	    							message.html("<b>" + data["user_name"] + "</b>" + ": <span style=\"float:right; color:gray;\">"+ data["timestamp"] +"</span> <br>" + data["text"]);
	    							$("#messageList").append(message)
	    						}
	    						last_msg_user_id = data["user_id"];
	    						document.title = "New messages in " + group_name;
	    						scrollCount = 6;
	    					}
	    				}
	        		});
	        		scrollToBottom();
	        	}
	        	msg_count = new_msg_count;
	        	//alert(new_msg_count);
	      }
	    });
	}
};

scrollToBottom = function() {
	var messageList = $("#messageList");
	messageList.scrollTop(messageList[0].scrollHeight);
}

function getCookie(name) {
  var value = "; " + document.cookie;
  var parts = value.split("; " + name + "=");
  if (parts.length == 2) return parts.pop().split(";").shift();
}