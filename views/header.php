<?php @session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"> <!--Meta-->
    <meta name="description" content="">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../assets/css/header.css">
    <title></title>
    <link rel="stylesheet" href="../assets/css/normalize.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header>
    <a href="../index.php"><img src="../assets/img/house-solid.svg" alt="house" id="house"></a>
    <a href="#" id="linkTop"><img src="../assets/img/up.png" alt="#" id="arrowTop"></a>
    <?php

    if (!isset($_SESSION["client"])) {

        echo '<a href="../views/Login.php"><img src="../assets/img/profil.png" alt="#" id="profil"></a>';
    }else if (isset($_SESSION['panier'])){

        $qte= 0;
        for ($i = 0; $i < count($_SESSION['panier']['produitId']); $i++) {
            $qte += $_SESSION['panier']['qte'][$i];
        }

        echo '<a href="../controllers/Controller.php?todo=montrerPanier"><img src="../assets/img/panier.svg" alt="voir mon panier" id="profil"> <div id="quantityPanier">'. $qte . '</div></a>';

    }else{
        echo '<a href="../controllers/Controller.php?todo=montrerPanier"><img src="../assets/img/panier.svg" alt="voir mon panier" id="profil"> </a>';

    }

    ?>
    <h1>BoutiqueBasique en MVC1 !</h1>
    <div class='login'>
        <?php
        if (isset($_SESSION["client"])) {
            echo "Bienvenue " . $_SESSION["client"]["prenom"];
            echo " <a href='../controllers/Controller.php?todo=deconnexion'>déconnexion</a>";
        }
        ?>
    </div>
    <nav>
        <ul>

            <?php
            if(isset($_SESSION["client"])) {
                if ($_SESSION["client"]['id'] == 0) {
                    echo "
            <li>
                <a href='../controllers/Controller.php?todo=creerClient'>Créer un nouveau client</a>
            </li>
            
            <li>
                <a href='../controllers/Controller.php?todo=creerProduit'>Créer un nouveau produit</a>
            </li>
                                        
            <li>
                <a href='../controllers/Controller.php?todo=afficherClients'>Voir les clients</a>
            </li>
            <li>
                <a href='../controllers/Controller.php?todo=afficherCommandes'>Voir les Commandes</a>
            </li>
                ";
                }
            }
            if (isset($_SESSION["client"]) && $_SESSION["client"]['id'] !== 0) {

                echo "
                                                
            <li>
                <a href='../controllers/Controller.php?todo=afficherClients&client=" . $_SESSION["client"]['id'] . "'>Voir mon profil</a>
            </li>
            <li>
                <a href='../controllers/Controller.php?todo=afficherCommandes&client=" . $_SESSION["client"]['id'] . "'>Voir mes Commandes</a>
            </li>
                ";
            }
            ?>


            <li>
                <a href='../controllers/Controller.php?todo=afficherProduits'>Voir les produits</a>
            </li>

        </ul>
    </nav>
</header>
