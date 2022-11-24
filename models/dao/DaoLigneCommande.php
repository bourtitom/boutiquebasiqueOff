<?php
require_once('../utilitaires/FonctionsUtiles.php');
require_once('../models/LigneCommande.php');
require_once('DaoCommande.php');
require_once('DaoProduit.php');

class DaoLigneCommande
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


    //CETTE FONCTION 1
    function totalCommande($idCommande): float
    {
        $query = "SELECT SUM(PRIX*QUANTITE) as total
                                    FROM LigneCommande 
                                    WHERE COMMANDE_ID = " . $idCommande;

        //ON APPELLE LA FONCTION QUI VA FAIRE LA CONNECTION ET RENVOYER UN RÉSULTAT
        $result = executeQuery($query);

        foreach ($result as $row) {
            $total = $row['total'];
        }
        return $total;
    }


    function resultToObjects($result)
    {   //ON RECUPERE LE RÉSULTAT DE LA REQUETE DANS UN TABLEAU
        //QUI CONTIENDRA 1 OU PLUSIEURS OBJETS DE TYPE PRODUIT
        $listLDCommande = array();
        $outilCommande = new DaoCommande();
        //ON INSTANCIE UN OBJET DE TYPE DaoProduit
        $outilProduit = new DaoProduit();



        foreach ($result as $row) {
            $commande = $outilCommande->getCommandeById($row['COMMANDE_ID'])[0];
            //LA METHODE "readProduit" nous renvoie un objet de type Produit
            $produit = $outilProduit->getProduitById($row['PRODUIT_ID'])[0];

            $LDcommande = new LigneCommande($commande, $produit, $row['QUANTITE'], $row['PRIX']);
            array_push($listLDCommande, $LDcommande);
        }
        return $listLDCommande;
    }
    function getLDCommandeById($id): array
    {
        $query = $this->maConnection->prepare("SELECT COMMANDE_ID,PRODUIT_ID, QUANTITE, PRIX
                                    FROM LigneCommande 
                                    WHERE COMMANDE_ID=?");
        $query->execute(array(
            $id));
        $result = $query->fetchAll();
        //ON TRANSFORME LE RESULTAT EN TABLEAU CONTENANT UN OU PLUSIEURS OBJETS DE TYPE PRODUIT
        return $this->resultToObjects($result);
    }
    function getAll(): array
    {
        $query = $this->maConnection->prepare("SELECT COMMANDE_ID,PRODUIT_ID, QUANTITE, PRIX
                                    FROM LigneCommande");
        $query->execute();
        $result = $query->fetchAll();
        //ON TRANSFORME LE RESULTAT EN TABLEAU CONTENANT UN OU PLUSIEURS OBJETS DE TYPE PRODUIT
        return $this->resultToObjects($result);
    }



    function afficherLigneCommandes($where = null): string
    {
        if (!empty($_POST['nomLigneCommande'])) {
            /* récupérer les données du formulaire en utilisant
               la valeur des attributs name comme clé
              */
            $where = $_POST['nomLigneCommande'];
        }
        //ON APPELLE LA FONCTION QUI VA FAIRE LA REQUETE AUPRES DE LA BASE DE DONNEES
        //CETTE FONCTION RENVOIE UN TABLEAU CONTENANT UN OU PLUSIEURS OBJETS DE TYPE LIGNEDECOMMANDE
        $lesLigneCommandes = $this->getLDCommandeById($where);
        //ON AFFICHE LE HTML POUR LE FICHIER "AfficherLigneCommandes"
        $nbProduit = 1;
        $contenu =
            "
        <h3>Contenu de la commande</h3></section><br><div>";

        foreach ($lesLigneCommandes as $LigneCommande) {
            $contenu .= "

             <h4> " . $nbProduit . ")    " . $LigneCommande->getProduit()->getProduitNom() . "  **  Prix pièce: " . $LigneCommande->getPrix() . " EUROS  ** Qté : " . $LigneCommande->getQuantite() . " <br>   ** Total : " . $LigneCommande->getTotal() . " EUROS</h4> 
             <br>";
            $nbProduit++;
        }
        $contenu .= "</div>";
        return $contenu;
    }

    function createLigneCommande($commandeid)
    {
        /* On parcourt le panier en session pour insérer chaqune de ses lignes dans la base de données */
        for ($i = 0; $i < count($_SESSION['panier']['produitId']); $i++) {

            //ON INSTANCIE UN OBJET DE TYPE DaoProduit
            $outilProduit = new DaoProduit();

            $produit = $outilProduit->getProduitById($_SESSION['panier']['produitId'][$i])[0];

            $query = $this->maConnection->prepare("INSERT INTO `LigneCommande`(`COMMANDE_ID`,`PRODUIT_ID`,`QUANTITE`,`PRIX`) values(?,?,?,?)");
            //ON APPELLE LA FONCTION QUI VA  EXECUTER LA REQUETE D INSERTION
            $result = $query->execute(array(
                $commandeid,
                $_SESSION['panier']['produitId'][$i],
                $_SESSION['panier']['qte'][$i],
                $produit->getProduitPrix()
            ));
        }

        unset($_SESSION['panier']);

    }




}
