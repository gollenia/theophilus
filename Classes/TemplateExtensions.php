<?php
namespace Theophilus\Classes;
/**
 * Encapsulates access to the $_REQUEST array, making sure used parameters are initialized and
 * have the correct type.
 *
 * All function access the $_REQUEST array by default, if you want to access $_POST or $_GET
 * explicitly use the $post and $get members.
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
class TemplateExtensions extends \Twig\Extension\AbstractExtension {
	public function getFunctions() {
		return array(
			new \Twig\TwigFunction('get_page', array($this, 'getPage')),
			new \Twig\TwigFunction('get_raw_page', array($this, 'getRawPage')),
			new \Twig\TwigFunction('get_index', array($this, 'getIndex')),
			new \Twig\TwigFunction('get_extras', array($this, 'getExtras')),
			new \Twig\TwigFunction('get_breadcrumbs', array($this, 'getBreadcrumbs')),
			new \Twig\TwigFunction('get_data', array($this, 'getData')),
			new \Twig\TwigFunction('dump', array($this, 'dump')),
			new \Twig\TwigFunction('get_tree', array($this, 'getTree'))
        );
	}
	
	public function getFilters() {
		return array(
			new \Twig\TwigFilter('dump', array($this, 'dump'))
        );
	}
	
	public function getPage($id = false) {
		if (!$id) {
			global $ID;
			$id = $ID;
		}
		
		if(auth_quickaclcheck($id) < AUTH_READ){
            header('HTTP/1.0 403 Forbidden');
            return;
        }
        
		if (page_exists($id, $rev='', $clean=true, $date_at=false)) {
			$content = p_wiki_xhtml($id, $rev, false);
			return $content;
		}
	}
	
	public function getTree($ns) {
		$tree = new \Theophilus\Controllers\IndexController;
		return $tree->tree($ns);
	}
	
	// Daten aus einem Datenbankmodell holen
	public function getData($model, $method, $args) {
	
		$controllerName = ucfirst ($model);
		
		if (!include_once(DOKU_TPLINC . '/Models/' . $controllerName . ".php")) {
			return "Fehler: " . $controllerName . ".php".  " nicht gefunden";
		}
		
		$controllerName = "Theophilus\\Models\\" . $controllerName;
		$controller = new $controllerName;
		
		if (method_exists( $controller, $method)) {
	    	return call_user_func_array ( array($controller, $method), [$args]);
	    } else {
	    	echo "Fehler: Funktion nicht gefunden";
	    }
		
	}
	
	// Dokuwiki-Metadaten aus von einer Seite laden
	public function getExtras($id = false, $json = false) {
		if (!$id) {
			global $ID;
			$id = $ID;
		}
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
		if ($json) {
			 return json_encode($data);
		}
		return $data;
	}
	
	// Rohdaten einer Wiki-Seite laden
	public function getRawPage($id = false) {
		if (!$id) {
			global $ID;
			$id = $ID;
		}
		
		if(auth_quickaclcheck($id) < AUTH_READ){
            return false;
        }
        
		if (page_exists($id, $rev='', $clean=true, $date_at=false)) {
			$text = rawWiki($id);
			return $text;
		}
		
		return false;
	
	}
	
	public function getIndex($id = false, $depth = 1) {

        global $conf;
        global $INFO;
        
		if (!$id) {
			global $ID;
			$id = $ID;
		}
		
        $data = array();
		$id = str_replace(':', "/", $id);
        $this->counter = 0;
        search($data,$conf['datadir'],array($this,'_search'),array(),$id,1,'natural');
        $this->counter = 0;
        

        	
        foreach($data as $key => $value) {
    		$subitems = array();
    		$meta = p_get_metadata($value['id']);
    		
    		$subid = str_replace(':', "/", $value['id']);
    		if ($depth==2) {
    			search($subitems, $conf['datadir'], array($this,'_search'), array(), $subid, 1,'natural');
       			$data[$key]['subitems'] = $subitems;
        	}
        }
        
        return $data;
    }
    
    // interne Callback-Funktion, um Seiten zu durchsuchen
    function _search(&$data,$base,$file,$type,$lvl,$opts){
        global $conf;
        $this->counter++;
        $return = true;
        $item = array();
        $id = pathID($file);
        if($type == 'd' && !(
            preg_match('#^'.$id.'(:|$)#',$opts['ns']) ||
            preg_match('#^'.$id.'(:|$)#',getNS($opts['ns']))
        )){
            //add but don't recurse
            $return = false;
        }elseif($type == 'f' && (!empty($opts['nofiles']) || substr($file,-4) != '.txt')){
            //don't add
            return false;
        }
        if($type=='d' && $conf['sneaky_index'] && auth_quickaclcheck($id.':') < AUTH_READ){
            return false;
        }
        if($type == 'd'){
            // link directories to their start pages
            $exists = false;
            $id = "$id:";
            resolve_pageid('',$id,$exists);
            $this->startpages[$id] = 1;
        }elseif(!empty($this->startpages[$id])){
            // skip already shown start pages
            return false;
        }elseif(noNS($id) == $conf['start']){
            // skip the main start page
            return false;
        }
        //check hidden
        if(isHiddenPage($id)){
            return false;
        }
        //check ACL
        if($type=='f' && auth_quickaclcheck($id) < AUTH_READ){
            return false;
        }
        $meta = p_get_metadata($id);
        
        if ($meta['title'] == null) {
        	$title = $meta['extras']['title'];
        } else {
        	$title = $meta['title'];
        }
        array_push ( $data , array( 
        	'id'    => $id,
            'extras' => $meta['extras'],
            'title' => $title
            ) );
        
        return $return;
    }
    
    public function dump($content) {
    	echo "<script> console.log(" . json_encode($content) . ");</script>";
    }
    
    public function pageTitle($id) {
    	return tpl_pagetitle($id, true);
    }
    
     public function getBreadcrumbs($id) {
    	$path = explode(":", $id);
    	array_pop ( $path );
    	$breadcrumbs = array();
    	$pathBuilder = "";
    	foreach ($path as $key => $value) {
    		if ($key == 0) {
    			$pathBuilder .= $value;
    		} else {
    			$pathBuilder .= ":" . $value;
    		}
    		$title = p_get_first_heading($pathBuilder);
    		if (is_null($title)) {
    			$title = ucfirst($value);
    		}
    		array_push($breadcrumbs, array('id' => $pathBuilder, 'title' =>  $title));
    	}
    	return $breadcrumbs;
    }
}