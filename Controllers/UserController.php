<?php
namespace Theophilus\Controllers;

class UserController {

	public function login($request){
        global $conf;
        global $auth;
        $user = $request->str('user', 'none');
        $pass = $request->str('pass', 'none');
        $sticky = $request->bool('sticky', false);
        if(!$conf['useacl'])
        {
            header("HTTP/1.0 404 Not Found");
        }
        if(!$auth)
        {
            header("HTTP/1.0 400 Forbidden");
        }
        else
        {
        	
        	$login = auth_login($user,$pass,$sticky,true);
        	if (!$login) {
        		header("HTTP/1.0 401 Unauthorized");
        	}
        	
            return json_encode(true);
        }
    }
	
	
}