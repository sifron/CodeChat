<?php
date_default_timezone_set('America/New_York');
error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
 * Copyright 2015 Dayton Bobbitt
 * Class used for creating and updating directed messages
 */
 
class DirectedMessage {
	private $id;
	private $message_id;
	private $user_id;
	
	public static function create($message_id, $user_id) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");	
		
		$sql_query = "INSERT INTO `DirectedMessage` (message_id, user_id) VALUES (" . 
			intval($mysqli->real_escape_string($message_id)) . ", " . 
			intval($mysqli->real_escape_string($user_id)) . ")";
	
		$result = $mysqli->query($sql_query);
		
		if ($result) {
			print("SUCCESS: " . $sql_query . "<br>");
			$id = $mysqli->insert_id;
			return new DirectedMessage($id, $message_id, $user_id);
		}
		print("ERROR: " . $sql_query . "<br>");
		return null;
	}
	
	public static function find_by_id($id) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		return DirectedMessage::find_by($mysqli, " id = " . $mysqli->real_escape_string($id));
	}
	
	public static function find_by_user_id($user_id) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		return DirectedMessage::find_by($mysqli, " user_id = " . $mysqli->real_escape_string($user_id));
	}
	
	public static function find_by_message_id($message_id) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		return DirectedMessage::find_by($mysqli, " message_id = " . $mysqli->real_escape_string($message_id));
	}
	
	private static function find_by($mysqli, $criteria) {
		$result = $mysqli->query("SELECT * FROM `DirectedMessage` WHERE" . $criteria);
		
		if ($result) {
      		if ($result->num_rows == 0) {
				return null;
      		}

      		$directed_message_info = $result->fetch_array();
      		
			return new DirectedMessage(intval($directed_message_info['id']),
				$directed_message_info['message_id'],
				$directed_message_info['user_id']);
    	}
    	return null;
	}
	
	public static function get_all_ids() {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$result = $mysqli->query("SELECT id FROM `DirectedMessage`");
		$id_array = array();
		
		if ($result) {
			while ($next_row = $result->fetch_array()) {
				$id_array[] = intval($next_row['id']);
			}
		}
		return $id_array;
	}
	
	private function __construct($id, $message_id, $user_id) {
		$this->id = $id;
		$this->message_id = $message_id;
		$this->user_id = $user_id;
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_message_id() {
		return $this->message_id;
	}
	
	public function get_user_id() {
		return $this->user_id;
	}
	
	public function remove() {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		$mysqli->query("DELETE FROM `DirectedMessage` WHERE id=" . $this->id);
	}
	
	public function getJSON() {
    	$json_obj = array('id' => $this->id,
    		  'message_id' => $this->message_id,
		      'user_id' => $this->user_id);
    	return json_encode($json_obj);
  	}
}