<?php

function chargerClasse($classeName) {
    require $classeName . '.php';
}

spl_autoload_register('chargerClasse');

session_start();

if (isset($_GET['deconnexion'])) {
    session_destroy();
    header('Location: .');
    exit();
    
}

$db = new PDO("mysql:host=localhost;dbname=test", 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

$manager = new PersonageManager($db);

if(isset($_SESSION['perso'])) // Si la session perso existe, on restaure l'objet
{
    $perso = $_SESSION['perso'];
}

if (isset($_POST['creer']) && isset($_POST['nom'])) {
    $perso = new Personnage(['name' => $_POST['nom']]);
    if(!$perso->nomValide())
    {
        $message = 'Nom de personnage invalide';
        unset($perso);
    }
    elseif ($manager->exists($perso->name())) {
        $message = 'Le nom de personnage est déjà utilisé';
        unset($perso);
}
else 
{
    $manager->add($perso);
}
}

elseif (isset($_POST['utiliser']) && isset($_POST['nom'])) {
    if ($manager->exists($_POST['nom'])) { // SI le perso existe
        $perso = $manager->get($_POST['nom']); // On le selectionne dans la BDD
    }
    else {
       $message = 'Ce personnage n\'existe pas';
    }
}
elseif (isset($_GET['frapper'])) { // Si on a cliqué sur perso pour le frapper
    if (!isset($perso)) {
        echo 'Merci de créer un personnage ou de vous identifier';
    }
    else
    {
        if (!$manager->exists((int) $_GET['frapper'])) {
            echo 'Le personnage à frapper n\'existe pas';
        }
        else {
            $persoAFrapper = $manager->get((int) $_GET['frapper']);
            
            $retour = $perso->frapper($persoAFrapper); // On stocke dans retour les éventuelles erreurs
            
            switch($retour) {
                case Personnage::CEST_MOI:
                    $message =  'Pourquoi voulez-vous vous frapper vous-même?';
                    break;
                case Personnage::PERSONNAGE_FRAPPE:
                    $message = 'Le Personnage a bien été frappé';
                    
                    $manager->update($perso);
                    $manager->update($persoAFrapper);
                    
                    break;
                
                case Personnage::PERSONNAGE_TUE:
                    $message = 'Vous avez tué ce personnage';
                    
                    $manager->update($perso);
                    $manager->delete($persoAFrapper);
                    
                    break;
            }
    }

}
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>TP : Mini jeu de combat</title>
    
    <meta charset="utf-8" />
  </head>
  <body>
    <p>Nombre de personnages créés : <?= $manager->count() ?></p>
    <?php
    if (isset($message)) // On a un message à afficher ?
    {
      echo '<p>', $message, '</p>'; // Si oui, on l'affiche.
    }

    if (isset($perso)) // Si on utilise un personnage (nouveau ou pas).
    {
    ?>
    <p><a href="?deconnexion=1">Déconnexion</a></p>
    
    <fieldset>
      <legend>Mes informations</legend>
      <p>
        Nom : <?= htmlspecialchars($perso->name()) ?><br />
        Dégâts : <?= $perso->damages() ?>
      </p>
    </fieldset>
    
    <fieldset>
      <legend>Qui frapper ?</legend>
      <p>
    <?php
    $persos = $manager->getList($perso->name());

    if (empty($persos))
    {
      echo 'Personne à frapper !';
    }

    else
    {
      foreach ($persos as $unPerso)
        echo '<a href="?frapper=', $unPerso->id(), '">', 
              htmlspecialchars($unPerso->name()), '</a> (dégâts : ', 
              $unPerso->damages(), ')<br />';
    }
    ?>
          </p>
        </fieldset>
    <?php
    }
    else
    {
    ?>
        <form action="" method="post">
          <p>
            Nom : <input type="text" name="nom" maxlength="50" />
            <input type="submit" value="Créer ce personnage" name="creer" />
            <input type="submit" value="Utiliser ce personnage" name="utiliser" />
          </p>
        </form>
    <?php
    }
    ?>
      </body>
    </html>
    <?php
    if (isset($perso)) // Si on a créé un personnage, on le stocke dans une variable session afin d'économiser une requête SQL.
    {
      $_SESSION['perso'] = $perso;
    }
    ?>