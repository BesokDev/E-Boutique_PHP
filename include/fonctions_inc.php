<?php 

//////////////////////////////////////////////////////////////////////////////////////
// ************************** FONCTION MEMBRE CONNECTÉ *****************************//
//////////////////////////////////////////////////////////////////////////////////////
function isConnect()
{
    if(!isset($_SESSION['membre']))
    {
        return false;
    }
    else 
    {
        return true;
    }
}

/////////////////////////////////////////////////////////////////////////////////////
// ****************************** FONCTION ADMIN **********************************//
/////////////////////////////////////////////////////////////////////////////////////
function isAdminConnect() 
{

    if(isConnect() && $_SESSION['membre']['statut'] == 1)
    {
        return true;
    }
    else 
    {
        return false;
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// *************************** FONCTION CREATION PANIER DANS LA SESSION **************************** //
///////////////////////////////////////////////////////////////////////////////////////////////////////
// Les données du panier ne sont jamais conservées en BDD, bcp de panier n'aboutissent pas à un paiement.
// Donc nous allons stocker les infos du panier directement dans le fichier $_SESSION du user.
// Dans la session, nous définissons différents ARRAY qui permettront de stocker par exemple toute les références des produits ajoutés au panier dans un ARRAY
function createCart()
{
    if(!isset($_SESSION['panier']))
    {
        $_SESSION['panier'] = array();  // création d'un ARRAY dans la session à l'indice ['panier']
        $_SESSION['panier']['id_produit'] = array();
        $_SESSION['panier']['photo'] = array();
        $_SESSION['panier']['reference'] = array();
        $_SESSION['panier']['titre'] = array();
        $_SESSION['panier']['quantite'] = array();
        $_SESSION['panier']['prix'] = array();
    }
}

/* 
array 
(
    [user] => ARRAY(info du user connecté)

    [panier] =>

                [id_produit] => array (

                                0 => 15
                                1 => 29
                            )

                [reference] => array (

                                0 => 12A54
                                1 => 79P47
                            )

                [photo] => array (

                                0 => http://localhost:8080/PHP/09-Boutique/....
                                1 => http://localhost:8080/PHP/09-Boutique/....
                                2 => etc...
                            )
)
*/

///////////////////////////////////////////////////////////////////////////////////////////////////////
// *************************** FONCTION AJOUTER PANIER DANS LA SESSION ***************************** //
///////////////////////////////////////////////////////////////////////////////////////////////////////
// Les paramètres définit dans la fonction permettront de receptionner les informations du produit ajouté dasn le panier afin de stocker chaque donnée dans les différents tableau ARRAY
function add_ToCart($id_produit, $photo, $reference, $titre, $quantite, $prix)
{
    createCart(); // On créé un panier si il est INEXISTANT

    // array_search () permet de trouver à quel index se trouve un élément dans un ARRAY
    $indexOf_IdProduit = array_search($id_produit, $_SESSION['panier']['id_produit']);

    // ***************** array_search() RETURN un number INTEGER, sinon un BOOL ********************* //

    // SI la variable $indexOf_IdProduit est différente de false, cela veut dire que array_search() a bien trouvé l'indice du produit dans la session
    if($indexOf_IdProduit !== false) // si le return de array_search() est different de FALSE (donc un INTEGER)
    {
        $_SESSION['panier']['quantite'][$indexOf_IdProduit] += $quantite;
        // On modifie la quantité du produit à l'indice correspondant, retourné par array_search()
        // Chaque indice numérique dans les tableaux 'photo,reference, prix' etc... correspondent au même produit ajouté dans le panier 
    }
    else
    {
        // Les crochets vides permettent de générer des indexs numériques dans les ARRAY
        $_SESSION['panier']['id_produit'][] = $id_produit;  // e.g : $_SESSION['panier']['id_produit'][0] = 12
        $_SESSION['panier']['photo'][] = $photo;
        $_SESSION['panier']['reference'][] = $reference;    // e.g :  $_SESSION['panier']['reference'][0] = AA0001
        $_SESSION['panier']['titre'][] = $titre;
        $_SESSION['panier']['quantite'][] = $quantite;
        $_SESSION['panier']['prix'][] = $prix;              // e.g :  $_SESSION['panier']['prix'][0] = 15
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// ******************************** FONCTION MONTANT TOTAL PANIER ********************************** //
///////////////////////////////////////////////////////////////////////////////////////////////////////
function montantTotal()
{
    $total = 0;
    for( $i = 0; $i < count($_SESSION['panier']['id_produit']); $i++)
    {
        $total += $_SESSION['panier']['quantite'][$i] * $_SESSION['panier']['prix'][$i];
    }
    return round($total, 2); // arrondi $total à 2 chiffres après la virgule
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// *************************** FONCTION Nombre Produit BADGE PANIER NAV **************************** //
///////////////////////////////////////////////////////////////////////////////////////////////////////
function nbreProduitPanier()
{
    if(isset($_SESSION['panier']) && $_SESSION['panier']['quantite'] > 0)
    {
        $nombre = 0;

        for( $i= 0; $i < count($_SESSION['panier']['id_produit']); $i++)
        {
            $nombre += $_SESSION['panier']['quantite'][$i];
        } 
    }
    else
    {
        $nombre = 0;
    }
    return $nombre;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
// ************************************ FONCTION DELETE ARTICLE PANIER ************************************* //
///////////////////////////////////////////////////////////////////////////////////////////////////////
function suppArticle($id_article)
{
     // On transmet à la fonction prédéfinie array_search(), l'id_produit du produit en rupture de stock
    // array_search() retourne l'indice du tableau ARRAY auquel se trouve l'id_produit a supprimer
    $indexArticle = array_search($id_article, $_SESSION['panier']['id_produit']);

    // Si la valeur de $positionProduit est différente de FALSE, cela veut dire que l'id_produit a supprimer a bien été trouvé dans le panier de la session
    if($indexArticle !== false)
    {
        // array_splice() permet de supprimer des éléments d'un tableau ARRAY
        // on supprime chaque ligne dans les tableaux ARRAY du produit en rupture de stock
        // array_splice() ré-organise les taleaux ARRAY, c'est à dire que tous les éléments aux indices inférieurs remonttent aux indices supérieurs, le produit stocké à l'indice 3 du tableau ARRAY remonte à l'indice 2 du tableau ARRAY
        array_splice($_SESSION['panier']['id_produit'], $indexArticle, 1);
        array_splice($_SESSION['panier']['photo'], $indexArticle, 1);
        array_splice($_SESSION['panier']['reference'], $indexArticle, 1);
        array_splice($_SESSION['panier']['titre'], $indexArticle, 1);
        array_splice($_SESSION['panier']['quantite'], $indexArticle, 1);
        array_splice($_SESSION['panier']['prix'], $indexArticle, 1);
    }

    /*
    array
    (
        [user] => ARRAY(infos de l'utilisateur connecté)

        [panier] => array(
                
                [id_produit] =>array(
                            0 => 15
                            1 => 21
                            2 => 40 
                        )

                [reference] => array(
                            0 => 12A45
                            1 => 87D36
                            2 => 46F56
                        )

                [photo] => array(
                            0 => http://localhost/PHP/09-boutique/photo/img1.jpg
                            1 => http://localhost/PHP/09-boutique/photo/img2.jpg
                            2 => http://localhost/PHP/09-boutique/photo/img3.jpg

                        )
        )
    )

    array_splice(param1, param2, param3) va supprimer 1 ligne de l'array en question, et va réorganiser les indexs de ce même array. Il remonte tous les indexs suivant l'index supprimé.

    =====================> ex : si on décide de supp l'index [1]

    array
    (
        [user] => ARRAY(infos de l'utilisateur connecté)

        [panier] => array(
                
                [id_produit] =>array(
                            0 => 15
                            1 => 40 => anciennement index [2]
                        )

                [reference] => array(
                            0 => 12A45
                            1 => 46F56 => anciennement index [2]
                        )

                [photo] => array(
                            0 => http://localhost/PHP/09-boutique/photo/img1.jpg
                            1 => http://localhost/PHP/09-boutique/photo/img3.jpg => anciennement index [2]

                        )
        )
    )

*/

}

?>