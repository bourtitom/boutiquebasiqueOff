<?php
require ('../models/dao/DaoCommande.php');

class ControllerCommande
{
    private $daoCommande;

    public function __construct()
    {
        //on instancie un objet de type daoClient
        //en utilisant la variable $daoClient
        $this->daoCommande = new DaoCommande();
    }
    public function showAll()
    {
        if($_SESSION["client"]['id'] == 0){
            $recherche = $this->daoCommande->rechercheCommande();
        }
            $contenu = $this->daoCommande->afficherCommandes();


        require('../views/Layout.php');
    }
    /** Enregistrement du nouveau produit dans la base de données**/
    public function store()
    {


        //On appelle la méthode qui crée le produit
        //cette méthode renvoie l'ID du dernier produit inséré
        $where = $this->daoCommande->createCommande();


        $recherche = $this->daoCommande->rechercheCommande();
        //On récupère la methode de daoClient qui recherche les clients
        //et qui les retourne sous forme de variable $contenu que l'on passe à la vue concernée.
        $contenu = $this->daoCommande->afficherCommandes($where);
        require('../views/Layout.php');
    }
}