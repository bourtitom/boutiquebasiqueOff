<?php
require_once('../config/config.php');
require_once('FonctionsBdd.php');
require_once('../utilitaires/FonctionsUtiles.php');
require_once('../models/Commande.php');
require_once('DaoClient.php');
require_once('DaoLigneCommande.php');

class DaoCommande
{

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
        $listCommande = array();
        $outilClient = new DaoClient();

        foreach ($result as $row) {
            $client = $outilClient->getClientById($row['CLIENT_ID'])[0];

            $commande = new Commande($row['COMMANDE_ID'], $client, $row['COMMANDE_DATE']);
            array_push($listCommande, $commande);
        }
        return $listCommande;
    }
    function getCommandeById($id): array
    {
        $query = $this->maConnection->prepare("SELECT COMMANDE_ID,CLIENT_ID, COMMANDE_DATE
                                    FROM commande 
                                    WHERE COMMANDE_ID=?");
        $query->execute(array(
            $id));
        $result = $query->fetchAll();
        //ON TRANSFORME LE RESULTAT EN TABLEAU CONTENANT UN OU PLUSIEURS OBJETS DE TYPE PRODUIT
        return $this->resultToObjects($result);
    }
    function getAll(): array
    {
        $query = $this->maConnection->prepare("SELECT COMMANDE_ID,CLIENT_ID, COMMANDE_DATE
                                    FROM commande");
        $query->execute();
        $result = $query->fetchAll();
        //ON TRANSFORME LE RESULTAT EN TABLEAU CONTENANT UN OU PLUSIEURS OBJETS DE TYPE PRODUIT
        return $this->resultToObjects($result);
    }


    function afficherCommandes($idcommande = null): string
    {
        //ON INSTANCIE UN OBJET DE TYPE LigneCommande afin de pouvoir afficher les produits commandés
        $outilLigneCommande = new DaoLigneCommande();
        $lesCommandes = array();;

        $lesCommandes = $this->getAll();
//
//        if (isset($_SESSION['client'])) {
//
//                $lesCommandes = $this->getAll();
//        }
        //On vérifie si il y a eu une recherche commande de postée via le MENU SELECT
        if (!empty($_POST['nomCommande'])) {
            /* récupérer les données du formulaire en utilisant
               la valeur des attributs name comme clé
              */
            $lesCommandes = $this->getCommandeById($_POST['nomCommande']);
        }


        //ON APPELLE LA FONCTION QUI VA FAIRE LA REQUETE AUPRES DE LA BASE DE DONNEES
        //CETTE FONCTION RENVOIE UN TABLEAU CONTENANT UN OU PLUSIEURS OBJETS DE TYPE COMMANDES
        //ON AFFICHE LE HTML POUR LE FICHIER "AfficherCommandes"

        $contenu =
            "<section id='slogan'>
        <h2>Liste des Commandes</h2></div></section>";
        foreach ($lesCommandes as $commande) {
            $contenu .= "<article class='article'>
                <h3> Commande n° " . $commande->getCommandeId() . "  du : " . dateEnClair($commande->getCommandeDate()) . " (Client  : " . $commande->getClient()->getClientPrenom() . " " . $commande->getClient()->getClientNom() . " )</h3>
          <br><h3> Total de la commande :   " . $commande->getTotal() . " EUROS </h3>
             <br>";
            //ON APPELLE LA FONCTION afficherLigneCommandes() DU DaoLigneCommande POUR AFFICHER LES PRODUITS COMMANDES
            $detailCommande = $outilLigneCommande->afficherLigneCommandes($commande->getCommandeId());
            $contenu .= $detailCommande;
            $contenu .= "</article>";
        }
        return $contenu;
    }


//CETTE FONCTION PERMET D'AFFICHER UN FORMULAIRE DE RECHERCHE DE COMMANDES
    function rechercheCommande(): string
    {   //ON APPELLE LA FONCTION QUI VA FAIRE LA REQUETE AUPRES DE LA BASE DE DONNEES
        //CETTE FONCTION RENVOIE TOUS LES COMMANDES SOUS FORME DE TABLEAU D'OBJETS
        $lesCommandes = $this->getAll();
        $recherche = "
<form name='searchProduct' action='../controllers/Controller.php' method='post' class='search-form'>
            <input type='hidden' name='todo' value='afficherCommandes'>
    <label for='nomCommande' hidden></label>
    <select name='nomCommande' id='nomCommande' class='header-select' onchange='this.form.submit()'>
        <option value=''>Choisir une commande </option>";
        foreach ($lesCommandes as $commande) {
            $recherche .= "<option value=" . $commande->getCommandeId() . ">" . $commande->getCommandeId() . ' ' . "</option>";
        }
        $recherche .= "</select>
</form>";
        return $recherche;
    }


    function createCommande(): string
    {
        //ON INSERE UNIQUEMENT L ID DU CLIENT,  LA DATE DOIT ETRE RENSEIGNEE AUTOMATIQUEMENT PAR LA BASE DE DONNEES (CURRENT TIMESTAMP)
        $query = $this->maConnection->prepare("INSERT INTO commande(CLIENT_ID) VALUES (?)");
        $result = $query->execute(array(
            $_SESSION["client"]["id"]
        ));

        //ON RECUPERE L'ID DE LA NOUVELLE COMMANDE
        //POUR LE TRANSMETTRE A LA METHODE DE daoLignecCommande :  createLigneCommande($nouvelid);
        $nouvelid = $this->maConnection->lastInsertId();

        //ON INSTANCIE UN OBJET OUTIL de type  DaoLigneCommande
        // afin de pouvoir rattacher la commande à ses lignes de commandes
        $outilLigneCommande = new DaoLigneCommande();
        $outilLigneCommande->createLigneCommande($nouvelid);
        //ON VIDE LE PANIER POUR QU'IL PUISSE AFFICHER LA NOUVELLE COMMANDE
        unset($_SESSION["panier"]);
        //ON RENVOIE L'ID DE LA COMMANDE AU CONTROLLER
        return $nouvelid;
    }


}