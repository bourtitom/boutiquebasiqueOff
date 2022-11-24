<?php
require('../models/dao/DaoProduit.php');

class ControllerProduit
{
    //on déclare un attribut de type daoProduit
    private $daoProduit;
    private $recherche;
    private $panierbasenbr;
    public function __construct()
    {
        //on instancie un objet de type daoProduit
        //en utilisant la variable $daoProduit
        $this->daoProduit = new DaoProduit();
        $this->recherche = $this->daoProduit->rechercheProduit();
        $this->panierbasenbr = 0;
    }

    /** Affichage du formulaire de création d'un produit **/
    public function showCreate()
    {
        //On appelle la méthode qui permet d'afficher la barre de recherche
        $recherche = $this->recherche;
        //On appelle le formulaire de création de produit
        require('../views/CreerProduit.php');
    }

    /** Enregistrement du nouveau produit dans la base de données**/
    public function store()
    {
        //On appelle la méthode qui crée le produit
        //cette méthode renvoie l'ID du dernier produit inséré
        $where = $this->daoProduit->createProduit();
        //On appelle la méthode qui permet d'afficher la barre de recherche
        $recherche = $this->daoProduit->rechercheProduit();
        //On récupère $contenu et on le passe à la vue concernée
        $contenu = $this->daoProduit->afficherProduits($where);
        require('../views/Layout.php');
    }

    /** Affichage des produits **/
    public function showAll()
    {

        //On appelle la méthode qui permet d'afficher la barre de recherche
        $recherche = $this->daoProduit->rechercheProduit();

        //On récupère la methode de daoProduit qui recherche les produits
        //et qui les retourne sous forme de variable $contenu que l'on passe à la vue concernée.
        $contenu = $this->daoProduit->afficherProduits();


        require('../views/Layout.php');
    }

    /** Affichage du formulaire de modification d'un produit **/
    public function showModify()
    {
        //On appelle la méthode qui permet d'afficher la barre de recherche
        $recherche = $this->daoProduit->rechercheProduit();
        //On appelle la méthode de la classe DaoProduit qui retourne le produit concerné
        $produit = $this->daoProduit->afficherFormModif();
        //On récupère $produit et on le passe à la vue concernée
        require('../views/ModifierProduit.php');
    }

    /** Enregistrement des modifications dans la base de données **/
    public function update()
    {
        //On appelle la méthode qui permet d'afficher la barre de recherche
        $recherche = $this->daoProduit->rechercheProduit();
        //On appelle la méthode qui permet de mettre à jour le produit dansDaoProduit
        $where = $this->daoProduit->updateProduit();
        //On récupère $contenu et on le passe à la vue concernée
        $contenu = $this->daoProduit->afficherProduits($where);
        require('../views/Layout.php');
    }

    /** Suppression d'un produit **/
    public function delete()
    {
        //On appelle la méthode qui supprime le produit
        $this->daoProduit->deleteProduit();
        //On appelle la méthode qui permet d'afficher la barre de recherche
        $recherche = $this->daoProduit->rechercheProduit();
        //On récupère $contenu et on le passe à la vue concernée
        $contenu = $this->daoProduit->afficherProduits();
        require('../views/Layout.php');
    }
    /** Ajouter un produit au panier **/
    public function ajouterAuPanier()
    {
        //On appelle la méthode qui rajoute le produit au panier
        $this->daoProduit->ajouterAuPanier();
        $this->showAll();
    }


    /** Ajouter un produit au panier **/
    public function modifieQuantityPanier()
    {
        //On appelle la méthode qui rajoute le produit au panier
        $this->daoProduit->modifieQuantityPanier();
        //On récupère $contenu et on le passe à la vue concernée
        $contenu = $this->daoProduit->afficherPanier()[1];
        $montant = $this->daoProduit->afficherPanier()[0];

        require('../views/Panier.php');
    }



    /** Afficher le contenu du panier **/
    public function montrerPanier()
    {
        //On récupère $contenu et on le passe à la vue concernée
        $contenu = $this->daoProduit->afficherPanier()[1];
        $montant = $this->daoProduit->afficherPanier()[0];
        require('../views/Panier.php');
    }

}