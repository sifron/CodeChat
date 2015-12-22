<?php
date_default_timezone_set('America/New_York');
error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
 * Copyright 2015 Dayton Bobbitt
 * Class used for creating and updating users
 */
 
class User {
	private $id;
	private $name;
	private $email;
	private $github;
	private $created;
	private $active;
	
	public static function create($name, $email, $github=null) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");	
		
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return null;		// Invalid email address
		
		
		// Insert github username if one is provided
		$github_field = "";
		$github_value = "";
		if ($github != null) {
			$github_field = ", github";
			$github_value = ", '" . $mysqli->real_escape_string($github) . "'";
		}
		
		$sql_query = "INSERT INTO User (name, email" . $github_field .") VALUES (" . 
					  "'" . $mysqli->real_escape_string($name) . "', " .
					  "'" . $mysqli->real_escape_string($email) . "'" .
					  $github_value . ")";
					
		$result = $mysqli->query($sql_query);
		
		if ($result) {
			//print("SUCCESS: " . $sql_query . "<br>");
			$id = $mysqli->insert_id;
			return new User($id, $name, $email, $github, date("Y-m-d"), true);
		}
		//print("ERROR: " . $sql_query . "<br>");
		return null;
	}
	
	public static function find_by_id($id) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		return User::find_by($mysqli, " id = " . intval($mysqli->real_escape_string($id)));
	}
	
	public static function find_by_email($email) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		return User::find_by($mysqli, " email = '" . $mysqli->real_escape_string($email) . "'");
	}
	
	private static function find_by($mysqli, $criteria) {
		$result = $mysqli->query("SELECT * FROM User WHERE" . $criteria);
		
		if ($result) {
      		if ($result->num_rows == 0) {
				return null;
      		}

      		$user_info = $result->fetch_array();
      		
      		$created = new DateTime($user_info['created']);
      		$active = true;
      		if (!$user_info['active']) {
      			$active = false;
      		}
      		
			return new User(intval($user_info['id']),
				$user_info['name'],
				$user_info['email'],
				$user_info['github'],
				$created,
				$active);
    	}
    	return null;
	}
	
	public static function get_all_ids() {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$result = $mysqli->query("SELECT id FROM User");
		$id_array = array();
		
		if ($result) {
			while ($next_row = $result->fetch_array()) {
				$id_array[] = intval($next_row['id']);
			}
		}
		return $id_array;
	}
	
	private function __construct($id, $name, $email, $github, $created, $active) {
		$this->id = $id;
		$this->name = $name;
		$this->email = $email;
		$this->github = $github;
		$this->created = $created;
		$this->active = $active;
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function get_email() {
		return $this->email;
	}
	
	public function get_github() {
		return $this->github;
	}
	
	public function get_created() {
		return $this->created;
	}
	
	public function is_active() {
		return $this->active;
	}
	
	public function set_name($name) {
		$this->name = $name;
		return $this->update();
	}
	
	public function set_email($email) {
		$this->email = $email;
		return $this->update();
	}
	
	public function set_github($github) {
		$this->github = $github;
		return $this->update();
	}
	
	// Soft delete
	public function remove() {
		$this->active = false;
		return $this->update();
	}
	
	private function update() {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$active = 'TRUE';
		if (!$this->active) {
			$active = 'FALSE';
		}
		
		// Insert github username if one is provided
		$github_field = "";
		$github_value = "";
		if ($this->github != null) {
			$github_field = ", github=";
			$github_value = "'" . $mysqli->real_escape_string($this->github) . "'";
		}
		
		$result = $mysqli->query("UPDATE User SET " .
			"name=" . "'" . $mysqli->real_escape_string($this->name) . "', " .
			"email=" . "'" . $mysqli->real_escape_string($this->email) . "'" .
			$github_field . $github_value . ", " . 
			"active=" . $active . 
			" WHERE id=" . $this->id);

		return $result;
	}
	
	public function getJSON() {
    	if ($this->created == null) {
    		$created = null;
    	} else if (is_string($this->created)) {
    		$created = $this->created;
    	} else {
    		$created = $this->created->format('Y-m-d');
    	}

    	$json_obj = array('id' => $this->id,
		      'name' => $this->name,
		      'email' => $this->email,
		      'github' => $this->github,
		      'created' => $created,
		      'active' => $this->active);
    	return json_encode($json_obj);
  	}
}