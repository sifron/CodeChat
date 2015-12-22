<?php
date_default_timezone_set('America/New_York');
error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
 * Copyright 2015 Dayton Bobbitt
 * Class used for creating and updating favorited messages
 */

class Favorite {
	private $id;
	private $message_id;
	private $user_id;
	private $timestamp;
	
	public static function create($message_id, $user_id) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$sql_query = "INSERT INTO Favorite (message_id, user_id) VALUES (" . 
			intval($mysqli->real_escape_string($message_id)) . ", " .
			intval($mysqli->real_escape_string($user_id)) . ")";
		
		$result = $mysqli->query($sql_query);
			
		if ($result) {
			print("SUCCESS: " . $sql_query . "<br>");
			$id = $mysqli->insert_id;
			return new Favorite($id, $message_id, $user_id, date('Y-m-d'));
		}
		print("ERROR: " . $sql_query . "<br>");
		return null;
	}
	
	public static function find_by_id($id) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$result = $mysqli->query("SELECT * FROM `Favorite` WHERE id = " . $id);
		
		if ($result) {
      		if ($result->num_rows == 0) {
				return null;
      		}

      		$favorite_info = $result->fetch_array();
      		$timestamp = new DateTime($favorite_info['timestamp']);
      		
			return new Favorite(intval($favorite_info['id']),
				$favorite_info['message_id'],
				$favorite_info['user_id'],
				$timestamp);
    	}
    	return null;
	}
	
	public static function get_all_ids() {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$result = $mysqli->query("SELECT id FROM `Favorite`");
		$id_array = array();
		
		if ($result) {
			while ($next_row = $result->fetch_array()) {
				$id_array[] = intval($next_row['id']);
			}
		}
		return $id_array;
	}
	
	public static function get_user_ids_of_favorited_message($message_id) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$result = $mysqli->query("SELECT user_id FROM `Favorite` WHERE message_id=" . $message_id);
		$id_array = array();
		
		if ($result) {
			while ($next_row = $result->fetch_array()) {
				$id_array[] = intval($next_row['user_id']);
			}
		}
		return $id_array;
	}
	
	public static function get_favorited_message_ids_of_user($user_id) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$result = $mysqli->query("SELECT message_id FROM `Favorite` WHERE user_id=" . $user_id);
		$id_array = array();
		
		if ($result) {
			while ($next_row = $result->fetch_array()) {
				$id_array[] = intval($next_row['message_id']);
			}
		}
		return $id_array;
	}
	
	public static function get_favorited_message_ids_of_user_in_group($user_id, $group_id) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$result = $mysqli->query("SELECT message_id FROM `Favorite` F, `Message` M WHERE F.message_id = M.id AND F.user_id=" .
			$user_id . " AND M.group_id=" . $group_id . " ORDER BY F.timestamp DESC");
		$id_array = array();
		
		if ($result) {
			while ($next_row = $result->fetch_array()) {
				$id_array[] = intval($next_row['message_id']);
			}
		}
		return $id_array;
	}
	
	private function __construct($id, $message_id, $user_id, $timestamp) {
		$this->id = $id;
		$this->message_id = $message_id;
		$this->user_id = $user_id;
		$this->timestamp = $timestamp;
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
	
	public function get_timestamp() {
		return $this->timestamp;
	}
	
	public function remove() {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		$mysqli->query("DELETE FROM `Favorite` WHERE id=" . $this->id);
	}
	
	public function getJSON() {
    	if ($this->timestamp == null) {
    		$timestamp = null;
    	} else if (is_string($this->timestamp)) {
    		$timestamp = $this->timestamp;
    	} else {
    		$timestamp = $this->timestamp->format('Y-m-d');
    	}

    	$json_obj = array('id' => $this->id,
		      'message_id' => $this->message_id,
		      'user_id' => $this->user_id,
		      'timestamp' => $this->timestamp);
    	return json_encode($json_obj);
  	}
}