<?php include("header.php"); ?>
<main>
    <section style="display: flex; align-items: center; flex-direction: column;">
        <h2>CONTENU DE VOTRE PANIER</h2>
        <article style="border:black solid; width: 40%;height: 50vh;border-radius: 25px;">
            <?php
            if (isset($contenu)) {
                echo "<div class='panier scroller'>$contenu</div>";

            }
            ?>
            <form action='../controllers/Controller.php' method="post" id="formCreate">
                <input type='hidden' name='todo' value='validerPanier'>

                <div>
                        <input type="submit" name="submitValiderLaC" value="Valider La Commande" id="vlc">
                    </div>
            </form>

            <span style="  float: right;margin-right: 20%;margin-top: -8%;font-size: 2vw;">
                <?php
                if (isset($montant)) {
                    echo $montant;

                }
                ?>
            </span>
        </article>
    </section>
  </main>
<footer>
    <small>&copy; 2022 - boutiquebasique</small>
</footer>
</body>
</html>