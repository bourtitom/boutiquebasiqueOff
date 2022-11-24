<?php
require_once('../config/config.php');
require_once('FonctionsBdd.php');
require_once('../utilitaires/FonctionsUtiles.php');
require_once('../models/Client.php');
//***********************************************CLIENT  CLIENT  CLIENT
//***********************************************CLIENT  CLIENT  CLIENT
//***********************************************CLIENT  CLIENT  CLIENT
class DaoClient
{
    private $maConnection;

    public function __construct()
    {
        //INSTANCIATION DE LA CONNEXION PAR APPEL AU CONSTRUCTEUR PDO ET VALORISATION DES ATTRIBUTS
        $this->maConnection = new PDO('mysql:host=' . HOST . ';dbname=' . DATABASE . ';charset=utf8;', USER, PASSWORD);
        //PARAMETRAGE POUR AFFICHAGE DES ERREURS RELATIVES A LA CONNEXION
        $this->maConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }

//CETTE FONCTION PERMET DE CREER UN NOUVEAU CLIENT
    function createClient(): string
    {
        $client = new Client(0, $_POST['clientPrenom'], $_POST['clientNom'], $_POST['clientNaissance'], $_POST['clientMail'], password_hash($_POST['clientPassword'], PASSWORD_BCRYPT));

        $query = $this->maConnection->prepare("INSERT INTO `client`(`CLIENT_PRENOM`,`CLIENT_NOM`,`CLIENT_NAISSANCE`,`CLIENT_MAIL` , `CLIENT_PASSWORD`) values(?, ?, ?, ?, ?)");
        //ON APPELLE LA FONCTION QUI VA  EXECUTER LA REQUETE
        $result = $query->execute(array(
            $client->getClientPrenom(),
            $client->getClientNom(),
            $client->getClientNaissance(),
            $client->getClientMail(),
            $client->getClientPassword(),

        ));
        return $this->maConnection->lastInsertId();
    }

//CETTE FONCTION PERMET DE METTRE A JOUR UN CLIENT
    function updateClient(): string
    {


        $client = new Client($_POST['clientId'], $_POST['clientPrenom'], $_POST['clientNom'], $_POST['clientNaissance'], $_POST['clientMail'], password_hash($_POST['clientPassword'], PASSWORD_BCRYPT));

        $query = $this->maConnection->prepare("UPDATE client SET CLIENT_PRENOM = ?, CLIENT_NOM = ? , CLIENT_NAISSANCE = ? , CLIENT_MAIL= ? , CLIENT_PASSWORD = ?  WHERE CLIENT_ID = ? ");
        //ON APPELLE LA FONCTION QUI VA  EXECUTER LA REQUETE
        $result = $query->execute(array(
            $client->getClientPrenom(),
            $client->getClientNom(),
            $client->getClientNaissance(),
            $client->getClientMail(),
            $client->getClientPassword(),
            $client->getClientId()

        ));
        //ON RENVOIE L ID DU CLIENT AU CONTROLEUR POUR QU il LE TRANSMETTE A LA VUE AFFICHERCLIENTS
        return $where = "CLIENT_ID=" . $client->getClientId();
    }


//CETTE FONCTION PERMET DE SUPPRIMER UN CLIENT
    function deleteClient(): void
    {

        $query = $this->maConnection->prepare("DELETE FROM client WHERE CLIENT_ID = ?");
        $result = $query->execute(array(
            $_POST['clientId']
        ));
    }
    function resultToObjects($result)
    {   //ON RECUPERE LE RÉSULTAT DE LA REQUETE DANS UN TABLEAU
        //QUI CONTIENDRA 1 OU PLUSIEURS OBJETS DE TYPE PRODUIT
        $listClients = array();
        foreach ($result as $row) {
            $client = new Client($row['CLIENT_ID'], $row['CLIENT_PRENOM'], $row['CLIENT_NOM'], $row['CLIENT_NAISSANCE'],$row['CLIENT_MAIL'],$row['CLIENT_PASSWORD']);
            array_push($listClients, $client);
        }
        return $listClients;
    }
    function getClientById($id): array
    {
        $query = $this->maConnection->prepare("SELECT CLIENT_ID,CLIENT_PRENOM, CLIENT_NOM, CLIENT_NAISSANCE , CLIENT_MAIL , CLIENT_PASSWORD
                                    FROM client 
                                    WHERE CLIENT_ID=?");

        $query->execute(array(
            $id));
        $result = $query->fetchAll();
        //ON TRANSFORME LE RESULTAT EN TABLEAU CONTENANT UN OU PLUSIEURS OBJETS DE TYPE PRODUIT
        return $this->resultToObjects($result);
    }
    function getAll(): array
    {
        $query = $this->maConnection->prepare("SELECT CLIENT_ID,CLIENT_PRENOM, CLIENT_NOM, CLIENT_NAISSANCE , CLIENT_MAIL , CLIENT_PASSWORD
                                    FROM client");
        $query->execute();
        $result = $query->fetchAll();
        //ON TRANSFORME LE RESULTAT EN TABLEAU CONTENANT UN OU PLUSIEURS OBJETS DE TYPE PRODUIT
        return $this->resultToObjects($result);
    }
    //CETTE FONCTION RENVOIE UN TABLEAU CONTENANT UN OU PLUSIEURS OBJETS DE TYPE CLIENT

    //CETTE FONCTION VERIFIE LE LOGIN ET MET LE CLIENT EN SESSION
    function login(): string
    {
        //ON VA CHERCHER DANS LA BASE DE DONNES SI LE MAIL FOURNI (POST) EXISTE
        //SI IL EXISTE ON RECUPERE LE CLIENT SOUS FORME D'OBJET DANS UN TABLEAU
//        $where = "CLIENT_MAIL= '" . $_POST['clientMail'] . "'";
        $tclient = $this->getClientById($_POST['clientMail']);

        //SI LE TABLEAU CONTIENT UN ELEMENT
        //ON VERIFIE QUE LE MOT DE PASSE FOURNI (POST) CORRESPOND AU MOT DE PASSE CRYPTE DANS LA BASE DE DONNEES
        //SI TOUT EST BON ON PASSE LE BOOLEEN ok A TRUE
        if (count($tclient) == 1) {
            $ok = false;
            $client = $tclient[0];
            if (password_verify($_POST['clientPassword'], $client->getClientPassword())) {
                $ok = true;

            }
        }

        //SI ok EST FALSE, ON RETOURNE UN MESSAGE D'ERREUR
        if (!$ok) {
            return "LE LOGIN EST ERRONE";
        }
        //SINON ON MET LE CLIENT EN SESSION ET ON APPELLE LA FONCTION QUI PERMETTRA
        //DE L'AFFICHER DANS "Layout'
        else {
            $_SESSION["client"] = [
                "id" => $client->getClientId(),
                "prenom" => $client->getClientPrenom(),
                "nom" => $client->getClientNom()
            ];
            return $this->afficherClients(null, $tclient);
        }

    }


    function afficherClients($IdClient = null): string
    {
            $lesClients = array();
         if ($IdClient !== null) {

            /* récupérer les données du formulaire en utilisant
           la valeur des attributs name comme clé */
             $lesClients = $this->getClientById($IdClient);
        }
        if (isset($_SESSION['client']) ) {

                $lesClients = $this->getAll();

        }
        else if (!empty($_POST['nomClient'])) {
            /* récupérer les données du formulaire en utilisant
               la valeur des attributs name comme clé
              */

            $lesClients = $this->getClientById($_POST['nomClient']);
        }


        //ON AFFICHE LE HTML POUR LE FICHIER "AfficherClients"
        if($_SESSION["client"]['id'] !== 0){
            $contenu =
                "<section id='slogan'>
    <h2>Mon Profil</h2></div ></section><div id='menu'>";

        }if($_SESSION["client"]['id'] == 0){
        $contenu =
            "<section id='slogan'>
    <h2>Catalogue Clients</h2></div ></section><div id='menu'>";

        }

        foreach ($lesClients as $client) {
            $id = $client->getClientId();
            $naissance = strftime('%d/%m/%Y', strtotime($client->getClientNaissance()));
            $signe = $client->getClientSigne();
            $contenu .= "<article class='article' >
        <div class='container' ><img class='image' src = '../assets/img/" . $signe . " ' alt=''></div >
         <h2 > " . $client->getClientPrenom() . ' ' . $client->getClientNom() . "</h2 >
         <p style='text-align: center;'> date de naissance " . $naissance . " 
         <br>" . $client->getClientAge() . " ANS</p><br>
        <button id='submit'>
            <a href = '../controllers/Controller.php?todo=modifierClient&id=$id'>MODIFIER OU SUPPRIMER LE CLIENT</a>
        </button><br> ";


            if (isset($_SESSION["client"])) {
                $contenu .= "<a href = '../controllers/Controller.php?todo=commencerCommande'>PASSER UNE COMMANDE</a>";
            }
            $contenu .= "</article > ";

        }
        return $contenu;
    }


//CETTE FONCTION PERMET D'AFFICHER UN FORMULAIRE DE RECHERCHE DE CLIENTS
    function rechercheClient(): string
    {    //ON APPELLE LA FONCTION QUI VA FAIRE LA REQUETE AUPRES DE LA BASE DE DONNEES
        //CETTE FONCTION RENVOIE TOUS LES Client SOUS FORME DE TABLEAU D'OBJETS
        $lesClients = $this->getAll();

        //ON AFFICHE LE HTML POUR LE FICHIER "ModifierClients"
        $recherche = "
<form name='searchProduct' action='../controllers/Controller.php' method='post' class='search-form'>
            <input type='hidden' name='todo' value='afficherClients'>
    <label for='nomClient' hidden></label>
    <select name='nomClient' id='nomClient' class='header-select' onchange='this.form.submit()'>
        <option value=''>Choisir un client</option>";
        foreach ($lesClients as $client) {
            $recherche .= "<option value=" . $client->getClientId() . ">" . $client->getClientPrenom() . ' ' . $client->getClientNom() . "</option>";
        }

        $recherche .= "</select>
</form>";

        return $recherche;
    }

//CETTE FONCTION PREND EN GET DANS L URL UN ID Client
//ET RENVOIE Client
    function afficherFormModif(): Client
    {

        //ON APPELLE LA FONCTION QUI VA FAIRE LA REQUETE AUPRES DE LA BASE DE DONNEES
        //CETTE FONCTION RENVOIE UN TABLEAU CONTENANT LE Client A MODIFIER
        //ON RETOURNE CET OBJET Client AU CONTROLEUR QUI A APPELLE LA FONCTION
        //LE CONTROLEUR RETOURNERA L'OBJET A LA VUE "ModifierClient";
        return $this->getClientById($_GET['id'])[0];

    }
}