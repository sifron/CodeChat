<?php
date_default_timezone_set('America/New_York');
error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
 * Copyright 2015 Dayton Bobbitt
 * Class used for creating and updating passwords
 */

class Authentication {
	private $id;
	private $user_id;
	private $timestamp;
	
	public static function create($user_id, $password) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$sql_query = "INSERT INTO `Authentication` (user_id, password) VALUES (" . 
			intval($mysqli->real_escape_string($user_id)) . ", PASSWORD('" . 
			$mysqli->real_escape_string($password) . "'))";
			
		$result = $mysqli->query($sql_query);
		
		if ($result) {
			//print("SUCCESS: " . $sql_query . "<br>");
			$id = $mysqli->insert_id;
			return new Authentication($id, $user_id, date('Y-m-d H:i:s'));
		}
		//print("ERROR: " . $sql_query . "<br>");
		return null;
	}
	
	public static function find_by_id($id) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$result = $mysqli->query("SELECT id, user_id, timestamp FROM `Authentication` WHERE id = " . $id);
		
		if ($result) {
      		if ($result->num_rows == 0) {
				return null;
      		}

      		$authentication_info = $result->fetch_array();
      		$timestamp = new DateTime($authentication_info['timestamp']);
      		
			return new Authentication(intval($authentication_info['id']),
				$authentication_info['user_id'],
				$timestamp);
    	}
    	return null;
	}
	
	public static function get_all_ids() {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$result = $mysqli->query("SELECT id FROM `Authentication`");
		$id_array = array();
		
		if ($result) {
			while ($next_row = $result->fetch_array()) {
				$id_array[] = intval($next_row['id']);
			}
		}
		return $id_array;
	}
	
	public static function find_by_user_id($user_id) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$result = $mysqli->query("SELECT id, user_id, timestamp FROM `Authentication` WHERE user_id = " . $user_id);

		if ($result) {
      		if ($result->num_rows == 0) {
				return null;
      		}

      		$authentication_info = $result->fetch_array();
      		$timestamp = new DateTime($authentication_info['timestamp']);
      		
			return new Authentication(intval($authentication_info['id']),
				$authentication_info['user_id'],
				$timestamp);
    	}
    	return null;
	}
	
	private function __construct($id, $user_id, $timestamp) {
		$this->id = $id;
		$this->user_id = $user_id;
		$this->timestamp = $timestamp;
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_user_id() {
		return $this->user_id;
	}
	
	public function get_timestamp() {
		return $this->timestamp;
	}
	
	public function authenticate($password) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$result = $mysqli->query("SELECT COUNT(*) AS count FROM `Authentication` WHERE id=" . 
			$this->id . " AND password = PASSWORD('" . 
			$mysqli->real_escape_string($password) . "')");
			
		if ($result) {		
			$row = $result->fetch_array();
			if (intval($row['count']) == 1) return true;	// Password matched exactly one row
		}
		return false;										// Incorrect password
	}
	
	public function set_password($old_password, $new_password) {
		if ($this->authenticate($old_password)) {			// Update new password only if old password is valid
			$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
	
			$result = $mysqli->query("UPDATE `Authentication` SET " .
				"password=PASSWORD('" . $mysqli->real_escape_string($new_password) . "'), " .
				"timestamp='" . date('Y-m-d H:i:s') . "'" .
				" WHERE id=" . $this->id);

			return $result;
		}
		return false;
	}
	
	public function getJSON() {
    	if ($this->timestamp == null) {
    		$timestamp = null;
    	} else if (is_string($this->timestamp)) {
    		$timestamp = $this->timestamp;
    	} else {
    		$timestamp = $this->timestamp->format('Y-m-d H:i:s');
    	}

    	$json_obj = array('id' => $this->id,
		      'user_id' => $this->user_id,
		      'timestamp' => $timestamp);
    	return json_encode($json_obj);
  	}
}