<?php
namespace Theophilus\Controllers;

class MusicController {

    public function __construct() {
    	include_once(DOKU_TPLINC . '/models/Song.php');
    	include_once(DOKU_TPLINC . '/models/SongCategory.php');
		$this->song = new \Theophilus\Models\Song();
		$this->category = new \Theophilus\Models\SongCategory();
	}
	
	
	public function get($request) {
		$name = $request->str('name');
		return $this->song->get($name);
	}
	
	public function save($request) {
		$data = $request->arr('data');
		return $this->song->save($data);
    }
    
    public function saveCategory($request) {
		$data = $request->arr('data');
		return $this->category->save($data);
    }
    
    
	public function list($request) {
		$name = $request->str('name', 'none');
		return $this->song->list($list);
	}
	
	
	public function search($request) {
		$string = $request->str('string', 'none');
		return json_encode($this->song->search($string));
	}
	
}