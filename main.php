<?php

global $ID;
global $INFO;
global $INPUT;

if (auth_quickaclcheck("start") == AUTH_NONE) {
    include_once('auth.php');
	die();
}

require __DIR__ . '/vendor/autoload.php';

$request = new \Theophilus\Classes\Request();

// AJAX-Abfragen werden abgefangen und ihrer Klasse zugeordnet
if ($request->str('controller', false, true)) {
	$controllerName = ucfirst ( $request->str('controller'));
	$controllerName = "Theophilus\\Controllers\\" . $controllerName . "Controller";
	$controller = new $controllerName;
	
	if (method_exists( $controller, $request->str('method', 'get'))) {
    	header('Content-Type: application/json');
    	echo call_user_func_array ( array($controller, $request->str('method', 'get')), [$request]);
    } else {
    	echo "Fehler: Funktion nicht gefunden";
    }
// Für den Upload von Bildern brauchen wir eine andere HTTP-Methode
} elseif ($INPUT->str('method') === "upload") {
	$controller = new \Theophilus\Controllers\MediaController;
	$controller->upload();
}

// Bei normalen HTTP-Abfragen: an die entsprechende App weiterleiten
else {
	
	// Twig initiieren  	
	$loader = new \Twig\Loader\FilesystemLoader(DOKU_TPLINC . '/Views/');
	$twig = new \Twig\Environment($loader, array(
		// Cache im Production-Mode wieder freischalten!
		//'cache' => 'cache/',
	));
	
	// Twig-Funktionen für Dokuwiki hinzufügen
	$twig->addExtension(new \Theophilus\Classes\TemplateExtensions());

	// Im Normalmodus ("show") App-abhängig das Template laden
	
		$path = explode(":", $ID);
		$available_apps = array("music" => "Musik", "documents" => "Dokumente", "recipes" => "Rezepte", "peaks" => "Bergtouren", "sermons" => "Predigten", "protocols" => "Protokolle", "start" => "Startseite", "translate" => "Übersetzungen", "books" => "Bücher");
		
		if (array_key_exists($path[0], $available_apps))
		{
			echo $twig->render('master.twig', array('act' => $ACT, 'app' => $path[0], 'apps' => $available_apps, 'id' => $ID, 'info' => $INFO, 'auth' => auth_quickaclcheck($ID), 'path' => $path, 'base' => DOKU_URL, 'assets' => DOKU_TPL . "Public/dist/"));
		}
		else
		{
		  	http_response_code(404);
		}
	
	

}





?>

