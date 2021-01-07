<?php

namespace Theophilus\Models;

class SongCategory {
	
	private $db;
	private $fluent;
	private $dummy = array(
		"name" => "", 
		"title" => "", 
		"icon" => "", 
	);
	
	public function __construct() {
		$this->db = new \PDO('sqlite:' . DOKU_INC . '/data/music.db');
		$this->fluent = new \FluentPDO($this->db);
	}
	
	public function get($name) {
		if(auth_quickaclcheck('music') < AUTH_READ){
            return false;
        }
		$query = $this->fluent->from('categories')
            ->where('name', $name);
		$result = $query->fetch();
		if (!$result) {
			$response = $this->dummy;
			$response['name'] = $name;
			return $response;
		}
		return $result;
	}
	
	public function delete($name) {
		if(auth_quickaclcheck('music') < AUTH_DELETE){
            return;
        }
		return $this->fluent->deleteFrom('categories')->where('name', $name)->execute();
		
	}
	
	public function save($data) {
		if(auth_quickaclcheck('music') < AUTH_CREATE){
            return;
        }
        
		$query = $this->fluent->from('categories')
            ->where('name', $data['name']);
		
		if (!$query->fetch()) {
			
			$query = $this->fluent->insertInto('categories', $data)->execute();
		}
		else {
			$query = $this->fluent->update('categories')->set($data)->where('name', $data['name']);
			echo $query;
			$query->execute();
		}
		return $result;
	}
	
	public function show($search = false) {
		if(auth_quickaclcheck('music') < AUTH_READ){
            header('HTTP/1.0 403 Forbidden');
            return;
        }
		$result = $this->db->query("SELECT * FROM categories");
		return $result->fetchAll(\PDO::FETCH_ASSOC);
	}
	
	
}