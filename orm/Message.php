<?php
date_default_timezone_set('America/New_York');
error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
 * Copyright 2015 Dayton Bobbitt
 * Class used for creating and updating messages
 */
 
class Message {
	private $id;
	private $user_id;
	private $user_name;
	private $group_id;
	private $text;
	private $code_flag;
	private $timestamp;
	private $deleted;
	
	public static function create($user_id, $group_id, $text, $code_flag) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");	
		
		$code_str = 'FALSE';
		if ($code_flag) {
			$code_str = 'TRUE';
		}
		
		$sql_query = "INSERT INTO `Message` (user_id, group_id, text, code_flag) VALUES (" . 
			intval($mysqli->real_escape_string($user_id)) . ", " . 
			intval($mysqli->real_escape_string($group_id)) . ", " . 
			"'" . $mysqli->real_escape_string($text) . "', " .
			$code_str . ")";
			
		$result = $mysqli->query($sql_query);
		
		if ($result) {
			//print("SUCCESS: " . $sql_query . "<br>");
			$id = $mysqli->insert_id;
			return new Message($id, $user_id, "", $group_id, $text, $code_flag, date("Y-m-d H:i:s"), false);
		}
		//print("ERROR: " . $sql_query . "<br>");
		return null;
	}
	
	public static function find_by_id($id) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$result = $mysqli->query("SELECT * FROM `Message` M, `User` U WHERE M.user_id = U.id AND M.id = " . $id);
		
		if ($result) {
      		if ($result->num_rows == 0) {
				return null;
      		}

      		$message_info = $result->fetch_array();
      		$timestamp = new DateTime($message_info['timestamp']);
      		
      		$deleted = false;
      		if ($message_info['deleted']) {
      			$deleted = true;
      		}
      		
			return new Message(intval($message_info['id']),
				$message_info['user_id'],
				$message_info['name'],
				$message_info['group_id'],
				$message_info['text'],
				$message_info['code_flag'],
				$timestamp,
				$deleted);
    	}
    	return null;
	}
	
	public static function get_all_ids() {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$result = $mysqli->query("SELECT id FROM `Message` WHERE deleted=FALSE ORDER BY timestamp DESC");
		$id_array = array();
		
		if ($result) {
			while ($next_row = $result->fetch_array()) {
				$id_array[] = intval($next_row['id']);
			}
		}
		return $id_array;
	}
	
	public static function get_message_ids_of_group($group_id) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$result = $mysqli->query("SELECT id FROM `Message` WHERE deleted=FALSE AND group_id=" . $group_id . " ORDER BY timestamp DESC");
		$id_array = array();
		
		if ($result) {
			while ($next_row = $result->fetch_array()) {
				$id_array[] = intval($next_row['id']);
			}
		}
		return $id_array;
	}
	
	public static function get_message_ids_of_user($user_id) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$result = $mysqli->query("SELECT id FROM `Message` WHERE deleted=FALSE AND user_id=" . $user_id . " ORDER BY timestamp DESC");
		$id_array = array();
		
		if ($result) {
			while ($next_row = $result->fetch_array()) {
				$id_array[] = intval($next_row['id']);
			}
		}
		return $id_array;
	}
	
	public static function get_message_ids_of_user_in_group($user_id, $group_id) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$result = $mysqli->query("SELECT id FROM `Message` WHERE deleted=FALSE AND user_id=" . 
			$user_id . " AND group_id=" . 
			$group_id . " ORDER BY timestamp DESC");
		$id_array = array();
		
		if ($result) {
			while ($next_row = $result->fetch_array()) {
				$id_array[] = intval($next_row['id']);
			}
		}
		return $id_array;
	}
	
	private function __construct($id, $user_id, $user_name, $group_id, $text, $code_flag, $timestamp, $deleted) {
		$this->id = $id;
		$this->user_id = $user_id;
		$this->user_name = $user_name;
		$this->group_id = $group_id;
		$this->text = $text;
		$this->code_flag = $code_flag;
		$this->timestamp = $timestamp;
		$this->deleted = $deleted;
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
	
	public function get_text() {
		return $this->text;
	}
	
	public function is_code() {
		return $this->code_flag;
	}
	
	public function get_timestamp() {
		return $this->timestamp;
	}
	
	public function is_deleted() {
		return $this->deleted;
	}
	
	public function set_text($text) {
		$this->text = $text;
		$this->update();
	}
	
	public function set_code_flag($code_flag) {
		$this->code_flag = $code_flag;
		$this->update();
	}
	
	// Soft delete
	public function remove() {
		$this->deleted = true;
		$this->update();
	}
	
	private function update() {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$deleted = 'FALSE';
		if ($this->deleted) {
			$deleted = 'TRUE';
		}
		
		$code_str = 'FALSE';
		if ($this->code_flag) {
			$code_str = 'TRUE';
		}
	
		$result = $mysqli->query("UPDATE `Message` SET " .
			"text=" . "'" . $mysqli->real_escape_string($this->text) . "', " .
			"code_flag=" . $code_str . ", " .
			"deleted=" . $deleted . 
			" WHERE id=" . $this->id);

		return $result;
	}
	
	public function getJSON() {
    	if ($this->timestamp == null) {
    		$timestamp = null;
    	} else if (is_string($this->timestamp)) {
    		$timestamp = $this->timestamp;
    	} else {
    		$timestamp = $this->timestamp->format('g:i:a');
    	}

    	$json_obj = array('id' => $this->id,
		      'user_id' => $this->user_id,
		      'user_name' => $this->user_name,
		      'group_id' => $this->group_id,
		      'text' => $this->text,
		      'code_flag' => $this->code_flag,
		      'timestamp' => $timestamp,
		      'deleted' => $this->deleted);
    	return json_encode($json_obj);
  	}
}