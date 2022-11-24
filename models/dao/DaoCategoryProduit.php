<?php
require_once('../config/config.php');
require_once('FonctionsBdd.php');
require_once('../models/CategoryProduit.php');
require_once('../utilitaires/FonctionsUtiles.php');
//*********************************************** PRODUIT PRODUIT PRODUIT
//*********************************************** PRODUIT PRODUIT PRODUIT
//*********************************************** PRODUIT PRODUIT PRODUIT

class DaoCategoryProduit
{
    //ATTRIBUT DE LA CLASSE DaoProduit
    private $maConnection;

    //CONSTRUCTEUR DE LA CLASSE DaoProduit
    public function __construct()
    {
        //INSTANCIATION DE LA CONNEXION PAR APPEL AU CONSTRUCTEUR PDO ET VALORISATION DES ATTRIBUTS
        $this->maConnection = new PDO('mysql:host=' . HOST . ';dbname=' . DATABASE . ';charset=utf8;', USER, PASSWORD);
        //PARAMETRAGE POUR AFFICHAGE DES ERREURS RELATIVES A LA CONNEXION
        $this->maConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }


    function resultToObjects($result)
    {   //ON RECUPERE LE RÉSULTAT DE LA REQUETE DANS UN TABLEAU
        //QUI CONTIENDRA 1 OU PLUSIEURS OBJETS DE TYPE PRODUIT
        $listCategory = array();
        foreach ($result as $row) {
            $category = new CategoryProduit($row['CATEGORY_ID'], $row['CATEGORY_NAME'], $row['CATEGORY_LOGO']);
            array_push($listCategory, $category);
        }
        return $listCategory;
    }

    function getVendeurId($id): array
    {
        $query = $this->maConnection->prepare("SELECT CATEGORY_ID , CATEGORY_NAME,CATEGORY_LOGO
                                    FROM categoryproduit WHERE CATEGORY_ID =?");
        $query->execute(array(
            $id));
        $result = $query->fetchAll();
        //ON TRANSFORME LE RESULTAT EN TABLEAU CONTENANT UN OU PLUSIEURS OBJETS DE TYPE PRODUIT
        return $this->resultToObjects($result);
    }
    function getAll(): array
    {
        $query = $this->maConnection->prepare("SELECT CATEGORY_ID , CATEGORY_NAME,CATEGORY_LOGO
                                    FROM categoryproduit");
        $query->execute();
        $result = $query->fetchAll();
        //ON TRANSFORME LE RESULTAT EN TABLEAU CONTENANT UN OU PLUSIEURS OBJETS DE TYPE PRODUIT
        return $this->resultToObjects($result);
    }

    function rechercheVendeur()
    {

        //ON APPELLE LA FONCTION QUI VA FAIRE LA REQUETE AUPRES DE LA BASE DE DONNEES
        //CETTE FONCTION RENVOIE UN TABLEAU CONTENANT UN OU PLUSIEURS OBJETS DE TYPE LIGNEDECOMMANDE
        $lesCategory = $this->getAll();
        //ON AFFICHE LE HTML POUR LE FICHIER "AfficherLigneCommandes"
        $recherche = "

    <label for='vendeur' hidden></label>
    <select name='vendeur' id='nomProduit' class='header-select' onchange='this.form.submit()'>
        <option value=''>Choisir un Vendeur</option>";

        foreach ($lesCategory as $category) {
            $recherche .= "<option value=" . $category->getCategoryId() . ">" . $category->getCategoryName() . "</option>";

        }

        $recherche .= "</select>";

        return $recherche;
    }

}