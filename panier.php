<?php 
require_once("include/init_inc.php");

if(isset($_POST['ajout_panier']))
{
    // echo "<pre>"; print_r($_POST); echo "</pre>";

    extract($_POST);

    $request= $bdd->prepare("SELECT * FROM produit WHERE id_produit = :id");
    $request->bindValue(':id', $id_produit, PDO::PARAM_INT);
    $request->execute();

    $produit = $request->fetch(PDO::FETCH_ASSOC);
    // echo "<pre>"; print_r($produit); echo "</pre>";

    extract($produit);

    ///////////////////// $quantite est dans $_POST pas dans $produit //////////////////////
    add_ToCart($id_produit, $photo, $reference, $titre, $_POST['quantite'], $prix );

}

////////// SUPPRESSION ARTICLE DANS LE PANIER
if(isset($_GET['action']) && $_GET['action'] == 'suppArticle')
{
    $idArticle = array_search($_GET['id_produit'], $_SESSION['panier']['id_produit']);

    $validSupp = "<div class='bg-light col-md-6 mx-auto mb-3 text-center rounded'>L'article <span class='text-danger'>" . strtoupper($_SESSION["panier"]["titre"][$idArticle]) . "</span> à bien été retiré de votre panier</div>";

    suppArticle($_GET['id_produit']);
}

// CONTROLE STOCK ARTICLE
if(isset($_POST["pay"]))
{
    $errorStock ="";
    for($i = 0; $i < count($_SESSION["panier"]["id_produit"]); $i++)
    {
        $request = $bdd->query("SELECT stock FROM produit WHERE id_produit = ". $_SESSION["panier"]["id_produit"][$i]);
        $stock = $request->fetch(PDO::FETCH_ASSOC);
        echo "<pre>"; echo "id_produit : " . $_SESSION["panier"]["id_produit"][$i]; echo "</pre>";
        // echo "<pre>"; print_r($stock); echo "</pre>";
        
        if($stock["stock"] < $_SESSION["panier"]["quantite"][$i])
        {
            $errorStock .= "<div class='bg-dark col-md-4 mx-auto text-center text-white rounded'>Stock restant du produit : $stock[stock]</div>"; 

            $errorStock .= '<div class="bg-info col-md-4 mx-auto my-2 text-center text-white rounded">Quantité demandée de l\'article : ' . $_SESSION["panier"]["quantite"][$i] . '</div>'; 

            if($stock['stock'] > 0 )
            {
                $errorStock .= '<div class="bg-light col-md-6 mx-auto mb-2 text-center text-danger border border-danger rounded">La quantité de l\'article : ' . '<span class="text-info">' . strtoupper($_SESSION["panier"]["titre"][$i]) . '</span>' . ' à été modifié car le stock est insuffisant.</div>'; 

                $_SESSION['panier']['quantite'][$i] = $stock['stock'];
            }
            else
            {
                $errorStock .= '<div class="bg-danger col-md-6 mx-auto mb-2 text-center text-black rounded">L\'article : ' . '<span class="text-info">' . strtoupper($_SESSION["panier"]["titre"][$i]) . '</span>' . ' à été supprimé car l\'article est en rupture de stock.</div>'; 

                suppArticle($_SESSION['panier']['id_produit'][$i]); // suppression dans $_SESSION de l'article qui a un stock à 0, en rupture de stock.
                $i--; // on décrémente $i dans la boucle pour ne pas manquer un article, car array_splice à fait remonter tous les indexs suivants et à changé leur numéro (si le suivant est l'index [5], il devient [4] et on le controle bien)
            }

            $errorPanier = true;
        }
    }

    if(!isset($errorPanier))
    {
        // INSERT DE LA COMMANDE DANS LA TABLE "commande"
        $request = $bdd->exec("INSERT INTO commande (membre_id, montant, date_enregistrement) VALUES (" . $_SESSION['membre']['id_membre'] . ", " . montantTotal() . ", NOW())");

        $idCommande = $bdd->lastInsertId(); // permet de recup le dernier id inséré dans la bdd afin de l'enregistrer dans la  table details_commande, pour chaque produit à la bonne commande

        for($i=0; $i < count($_SESSION['panier']['id_produit']); $i++)
        {
            $req = $bdd->exec("INSERT INTO details_commande (commande_id, produit_id, quantite, prix) VALUES ($idCommande, " . $_SESSION['panier']['id_produit'][$i] . ", " . $_SESSION['panier']['quantite'][$i] . ", " . $_SESSION['panier']['prix'][$i] . ")");

            // DECREMENT des stocks d'article
            // Modifie la colonne 'stock' de la table 'produit' afin que le stock s'actualise en fonction des quantités commandées et de l'id_produit commandé
            $r= $bdd->exec("UPDATE produit SET stock = stock - " . $_SESSION['panier']['quantite'][$i] . " WHERE id_produit= " . $_SESSION['panier']['id_produit'][$i]);
        }
        // On "vide" le panier du membre après le click du bouton 'valider mon paiement'
        unset($_SESSION['panier']);

        $_SESSION['num_cmd'] = $idCommande; // on stocke l'id_commande dans $_SESSION (à''num_cmd') après validation du panier
        header('location: validation_cmd.php'); // redirection du membre après la validation du paiement

    }
}

// echo "<pre>"; print_r($_SESSION); echo "</pre>";

require_once('include/header_inc.php');
require_once("include/nav_inc.php");
?>

<h1 class="display-4 text-center my-4">Mon Panier</h1>

<?php if(isset($errorStock)) echo $errorStock; ?>
<?php if(isset($validSupp)) echo $validSupp; ?>


<table class="col-md-9 mx-auto table table-bordered text-center">
    <tr>
        <th>PHOTO</th>
        <th>REFERENCE</th>
        <th>TITRE</th>
        <th>QUANTITE</th>
        <th>PRIX Unitaire</th>
        <th>PRIX Total/Produit</th>
        <th>SUPPRIMER</th>
    </tr>

<?php if(empty($_SESSION['panier']['id_produit'])): ?>

        <tr><td colspan="7" class="text-warning bg-dark">Aucun produit dans votre panier</td></tr>
    </table>

<?php else : ?>

    <?php for($i=0; $i < count($_SESSION['panier']['id_produit']) ; $i++): ?>

        <tr>
            <td><a href="fiche_produit.php?id_produit=<?= $_SESSION['panier']['id_produit'][$i] ?>"><img src="<?= $_SESSION['panier']['photo'][$i] ?>" alt="<?= $_SESSION['panier']['titre'][$i] ?>" style="width:100px;"></a></td>

            <td><?= $_SESSION['panier']['reference'][$i] ?></td>
            <td><?= $_SESSION['panier']['titre'][$i] ?></td>
            <td><?= $_SESSION['panier']['quantite'][$i] ?></td>
            <td><?= $_SESSION['panier']['prix'][$i] ?>€</td>
            <td><?= $_SESSION['panier']['quantite'][$i] * $_SESSION['panier']['prix'][$i] ?>€ </td>

            <td><a href="?action=suppArticle&id_produit=<?= $_SESSION['panier']['id_produit'][$i]?>" class="btn"><i class='fas fa-trash-alt text-danger'></i></a></td>


        </tr>
    

    <?php endfor; ?>

        <tr>
            <th class="bg-dark text-white">Montant TOTAL</th>
            <td colspan="4" class="bg-dark"></td>
            <th class="bg-dark text-white"><?= montantTotal(); ?>€</th>
            <td class="bg-dark"></td>
        </tr>


    </table>

    <?php if(isConnect()) : ?>

        <form action="" method="POST">
            <input type="submit" name="pay" value="Valider mon paiement" class="offset-md-9 btn btn-success my-4">
        </form>

    <?php else : ?>

        <a href="<?= URL ?>connexion.php" class="offset-md-9 btn-lg btn-info my-4">Identifiez-vous</a>

    <?php endif; ?>

<?php endif; ?>


<?php require_once("include/footer_inc.php"); ?>
