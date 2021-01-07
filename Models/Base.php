<?php

namespace Theophilus\Models;

class Base {
	
	private $db;
	private $fields = array();
	private $table = "";
	private $acl = false;
	
	public function __construct($dbfile, $table, $acl = false) {
		$this->db = new \PDO('sqlite:' . DOKU_INC . '/data/' . $dbfile . '.db');
		$this->table = $table;
		$this->acl = $acl;
	}
	
	public function get($name) {
		if ($this->acl) {
			if(auth_quickaclcheck($acl) < AUTH_READ){
	            return false;
	        }
		}
		$result = $this->db->query("SELECT * FROM songs WHERE name = '" . $name . "'");
		return $result->fetch(\PDO::FETCH_ASSOC);
	}
	
	public function delete($name) {
		if(auth_quickaclcheck('music') < AUTH_DELETE){
            header('HTTP/1.0 403 Forbidden');
            return;
        }
		$result = $this->db->query("DELETE FROM songs WHERE name = '" . $name . "'");
		return $result->fetch();
	}
	
	public function save($data) {
		if(auth_quickaclcheck('music') < AUTH_CREATE){
            header('HTTP/1.0 403 Forbidden');
            return;
        }
        
		$result = $this->db->query("SELECT * FROM songs WHERE name = '" . $data['name'] . "'");
		if (!$result->fetch()) {
			$query = $this->queryCreate($data);
			$result = $this->db->exec($query);
		}
		else {
			$query = $this->queryUpdate($data, array('name' => $data['name']));
			$result = $this->db->exec($query);
		}
		return $result;
	}
	
	public function show($search = false) {
		if(auth_quickaclcheck('music') < AUTH_READ){
            header('HTTP/1.0 403 Forbidden');
            return;
        }
        
        
        
		$result = $this->db->query("SELECT name, title, subtitle, category, bible, key, artist FROM songs");
		return $result->fetchAll();
	}
	
	private function queryCreate($data) {
		$keys = array();
		$values = array();
		foreach($this->fields as $key => $type) {
				switch ($type) {
					case 'string':
						array_push($keys, $key);
						if (array_key_exists ( $key , $data )) {
	
							array_push($values, $this->db->quote($data[$key]));
							break;
						}
						array_push($values, "''");
						break;
					case 'integer':
						array_push($keys, $key);
						if (array_key_exists ( $key , $data ) && is_int ( $data[$key] )) {
							array_push($values, $data[$key]);
							break;
						}
						array_push($values,0);
						break;
				}
			
		}
		return "INSERT INTO songs(" . join($keys, ',')  . ") VALUES(" . join($values, ',')  . ")"	;
	}
	
	
	
	private function queryUpdate($data, $where) {
		$values = array();
		//echo(json_encode($data));
		foreach($this->fields as $key => $type) {
			
				switch ($type) {
					case 'string':
						if (array_key_exists ( $key , $data )) {
							array_push($values, $key . " = " . $this->db->quote($data[$key]));
							break;
						}
						array_push($values, "''");
						break;
					case 'integer':
						array_push($keys, $key);
						if (array_key_exists ( $key , $data ) && is_int ( $data[$key] )) {
							array_push($values, $key . " = '" . $data[$key] . "'");
							break;
						}
						array_push($values, $key . " = '0'");
						break;
				}
			
		}

		return "UPDATE songs SET " . join($values, ", ")  . " WHERE " . key($where)  . " = '" . reset($where) ."'";
			
	}
	
	
	private function createDummyData($name) {
		
	}
}