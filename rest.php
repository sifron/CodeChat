<?php

require_once('orm/Authentication.php');
require_once('orm/DirectedMessage.php');
require_once('orm/Favorite.php');
require_once('orm/Group.php');
require_once('orm/Member.php');
require_once('orm/Message.php');
require_once('orm/User.php');
    
$path_components = (isset($_SERVER['PATH_INFO']) ? explode('/', $_SERVER['PATH_INFO']) : '');
$path_length = count($path_components);
/*
print("path_components: ");
print_r($path_components);
print("<br>");
print("path_components length: " . $path_length . "<br>");*/

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if ($path_length >= 2 && $path_components[1] != "") {
    	
    	// GET user
    	if ($path_components[1] == "user") {
    		if (isset($path_components[2]) && $path_components[2] != "") {
          		$user_id = intval($path_components[2]);
          		$user = User::find_by_id($user_id);
          
          		if ($user == null) {
            		// User did not exist
            		header("HTTP/1.0 404 Not Found");
          			print("User id: " . $user_id . " not found.<br>");
            		exit();
          		}
    		}
          
          	// Looking for the json of a specific user
          	if ($path_length == 3 && $path_components[2] != ""|| ($path_length == 4 && $path_components[3] == "")) {
          		header("Content-type: application/json");
          		print($user->getJSON());
          		exit();
          	} 
          	else if ($path_length >= 4) {
          		// Return message ids owned by user
          		if ($path_components[3] == "message") {
        			header("Content-type: application/json");
        			print(json_encode(Message::get_message_ids_of_user($user_id)));
        			exit();
          		} 
          		// Return ids of groups to which the user id belongs
          		else if ($path_components[3] == "group") {
          			header("Content-type: application/json");
        			print(json_encode(Member::get_groups_of_user($user_id)));
        			exit();
          		}
          		// Return ids of messages that the user id favorited
          		else if ($path_components[3] == "favorite") {
          			header("Content-type: application/json");
        			print(json_encode(Favorite::get_favorited_message_ids_of_user($user_id)));
        			exit();
          		}
          	}
            // Id not specified, looking for index of Users
        	header("Content-type: application/json");
        	print(json_encode(User::get_all_ids()));
        	exit();
      	}
      	
      	// GET message
      	else if ($path_components[1] == "message") {
        	if (isset($path_components[2]) && $path_components[2] != "") {
          		$message_id = intval($path_components[2]);
          		$message = Message::find_by_id($message_id);
          
          		if ($message == null) {
            		// Message did not exist
            		header("HTTP/1.0 404 Not Found");
          			print("Message id: " . $message_id . " not found.<br>");
            		exit();
          		}
          		// Looking for the json of a specific message
          		if ($path_length == 3 || ($path_length == 4 && $path_components[3] == "")) {
          			header("Content-type: application/json");
          			print($message->getJSON());
          			exit();
          		} 
          		else if ($path_length == 4) {
          			// Return user ids that favorited a given message id
          			if ($path_components[3] == "favorite") {
        				header("Content-type: application/json");
        				print(json_encode(Favorite::get_user_ids_of_favorited_message($message_id)));
        				exit();
          			}
          		}
    		}
            // Id not specified, looking for index of Messages
        	header("Content-type: application/json");
        	print(json_encode(Message::get_all_ids()));
        	exit();
      	}
      	
      	// GET group
      	else if ($path_components[1] == "group") {
        	if (isset($path_components[2]) && $path_components[2] != "") {
          		$group_id = intval($path_components[2]);
          		$group = Group::find_by_id($group_id);
          
          		if ($group == null) {
            		// Group did not exist
            		header("HTTP/1.0 404 Not Found");
          			print("Group id: " . $group_id . " not found.<br>");
            		exit();
          		}
          		// Looking for the json of a specific user
          		if ($path_length == 3 || ($path_length == 4 && $path_components[3] == "")) {
          			header("Content-type: application/json");
          			print($group->getJSON());
          			exit();
          		} 
          		else if ($path_length >= 4) {
          			// Return message ids belonging to a given group id
          			if ($path_components[3] == "message") {
        				header("Content-type: application/json");
        				print(json_encode(Message::get_message_ids_of_group($group_id)));
        				exit();
          			} 
          			// Return ids of users belonging to a given group id
          			else if ($path_components[3] == "user") {
          				header("Content-type: application/json");
        				print(json_encode(Member::get_user_ids_in_group($group_id)));
        				exit();
          			}
          		}
    		}
            // Id not specified, looking for index of Groups
        	header("Content-type: application/json");
        	print(json_encode(Group::get_all_ids()));
        	exit();
      	}
    }
}

else if ($_SERVER['REQUEST_METHOD'] == "PUT"){
    $message_id = intval($path_components[1]);
    $message = Message::find_by_id($message_id);

}

else if ($_SERVER['REQUEST_METHOD'] == "POST"){
    if ($path_length >= 2 && $path_components[1] != "") {
    	// POST message - create a new message
    	if ($path_components[1] == "message") {
			// Create new message
			if (!isset($_REQUEST['user_id'])) {
				header("HTTP/1.0 400 Bad Request");
				print("Missing user_id");
				exit();
			}
			$user_id = intval(trim($_REQUEST['user_id']));
			
			if (!isset($_REQUEST['group_id'])) {
				header("HTTP/1.0 400 Bad Request");
				print("Missing group_id");
				exit();
			}
			$group_id = intval(trim($_REQUEST['group_id']));
			
			if (!isset($_REQUEST['text'])) {
				header("HTTP/1.0 400 Bad Request");
				print("Missing text");
				exit();
			}
			$text = trim($_REQUEST['text']);
			
			if (!isset($_REQUEST['code_flag'])) {
				header("HTTP/1.0 400 Bad Request");
				print("Missing code flag");
				exit();
			}
			$code_flag = trim($_REQUEST['code_flag']);
			
			$new_message = Message::create($user_id, $group_id, $text, $code_flag);
			
			// Report if failed
			if ($new_message == null) {
				header("HTTP/1.0 500 Server Error");
				print("Server couldn't create new message");
				exit();
			}
			
			// Generate JSON encoding of new message
			header("Content-type: application/json");
			print($new_message->getJSON());
			exit();
      	} 
      	    
      	// POST group
        else if ($path_components[1] == "group") {
            if (!isset($_REQUEST['name'])) {
                header("HTTP/1.0 400 Bad Request");
                print("Missing name");
                exit();
            }
            $name = trim($_REQUEST['name']);

			$description = null;
			if (isset($_REQUEST['description'])) {
                $description = trim($_REQUEST['description']);
            }
            
            $github_url = null;
            if (isset($_REQUEST['github_url'])) {
                $github_url = trim($_REQUEST['github_url']);
            }
            
            $new_group = Group::create($name, $description, $github_url);
			
			// Report if failed
			if ($new_group == null) {
				header("HTTP/1.0 500 Server Error");
				print("Server couldn't create new group");
				exit();
			}
			
			// Generate JSON encoding of new group
			header("Content-type: application/json");
			print($new_group->getJSON());
			exit();
        }
        
        // POST member
        else if ($path_components[1] == "member") {
        	if (!isset($_REQUEST['group_id'])) {
        		header("HTTP/1.0 400 Bad Request");
                print("Missing group_id");
                exit();
        	}
        	$group_id = intval(trim($_REQUEST['group_id']));
        	
        	if (!isset($_REQUEST['email']) && !isset($_REQUEST['user_id'])) {
                header("HTTP/1.0 400 Bad Request");
                print("Missing user_id or email");
                exit();
            }
            
            if (isset($_REQUEST['email'])) {
            	$email = trim($_REQUEST['email']);
            	$user_id = User::find_by_email($email)->get_id();
            } else {
            	$user_id = trim($_REQUEST['user_id']);
            }
            
            $new_member = Member::create($user_id, $group_id);
			
			// Report if failed
			if ($new_member == null) {
				header("HTTP/1.0 500 Server Error");
				print("Server couldn't create new member");
				exit();
			}
			
			// Generate JSON encoding of new member
			header("Content-type: application/json");
			print($new_member->getJSON());
			//Member::notify($user_id, $group_id);
			exit();
        }

        // POST user
        else if ($path_components[1] == "user" && $path_length <= 3 && isset($path_components[2]) && $path_components[2] == "") {
            if (!isset($_REQUEST['name'])) {
                header("HTTP/1.0 400 Bad Request");
                print("Missing name");
                exit();
            }
            $name = trim($_REQUEST['name']);

			if (!isset($_REQUEST['password'])) {
                header("HTTP/1.0 400 Bad Request");
                print("Missing password");
                exit();
            }
            $password = trim($_REQUEST['password']);

			if (!isset($_REQUEST['email'])) {
                header("HTTP/1.0 400 Bad Request");
                print("Missing email");
                exit();
            }
            $email = trim($_REQUEST['email']);
            
            $github = null;
            if (isset($_REQUEST['github'])) {
                $github = trim($_REQUEST['github']);
            }
            
            $new_user = User::create($name, $email, $github);
			
			// Report if failed
			if ($new_user == null) {
				header("HTTP/1.0 500 Server Error");
				print("Server couldn't create new user");
				exit();
			}
			
			Authentication::create($new_user->get_id(), $password);
			
			// Generate JSON encoding of new user
			header("Content-type: application/json");
			print($new_user->getJSON());
			exit();
        }
        
        // POST user - update existing user
        else if ($path_components[1] == "user" && $path_components[2] != "") {
        	$user_id = $path_components[2];
            $user = User::find_by_id($user_id);
            
            if ($user == null) {
                // User did not exist
                header("HTTP/1.0 404 Not Found");
                print("User id: " . $user_id . " not found.<br>");
                exit();
            }
            
            // Delete user
            if (isset($_REQUEST['remove']) && $_REQUEST['remove'] == "remove") {
                $user->remove();
            }
            
            // Update user
            else {
                if (isset($_REQUEST['name']) && $_REQUEST['name'] !== "") {
                    $user->set_name(trim($_REQUEST['name']));
                }
                
                if (isset($_REQUEST['email']) && $_REQUEST['email'] !== "") {
                    $user->set_email(trim($_REQUEST['email']));
                }
                
                if (isset($_REQUEST['github']) && $_REQUEST['github'] !== "") {
                    $user->set_github(trim($_REQUEST['github']));
                }
                
                if (isset($_REQUEST['old_password']) && isset($_REQUEST['new_password']) && $_REQUEST['old_password'] !== "" && $_REQUEST['new_password'] !== "") {
                    header("Content-type: application/json");
                    if ($auth->set_password(trim($_REQUEST['old_password']), trim($_REQUEST['new_password']))) {
                		print("HTTP/1.0 200 OK");
                	} else {
                		print("HTTP/1.0 401 UNAUTHORIZED");
                	}
                    exit();
                }
                header("Content-type: application/json");
                print($user->getJSON());
                exit();
            }
        }    
            
        // POST user - authentication
        else if ($path_components[1] == "auth") {
        	if (!isset($_REQUEST['email'])) {
        		header("HTTP/1.0 400 Bad Request");
                print("Missing email");
                exit();
        	}
        	$email = trim($_REQUEST['email']);
        	
        	if (!isset($_REQUEST['password'])) {
        		header("HTTP/1.0 400 Bad Request");
                print("Missing password");
                exit();
        	}
        	$password = trim($_REQUEST['password']);
        	
            $user = User::find_by_email($email);
            
            if ($user == null) {
                // User did not exist
                header("HTTP/1.0 401 UNAUTHORIZED");
                exit();
            }
            $user_id = $user->get_id();
            
            $auth = Authentication::find_by_user_id($user_id);
           
            if ($auth->authenticate($password)) {
             	header("HTTP/1.0 200 OK");
             	//$_SESSION['user_id'] = $user_id;
  				//$_SESSION['authsalt'] = time();
				//$auth_cookie_val = md5($_SESSION['user_id'] . $_SERVER['REMOTE_ADDR'] . $_SESSION['authsalt']);
				//setcookie('codechat_session', $auth_cookie_val, 0, "/");
				setcookie('codechat_session', $user_id, time() + (86400 * 30), "/");
            } else {
               	header("HTTP/1.0 401 UNAUTHORIZED");
            }
            exit();
        }
    }
}

//None of the above works
header("HTTP/1.0 400 Bad Request");
print("Did not understand URL");

?>