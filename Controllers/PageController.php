<?php
namespace Theophilus\Controllers;

class PageController {

	public function get($request) {
		$id = $request->str('id', 'none');
		
		if(auth_quickaclcheck($id) < AUTH_READ){
            header('HTTP/1.0 403 Forbidden');
            return;
        }
        
		if (page_exists($id, $rev='', $clean=true, $date_at=false)) {
			$content = p_wiki_xhtml($id, $rev, false);
			return json_encode($content);
		}
		
		else {
			header("HTTP/1.0 404 Not Found");
			
		}
		
	}
	
	public function raw($request) {
		$id = $request->str('id', 'none');
		
        if(auth_quickaclcheck($id) < AUTH_READ){
            header('HTTP/1.0 403 Forbidden');
            return;
        }
        $text = rawWiki($id);
        $title = p_get_metadata($id, 'title');
        if(!$text) {
            $data = array($id);
            header("HTTP/1.0 404 Not Found");
            return;
        } else {
            return json_encode($text);
        }
    }
    
    public function exists($request) {
    	$id = $request->str('id', '');
    	if (page_exists($id, $rev='', $clean=true, $date_at=false)) {
			return json_encode(true);
		} else {
			return json_encode(false);
		}
    }
	
	public function save($request) {
        global $TEXT;
        global $lang;
        global $conf;

        $id    = cleanID($request->str('id', 'none'));
        $TEXT  = cleanText($request->str('text'));
        $sum   = $request->str('sum', '');;
        $minor = $request->str('minor', '');;
		echo "saving " . $id . " mit Text: " . $TEXT;

        if(!page_exists($id) && trim($TEXT) == '' ) {
            
            return;
        }

        if(auth_quickaclcheck($id) < AUTH_EDIT) {
        	header('HTTP/1.0 403 Forbidden');
            return;
        }

        if(checklock($id)) {
            header('HTTP/1.0 423 Locked');
            return;
        }

        lock($id);
		saveWikiText($id,$TEXT,$sum,$minor);
		unlock($id);

        return json_encode(true);
    }
	
	
}