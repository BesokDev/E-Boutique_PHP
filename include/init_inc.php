<?php

/////////////////////////////////////////////////////////////
// ******************** CONNEXION BDD ******************** //
// LOCAL HOST
$bdd = new PDO('mysql:host=localhost;dbname=boutique', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

// INFINITY FREE
// $bdd = new PDO('mysql:host=sql200.epizy.com;dbname=epiz_27185174_boutique', 'epiz_27185174', 'JqoMhBPDKoP', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

/////////////////////////////////////////////////////////////
// ********************** SESSION ************************ //
session_start();

////////////////////////////////////////////////////////////////////
// *********************** CONSTANTES (chemin) ****************** //

// Defini une constante de chemin physique sur le pc pour le serveur 
define("RACINE_SITE", $_SERVER["DOCUMENT_ROOT"] . "/PHP/09-Boutique/"); // $_SERVER['DOCUMENT_ROOT'] = C://xampp/htdocs

// INFINITY FREE
// define("RACINE_SITE", $_SERVER['DOCUMENT_ROOT'] . '/');


// Cette constante retourne le chemin physique du dossier 09-boutique sur le serveur local xampp.
// Lors de l'enregistrement d'une image/photo, nous aurons besoin du chemin physique complet vers le dossier photo sur le serveur pour enregistrer la photo dans le bon dossier
// On appel $_SERVER['DOCUMENT_ROOT'] parce que chaque serveur possède des chemins diffdérents 


// echo RACINE_SITE . '<hr>'; // = C:/xampp/htdocs/PHP/09-Boutique/ 

/*ON AURA PLUS QU'A CONCAT LE NOM DU DOSSIER POUR ACCEDER AUX DIFFERENTS DOSSIERS
====>  RACINE_SITE . 'photo'; ===> pour acceder au dossier 'photo'
*/

define("URL", "http://localhost:8080/PHP/09-Boutique/");
// cette constante sert à enregistrer l'URL d'une image ou photo dans la BDD

// INFINITY FREE
// define("URL", "http://nevergrowup-boutique.rf.gd/");


////////////////////////////////////////////////////////////////////
// *********************** INCLUSION ****************** //
require_once('fonctions_inc.php');
// en appelant init_inc.php sur chaque fichier, on inclus en même temps les fonctions déclarées

?>