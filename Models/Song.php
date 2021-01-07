<?php

namespace Theophilus\Models;

class Song {
	
	private $db;
	private $fluent;
	private $dummy = array(
		"name" => "", 
		"title" => "", 
		"subtitle" => "", 
		"content" => "", 
		"category" => "", 
		"composer" => "", 
		"melody" => "", 
		"artist" => "", 
		"key" => "", 
		"bpm" => "", 
		"bible" => "", 
		"ccli" => "", 
		"copyright" => "", 
		"extras" => ""
	);
	
	public function __construct() {
		$this->db = new \PDO('sqlite:' . DOKU_INC . '/data/music.db');
		$this->fluent = new \FluentPDO($this->db);
	}
	
	public function get($name) {
		if(auth_quickaclcheck('music') < AUTH_READ){
            return false;
        }
		$query = $this->fluent->from('songs')
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
		return $this->fluent->deleteFrom('songs')->where('name', $name)->execute();
		
	}
	
	public function save($data) {
		if(auth_quickaclcheck('music') < AUTH_CREATE){
            return;
        }
        
		$query = $this->fluent->from('songs')
            ->where('name', $data['name']);
		
		if (!$query->fetch()) {
			$query = $this->fluent->insertInto('songs', $data)->execute();
			
		}
		else {
			$query = $this->fluent->update('songs')->set($data)->where('name', $data['name'])->execute();
			
		}
		
	}
	
	public function show($search = false) {
		if(auth_quickaclcheck('music') < AUTH_READ){
            header('HTTP/1.0 403 Forbidden');
            return;
        }
		$result = $this->db->query("SELECT name, title, subtitle, category, bible, key, artist FROM songs");
		return $result->fetchAll(\PDO::FETCH_ASSOC);
	}
	
	public function category($category) {
		if(auth_quickaclcheck('music') < AUTH_READ){
            header('HTTP/1.0 403 Forbidden');
            return;
        }
		$result = $this->db->query("SELECT name, title, subtitle, category, bible, key, artist FROM songs WHERE category = '" . $category . "'");
		return $result->fetchAll(\PDO::FETCH_ASSOC);
	}
	
	public function search($string) {
		if(auth_quickaclcheck('music') < AUTH_READ){
            header('HTTP/1.0 403 Forbidden');
            return;
        }
        $params = [':search' => "%$string%"];
        $query = $this->fluent->from('songs')
        	->where('(title LIKE :search OR subtitle LIKE :search OR artist LIKE :search OR bible LIKE :search)', $params)
        	->select(NULL)
        	->select('name, title, subtitle, category, bible, key, artist');
        $result = $query->fetchAll();
		return $result;
	}
	
	
}