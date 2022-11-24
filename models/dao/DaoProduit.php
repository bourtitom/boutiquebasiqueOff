<?php
require_once('../config/config.php');
require_once('FonctionsBdd.php');
require_once('../models/Produit.php');
require_once('../utilitaires/FonctionsUtiles.php');
require_once('DaoCategoryProduit.php');

//*********************************************** PRODUIT PRODUIT PRODUIT
//*********************************************** PRODUIT PRODUIT PRODUIT
//*********************************************** PRODUIT PRODUIT PRODUIT


class DaoProduit
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

//CETTE FONCTION PERMET DE CREER UN NOUVEAU PRODUIT
    function createProduit(): string
    {
        //ON INSTANCIE UN PRODUIT EN PASSANT DANS LE CONSTRUCTEUR LES VALEURS POSTEES VIA LE FORMULAIRE DE CREATION D UN PRODUIT
        $produit = new Produit(0, $_POST['produitNom'], $_POST['produitPrix'], $_POST['produitImage']);

        //ON UTILISE LA METHODE prepare() de PDO POUR FAIRE UNE REQUETE PARAMETREE
        $query = $this->maConnection->prepare("INSERT INTO produit(PRODUIT_NOM, PRODUIT_PRIX, PRODUIT_IMAGE) 
                                                        VALUES (?, ?, ?)");
        $result = $query->execute(array(
            $produit->getProduitNom(),
            $produit->getProduitPrix(),
            $produit->getProduitImage()
        ));

        //ON RECUPERE L'ID DU NOUVEAU PRODUIT INSERE
        $nouvelid = $this->maConnection->lastInsertId();
        return $nouvelid;
    }


//CETTE FONCTION PERMET DE METTRE A JOUR UN PRODUIT
    function updateProduit(): string
    {
        if (empty($_POST['newImage'])) {
            $image = $_POST['produitImage'];
        } else {
            $image = $_POST['newImage'];
        }

        $produit = new Produit($_POST['produitId'], $_POST['produitNom'], $_POST['produitPrix'], $image);

        $query = $this->maConnection->prepare("UPDATE produit SET PRODUIT_NOM = ?, PRODUIT_PRIX =? , PRODUIT_IMAGE = ? WHERE  PRODUIT_ID = ?");
        //ON APPELLE LA FONCTION QUI VA  EXECUTER LA REQUETE
        $result = $query->execute(array(
            $produit->getProduitNom(),
            $produit->getProduitPrix(),
            $produit->getProduitImage(),
            $produit->getProduitId()

        ));


        //ON RENVOIE L ID DU PRODUIT AU CONTROLEUR POUR QU il LE TRANSMETTE A LA VUE AFFICHERPRODUITS


        return $where = "PRODUIT_ID=" . $produit->getProduitId();

    }

    function deleteProduit(): void
    {
        $query = $this->maConnection->prepare("DELETE FROM produit WHERE PRODUIT_ID =?");
        $result = $query->execute(array($_POST['produitId']));
    }

    function resultToObjects($result)
    {   //ON RECUPERE LE RÉSULTAT DE LA REQUETE DANS UN TABLEAU
        //QUI CONTIENDRA 1 OU PLUSIEURS OBJETS DE TYPE PRODUIT
        $listProduits = array();
        foreach ($result as $row) {
            $produit = new Produit($row['PRODUIT_ID'], $row['PRODUIT_NOM'], $row['PRODUIT_PRIX'], $row['PRODUIT_IMAGE']);
            array_push($listProduits, $produit);
        }
        return $listProduits;
    }

    function getProduitById($id): array
    {
        $query = $this->maConnection->prepare("SELECT PRODUIT_ID, PRODUIT_NOM,PRODUIT_PRIX ,PRODUIT_IMAGE FROM produit WHERE PRODUIT_ID =?");
        $query->execute(array(
            $id));
        $result = $query->fetchAll();
        //ON TRANSFORME LE RESULTAT EN TABLEAU CONTENANT UN OU PLUSIEURS OBJETS DE TYPE PRODUIT
        return $this->resultToObjects($result);
    }
    function getProduitByPrix($prix): array
    {
        $query = $this->maConnection->prepare("SELECT PRODUIT_ID, PRODUIT_NOM,PRODUIT_PRIX ,PRODUIT_IMAGE FROM produit WHERE PRODUIT_PRIX <?");
        $query->execute(array(
            $prix));
        $result = $query->fetchAll();
        //ON TRANSFORME LE RESULTAT EN TABLEAU CONTENANT UN OU PLUSIEURS OBJETS DE TYPE PRODUIT
        return $this->resultToObjects($result);
    }
    function getProduitByVendeur($vendeurId): array
    {

        $outilCategory = new DaoCategoryProduit();

        $query = $this->maConnection->prepare("SELECT PRODUIT_ID, PRODUIT_NOM,PRODUIT_PRIX ,PRODUIT_IMAGE, CATEGORY_ID FROM produit WHERE CATEGORY_ID =?");
        $query->execute(array(
            $vendeurId));
        $result = $query->fetchAll();
        //ON TRANSFORME LE RESULTAT EN TABLEAU CONTENANT UN OU PLUSIEURS OBJETS DE TYPE PRODUIT
        return $this->resultToObjects($result);
    }

    function getAll(): array
    {
        $query = $this->maConnection->prepare("SELECT PRODUIT_ID, PRODUIT_NOM,PRODUIT_PRIX ,PRODUIT_IMAGE FROM produit");
        $query->execute();
        $result = $query->fetchAll();
        //ON TRANSFORME LE RESULTAT EN TABLEAU CONTENANT UN OU PLUSIEURS OBJETS DE TYPE PRODUIT
        return $this->resultToObjects($result);
    }


//CETTE FONCTION PERMET D'AFFICHER TOUT LE CATALOGUE PRODUIT SI L'ID RECUE EN PARAMETRE EST NULL
//SI L'ID EST RENSEIGNE ELLE AFFICHERA UN SEUL PRODUIT
    function afficherProduits($produitId = null): string
    {

        $outilCategory = new DaoCategoryProduit();
        $lesProduits = array();
        if($produitId !== null ){
            $lesProduits = $this->getProduitById($produitId);
        }
        else if (!empty($_POST['prixProduit'])) {
            /* récupérer les données du formulaire en utilisant
               la valeur des attributs name comme clé */
            $lesProduits = $this->getProduitByPrix($_POST['prixProduit']);
        }else if (!empty($_POST['produitId'])) {
            /* récupérer les données du formulaire en utilisant
                la valeur des attributs name comme clé
               */
            $lesProduits = $this->getProduitById($_POST['produitId']);
        }else if (!empty($_POST['vendeur'])) {
            /* récupérer les données du formulaire en utilisant
               la valeur des attributs name comme clé
              */
        $lesProduits = $this->getProduitByVendeur($_POST['vendeur']);
        }
        else {
            //ON APPELLE LA METHODE QUI VA FAIRE LA REQUETE AUPRES DE LA BASE DE DONNEES
            //CETTE METHODE RENVOIE UN TABLEAU CONTENANT UN OU PLUSIEURS OBJETS DE TYPE PRODUIT
            $lesProduits = $this->getAll();
        }

        //ON APPELLE LA FONCTION QUI VA FAIRE LA REQUETE AUPRES DE LA BASE DE DONNEES
        //CETTE FONCTION RENVOIE UN TABLEAU CONTENANT UN OU PLUSIEURS OBJETS DE TYPE PRODUIT



        //ON CONSTRUIT LE HTML POUR LE FICHIER "AfficherProduits"
        $contenu =
            "<section id='slogan'>
        <h2>Catalogue Produits</h2></div ></section><div id='menu'>";
        foreach ($lesProduits as $produit) {
            $id = $produit->getProduitId();
            $contenu .= "<article class='article' >
            <div class='container' ><img class='image' src = '../assets/img/" . $produit->getProduitImage() . " ' alt=''></div >
             <h2 > " . $produit->getProduitNom() . "</h2 >
             <p > " . $produit->getProduitPrix() . " EUROS </p >
            <br>";

            if(isset($_SESSION["client"])) {

                if ($_SESSION["client"]['id'] == 0) {

                    $contenu .= "            <button id='submit'>
                <a href = '../controllers/Controller.php?todo=modifierProduit&id=$id' > MODIFIER LE PRODUIT </a>
            </button>";
                }

                if (isset($_SESSION["client"])) {

                    $contenu .= " <a href = '../controllers/Controller.php?todo=ajouterAuPanier&produitId=$id&prix=" . $produit->getProduitPrix() . "euro'>AJOUTER AU PANIER</a> ";

                }
            }
//            $logoVendeur = $outilCategory->rechercheVendeur()[0];
            $contenu .= "</article > ";
        }
//        if(!empty($_POST['vendeur'])){
//
//            echo "<div style='width: 10%; position: fixed; bottom: 1%'><img  style='width: 100%; class='image' src = '../assets/img/' alt=''></div>";
//
//        }
        //ON RENVOIE LE HTML AU CONTROLEUR QUI VA LE TRANSMETTRE A LA VUE
        return $contenu;
    }


//CETTE FONCTION PERMET D'AFFICHER UN FORMULAIRE DE RECHERCHE DE PRODUITS
    function rechercheProduit(): string
    {    //ON APPELLE LA FONCTION QUI VA FAIRE LA REQUETE AUPRES DE LA BASE DE DONNEES
        //CETTE FONCTION RENVOIE TOUS LES PRODUITS SOUS FORME DE TABLEAU D'OBJETS
        //POUR AFFICHER LES NOMS DES PRODUITS DANS LE SELECT
        $lesProduits = $this->getAll();
        $outilCategory = new DaoCategoryProduit();
        $rechercheVendeur = $outilCategory->rechercheVendeur();
        //ON CONSTRUIT LE HTML POUR LE FICHIER "ModifierProduit"
        $recherche = "
<form name='searchProduct' action='../controllers/Controller.php' method='post' class='search-form'>
            <input type='hidden' name='todo' value='afficherProduits'>

    <label for='nomProduit' hidden></label>
    <select name='nomProduit' id='nomProduit' class='header-select' onchange='this.form.submit()'>
        <option value=''>Choisir un produit</option>";

        foreach ($lesProduits as $produit) {
            $recherche .= "<option value=" . $produit->getProduitId() . ">" . $produit->getProduitNom() . "</option>";
        }

        $recherche .= "</select>
   <label for='prixProduit' hidden></label>
 <select name='prixProduit' id='prixProduit' class='header-select' onchange='this.form.submit()'>
                            <option value='' >choisir un prix</option>
                            <option value='50'  >Moins de 50 euros</option>
                            <option value='100' >Moins de 100 euros</option>
                            <option value='200' >Moins de 200 euros</option>
                            <option value='300' >Moins de 300 euros</option>
                            <option value='500' >Moins de 500 euros</option>
                            <option value='800' >Moins de 800 euros</option>
                            <option value='1000' >Moins de 1000 euros</option>
                            <option value='2000' >Moins de 2000 euros</option>
                           <option value='5000' >Moins de 5000 euros</option>  
                         </select>";
        $recherche .= $rechercheVendeur;
        $recherche .= " 
 
</form>";
        return $recherche;
    }


//CETTE FONCTION PREND EN GET DANS L URL UN ID PRODUIT
//ET RENVOIE PRODUIT
    function afficherFormModif(): Produit
    {
        //ON APPELLE LA METHODE QUI VA FAIRE LA REQUETE AUPRES DE LA BASE DE DONNEES
        //CETTE METHODE RENVOIE UN TABLEAU CONTENANT LE PRODUIT A MODIFIER
        //ON RETOURNE CET OBJET PRODUIT AU CONTROLEUR QUI A APPELLE LA METHODE
        //LE CONTROLEUR RETOURNERA L'OBJET A LA VUE "ModifierProduit";
        return $this->getProduitById($_GET['id'])[0];
    }

//CETTE FONCTION PERMET D AJOUTER UN PRODUIT AU PANIER EN SESSION
    function ajouterAuPanier(): void
    {

        /* On vérifie l'existence du panier, sinon, on le crée */
        if (!isset($_SESSION['panier'])) {
            /* Initialisation du panier */
            $_SESSION['panier'] = array();
            /* Subdivision du panier */
            $_SESSION['panier']['produitId'] = array();
            $_SESSION['panier']['qte'] = array();
        }
//ON vérifie si l'article existe déjà on rajoute 1 à quantité
        $rajoute = false;
        /* On parcourt le panier en session pour modifier l'article précis. */
        for ($i = 0; $i < count($_SESSION['panier']['produitId']); $i++) {
            if ($_GET['produitId'] == $_SESSION['panier']['produitId'][$i]) {
                $_SESSION['panier']['qte'][$i] = $_SESSION['panier']['qte'][$i] + 1;
                $rajoute = true;
            }
        }
        //Si le produit n'existe pas encore dans le panier, on le rajoute
        if (!$rajoute) {

//Rajout d'un produit dans le panier
            array_push($_SESSION['panier']['produitId'], $_GET['produitId']);
            array_push($_SESSION['panier']['qte'], 1);

        }
    }

    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////




    function supprim_article($ref_article)
    {
        $suppression = false;
            /* création d'un tableau temporaire de stockage des articles */
            $panier_tmp = array("produitId"=>array(),"qte"=>array(), "prix"=>array());
            /* Comptage des articles du panier */
            $nb_articles = count($_SESSION['panier']['produitId']);
            /* Transfert du panier dans le panier temporaire */
            for($i = 0; $i < $nb_articles; $i++)
            {
            /* On transfère tout sauf l'article à supprimer */
                if($_SESSION['panier']['produitId'][$i] != $ref_article)
                {
                    array_push($panier_tmp['produitId'],$_SESSION['panier']['produitId'][$i]);
                    array_push($panier_tmp['qte'],$_SESSION['panier']['qte'][$i]);
                    array_push($panier_tmp['prix'],$_SESSION['panier']['prix'][$i]);
                }
            }
            /* Le transfert est terminé, on ré-initialise le panier */
            $_SESSION['panier'] = $panier_tmp;
            /* Option : on peut maintenant supprimer notre panier temporaire: */
            unset($panier_tmp);
            $suppression = true;
//            return $suppression;
}





    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////


//CETTE FONCTION PERMET D AJOUTER ou supprimer UN PRODUIT AU PANIER EN SESSION
    function modifieQuantityPanier(): void
    {
        /* On vérifie l'existence du panier, sinon, on le crée */
        if (!isset($_SESSION['panier'])) {
            /* Initialisation du panier */
            $_SESSION['panier'] = array();
            /* Subdivision du panier */
            $_SESSION['panier']['produitId'] = array();
            $_SESSION['panier']['qte'] = array();
        }
//ON vérifie si l'article existe déjà on rajoute 1 à quantité
        $rajoute = false;
        /* On parcourt le panier en session pour modifier l'article précis. */


        for ($i = 0; $i < count($_SESSION['panier']['produitId']); $i++) {
            if ($_GET['produitId'] == $_SESSION['panier']['produitId'][$i]) {
                $rajoute = true;

                if($_GET['Qte'] == 'plus' || $_GET['Qte'] == 'add'){
                    $_SESSION['panier']['qte'][$i] = $_SESSION['panier']['qte'][$i] + 1;
                }
                if($_GET['Qte'] == 'moins') {

                    if($_SESSION['panier']['qte'][$i] <= 1){
                    echo "je supp";
//                        unset($_SESSION['panier']['produitId'][array_search($_GET['produitId'],$_SESSION['panier']['produitId'])]);
//                        $_SESSION['panier']['qte'][$i] = $_SESSION['panier']['qte'][$i] - 1;
                    $this->supprim_article($_GET['produitId']);
                    }else{
                        $_SESSION['panier']['qte'][$i] = $_SESSION['panier']['qte'][$i] - 1;
                        $rajoute = true;
                    }
                }
                if($_GET['Qte'] == 'supp') {
                    $this->supprim_article($_GET['produitId']);

                }
                }
        }

        //Si le produit n'existe pas encore dans le panier, on le rajoute
        if (!$rajoute) {

//Rajout d'un produit dans le panier
            array_push($_SESSION['panier']['produitId'], $_GET['produitId']);
            array_push($_SESSION['panier']['qte'], 1);
        }

    }


//AFFICHER LE CONTENU D'UN PANIER
    function afficherPanier(): array
    {
        $contenu = "";
        $montant = 0;
        /* On vérifie l'existence du panier, sinon, on le crée */
        if (isset($_SESSION['panier'])) {
            /* On parcourt le panier en session pour afficher chaque produit ajouté. */

            for ($i = 0; $i < count($_SESSION['panier']['produitId']); $i++) {

                $produit = $this->getProduitById($_SESSION['panier']['produitId'][$i])[0];
                $qte = $_SESSION['panier']['qte'][$i];

                $id = $produit->getProduitId();

                $contenu .= "
 
                                   <a href = '../controllers/Controller.php?todo=modifieQuantityPanier&produitId=$id&prix=". $produit->getProduitPrix() ."euro&Qte=supp'> <img class='supp' src = '../assets/img/redcross.png' alt=''></a>
                                    <div class='ctG'> 
                                    <img class='imageInPanier' src = '../assets/img/" . $produit->getProduitImage() . " ' alt=''> 
                                    </div> 
                                    <div class='ctD'>
                        <h4>Nom :" . $produit->getProduitNom() . " </h4>
                        <h4><a href = '../controllers/Controller.php?todo=modifieQuantityPanier&produitId=$id&prix=". $produit->getProduitPrix() ."euro&Qte=plus'><img class='imageAddRemove' src = '../assets/img/plus.png' alt=''> </a>  Qte :" . $qte . " <a href = '../controllers/Controller.php?todo=modifieQuantityPanier&produitId=$id&prix=". $produit->getProduitPrix() ."euro&Qte=moins'><img class='imageAddRemove' src = '../assets/img/moins.png' alt=''> </a> </h4>

                         <h4>Prix Unité :" . $produit->getProduitPrix() . " </h4>
                    <div>". $produit->getProduitPrix()*$qte . "</div>

                                     </div> <div class='clear'></div> <br> <hr>
<br>";
                $montant += $produit->getProduitPrix()*$qte;

            }
            $TotalPanier = $montant . " €";

            $contenu .= "</div>";

        } if(!isset($_SESSION['panier']) || count($_SESSION['panier']['produitId']) == 0 ) {
            $contenu = "VOTRE PANIER EST VIDE";
            $TotalPanier = "0 €";

    }
        $tab = array($TotalPanier,$contenu);
        return $tab;
    }


}