<?php
date_default_timezone_set('America/New_York');
error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
 * Copyright 2015 Dayton Bobbitt
 * Class used for creating and updating members, where a member is a user that belongs to a group
 */
 
class Member {
	private $id;
	private $user_id;
	private $group_id;
	private $joined;
	
	public static function create($user_id, $group_id) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");	
		
		$sql_query = "INSERT INTO `Member` (user_id, group_id) VALUES (" . 
			intval($mysqli->real_escape_string($user_id)) . ", " . 
			intval($mysqli->real_escape_string($group_id)) . ")";
	
		$result = $mysqli->query($sql_query);
		
		if ($result) {
			//print("SUCCESS: " . $sql_query . "<br>");
			$id = $mysqli->insert_id;
			return new Member($id, $user_id, $group_id, date('Y-m-d'));
		}
		//print("ERROR: " . $sql_query . "<br>");
		return null;
	}
	
	public static function find_by_id($id) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$result = $mysqli->query("SELECT * FROM `Member` WHERE id = " . $id);
		
		if ($result) {
      		if ($result->num_rows == 0) {
				return null;
      		}

      		$member_info = $result->fetch_array();
      		$joined = new DateTime($member_info['joined']);
      		
			return new Member(intval($member_info['id']),
				$member_info['user_id'],
				$member_info['group_id'],
				$joined);
    	}
    	return null;
	}
	
	public static function get_all_ids() {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$result = $mysqli->query("SELECT id FROM `Member`");
		$id_array = array();
		
		if ($result) {
			while ($next_row = $result->fetch_array()) {
				$id_array[] = intval($next_row['id']);
			}
		}
		return $id_array;
	}
	
	public static function get_user_ids_in_group($group_id) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$result = $mysqli->query("SELECT user_id FROM `Member` WHERE group_id=" . $group_id);
		$id_array = array();
		
		if ($result) {
			while ($next_row = $result->fetch_array()) {
				$id_array[] = intval($next_row['user_id']);
			}
		}
		return $id_array;
	}
	
	public static function get_groups_of_user($user_id) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$result = $mysqli->query("SELECT group_id FROM `Member` WHERE user_id=" . $user_id);
		$id_array = array();
		
		if ($result) {
			while ($next_row = $result->fetch_array()) {
				$id_array[] = intval($next_row['group_id']);
			}
		}
		return $id_array;
	}
	
	public static function notify($user_id, $group_id) {
		$group = Group::find_by_id($group_id);
		if ($group == null) return;		// Error getting group
		
		$user = User::find_by_id($user_id);
		if ($user == null) return;		// Error getting user
		
        $to = $user->get_email();
        $subject = wordwrap("You've been added to '" . $group->get_name() . "' on CodeChat!");
        $message = "<html><body>";
        $message .= "<p>To start chatting with your new group, just visit <a href='http://wwwp.cs.unc.edu/Courses/comp426-f15/users/dbobbitt/Codiad/workspace/cs426/Final/Frontend/main.php'>www.codechat.com</a>!</p>";
        $message .= "<br><br>Happy coding!";
        $message .="</body></html>";
        $headers = "From: no-reply@codechat.com\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        mail($to,$subject,$message,$headers);
    }
	
	private function __construct($id, $user_id, $group_id, $joined) {
		$this->id = $id;
		$this->user_id = $user_id;
		$this->group_id = $group_id;
		$this->joined = $joined;
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_user_id() {
		return $this->user_id;
	}
	
	public function get_group_id() {
		return $this->group_id;
	}
	
	public function get_joined() {
		return $this->joined;
	}
	
	public function remove() {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		$mysqli->query("DELETE FROM `Member` WHERE id=" . $this->id);
	}
	
	public function getJSON() {
    	if ($this->joined == null) {
    		$joined = null;
    	} else if (is_string($this->joined)) {
    		$joined = $this->joined;
    	} else {
    		$joined = $this->joined->format('Y-m-d');
    	}

    	$json_obj = array('id' => $this->id,
		      'user_id' => $this->user_id,
		      'group_id' => $this->group_id,
		      'joined' => $this->joined);
    	return json_encode($json_obj);
  	}
}