<?php
date_default_timezone_set('America/New_York');
error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
 * Copyright 2015 Dayton Bobbitt
 * Class used for creating and updating groups
 */
 
class Group {
	private $id;
	private $name;
	private $description;
	private $github_url;
	private $created;
	private $active;
	
	public static function create($name, $description=null, $github_url=null) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");	
		
		// Insert description if one is provided
		$description_field = "";
		$description_value = "";
		if ($description != null) {
			$description_field = ", description";
			$description_value = ", '" . $mysqli->real_escape_string($description) . "'";
		}
		
		// Insert github url if one is provided
		$github_field = "";
		$github_value = "";
		if ($github_url != null) {
			$github_field = ", github_url";
			$github_value = ", '" . $mysqli->real_escape_string($github_url) . "'";
		}
		
		$sql_query = "INSERT INTO `Group` (name" . $description_field . $github_field .") VALUES (" . 
					  "'" . $mysqli->real_escape_string($name) . "'" .
					  $description_value .
					  $github_value . ")";
					
		$result = $mysqli->query($sql_query);
		
		if ($result) {
			//print("SUCCESS: " . $sql_query . "<br>");
			$id = $mysqli->insert_id;
			return new Group($id, $name, $description, $github_url, date("Y-m-d"), true);
		}
		//print("ERROR: " . $sql_query . "<br>");
		return null;
	}
	
	public static function find_by_id($id) {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$result = $mysqli->query("SELECT * FROM `Group` WHERE id = " . $id);
		
		if ($result) {
      		if ($result->num_rows == 0) {
				return null;
      		}

      		$group_info = $result->fetch_array();
      		
      		$created = new DateTime($group_info['created']);
      		$active = true;
      		if (!$group_info['active']) {
      			$active = false;
      		}
      		
			return new Group(intval($group_info['id']),
				$group_info['name'],
				$group_info['description'],
				$group_info['github_url'],
				$created,
				$active);
    	}
    	return null;
	}
	
	public static function get_all_ids() {
		$mysqli = new mysqli("classroom.cs.unc.edu", "dbobbitt", "comp426final", "dbobbittdb");
		
		$result = $mysqli->query("SELECT id FROM `Group`");
		$id_array = array();
		
		if ($result) {
			while ($next_row = $result->fetch_array()) {
				$id_array[] = intval($next_row['id']);
			}
		}
		return $id_array;
	}
	
	private function __construct($id, $name, $description, $github_url, $created, $active) {
		$this->id = $id;
		$this->name = $name;
		$this->description = $description;
		$this->github_url = $github_url;
		$this->created = $created;
		$this->active = $active;
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function get_description() {
		return $this->description;
	}
	
	public function get_github_url() {
		return $this->github_url;
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
	
	public function set_description($description) {
		$this->description = $description;
		return $this->update();
	}
	
	public function set_github_url($github_url) {
		$this->github_url = $github_url;
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
		
		// Insert description if one is provided
		$description_field = "";
		$description_value = "";
		if ($this->description != null) {
			$description_field = ", description=";
			$description_value = "'" . $mysqli->real_escape_string($this->description) . "'";
		}
		
		// Insert github username if one is provided
		$github_field = "";
		$github_value = "";
		if ($this->github_url != null) {
			$github_field = ", github_url=";
			$github_value = "'" . $mysqli->real_escape_string($this->github_url) . "'";
		}
	
		$result = $mysqli->query("UPDATE `Group` SET " .
			"name=" . "'" . $mysqli->real_escape_string($this->name) . "'" .
			$description_field . $description_value .
			$github_field . $github_value .  
			", active=" . $active . 
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
		      'description' => $this->description,
		      'github_url' => $this->github_url,
		      'created' => $created,
		      'active' => $this->active);
    	return json_encode($json_obj);
  	}
}