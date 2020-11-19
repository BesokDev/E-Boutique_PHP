<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <a class="navbar-brand" href="accueil.php">La boutique NeverGrowUp !</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample04" aria-controls="navbarsExample04" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span></button>

    <div class="collapse navbar-collapse" id="navbarsExample04">

        <ul class="navbar-nav mr-auto">

        <?php if(isConnect()): // acces membre connecté mais NON ADMIN ?>

        <li class="nav-item active">
            <a class="nav-link" href="<?= URL ?>profil.php">Mon compte</a>
        </li>

        <li class="nav-item active">
            <a class="nav-link" href="<?= URL ?>boutique.php">Boutique</a>
        </li>

        <li class="nav-item active">  
            <a class="nav-link" href="<?= URL ?>panier.php">Mon panier <span class="badge badge-info rounded-circle"><?php if(!empty(nbreProduitPanier())) echo nbreProduitPanier(); ?></span></a>
        </li>

        <li class="nav-item active">  
            <a class="nav-link" href="<?= URL ?>connexion.php?action=deconnexion">Déconnection</a>
        </li>

        <?php else: // acces visiteur non connecté ?>

        <li class="nav-item active">
            <a class="nav-link" href="inscription.php">Créer votre compte</a>
        </li>
        <li class="nav-item active">  
            <a class="nav-link" href="connexion.php">Connectez-vous</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" href="<?= URL ?>boutique.php">Boutique</a>
        </li>
        <li class="nav-item active">  
            <a class="nav-link" href="<?= URL ?>panier.php">Mon panier</a>
        </li>

        <?php endif; ?>

        <?php if(isAdminConnect()): // acces ADMIN connecté (si statut membre est 1)?>

        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown" aria-haspopup="true">BACK OFFICE</a>

            <div class="dropdown-menu" aria-labelledby="dropdown04">
            <a class="dropdown-item" href="<?= URL ?>admin/backOffice_boutique.php">Gestion boutique</a>
            <a class="dropdown-item" href="<?= URL ?>admin/backOffice_commande.php">Gestion commande</a>
            <a class="dropdown-item" href="<?= URL ?>admin/backOffice_membre.php">Gestion membre</a>
            </div>
        </li>

        <?php endif; ?>

    </ul>

    <form class="form-inline my-2 my-md-0">
      <input class="form-control" type="text" placeholder="Search">
    </form>
  </div>
</nav>

<!-- //////////////////////////////// MAIN //////////////////////////////// -->

<main class="container-fluid" style="min-height: 90vh;">