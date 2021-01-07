<?php

$request = new \Theophilus\Classes\Request();
if ($request->str('controller', false, true)) {
	$user = new \Theophilus\Controllers\UserController();
	$user->login($request);
// Für den Upload von Bildern brauchen wir eine andere HTTP-Methode
}


$loader = new Twig_Loader_Filesystem(DOKU_TPLINC . '/Views/');
$twig = new Twig_Environment($loader, array(
	// Cache im Production-Mode wieder freischalten!
	//'cache' => 'cache/',
));

echo $twig->render('auth.twig', array('id' => $ID, 'info' => $INFO, 'base' => DOKU_URL, 'assets' => DOKU_TPL . "Public/dist/"));