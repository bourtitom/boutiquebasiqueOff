<?php


session_start();


/* On vérifie l'existence du panier, sinon, on le crée */
if(!isset($_SESSION['panier']))
{
    /* Initialisation du panier */
    $_SESSION['panier'] = array();
    /* Subdivision du panier */
    $_SESSION['panier']['produitId'] = array();
    $_SESSION['panier']['qte'] = array();
}

//Rajout d'un produit dans le panier
array_push($_SESSION['panier']['produitId'],$_GET['id']);
array_push($_SESSION['panier']['qte'],1);

//Afficher le contenu du panier
var_dump($_SESSION['panier']);

