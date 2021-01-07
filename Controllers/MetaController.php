<?php
namespace Theophilus\Controllers;

class MetaController {

	public function get($request) {
		$id = $request->str('id', 'none');
		$data = p_get_metadata($id)['extra'];
		if ($data == null) {
				$data = array(
					'image' => '',
					'icon' => '',
					'locked' => false,
					'date' => '',
					'color' => ''
				);
		}
		return json_encode($data);
	}
	

	
	public function set($request) {
		
        global $conf;
		global $_POST;
        
		$id = $request->str('id', 'none');
		p_set_metadata($id, array('extra' => $request->arr('extra')));
		return json_encode(p_get_metadata($id, 'extra'));
    }
	
	
}