<?php
session_start();

require_once '../controllers/fonctions.php';

$identifiantHasher = hashIdentifiant();

$erreurs = [];

$messageEnvoi = '';


// pour gérer la form:

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $targetDir = "../../upload/identifierHasher/";
        $file = basename($_FILES["upload"]["name"]);
        $targetFile = $targetDir . $file;
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        //verifier si le fichier exsiste déja:

            if($file_exsists($targetFile)){
                $errurs[] = "ce fichier exsite déja!";
                $uploadOk = 0;
            }

            // vérfier si le chemin "path - direction" exsiste sinon créer le :
            // 0777 : direction permission: tout le monde sont le droit pour créer des permission

            if (!is_dir($targetDir)) {
                mkdir("$targetDir", 0777, true);
            };

            // validation de la taille et le type de fichier :

            if ($_FILES["upload"]["size"] > 20000000) {
                $erreurs[] = "Votre fichier doit être inférieur à 20Mo";
                $uploadOk = 0;
            };

            // par securité le utilisateur ne peut pas selectioner un fichier .php c'est parce que ça peut 
            // être dangereux pour le systeme

            if ($fileType == "php") {
                $erreurs[] = "Vous ne pouvez pas envoyer un fichier .php";
                $uploadOk = 0;
            };
            
            // si tout va bien: 

            if ($uploadOk == 0) {
                $erreurs[] = "Votre fichier n'a pas été envoyé";
            } else {
                if (move_uploaded_file($_FILES["upload"]["tmp_name"], $targetFile)) {

                    // quand le fichier est télechargé créer un lien,
                    // generer un message et l'afficher pour le utilisateur :

                    $messageEnvoi = "Votre fichier ". htmlspecialchars(basename($_FILES["upload"]["name"])) . " a été envoyé";
        
                    // Génerer un ID  unique pour le fichier:

                    $fileUid = uniqid('file_');
        
                    // créer un permission pour un utilisateur unique:

                    $permissionFile = "../../permissions/$fileUid.txt";  // créer un fichier .txt ou les permissions ont sauvgarder!

                    // si le type d'access est privé est le utilisateur a choisi pour qui le fichier est autoriseé:
                    $accessType = ($_POST['access_type'] == 'private' && !empty($_POST['recipient'])) ? 'private' : 'public';
                    $recipient = ($_POST['access_type'] == 'private' && !empty($_POST['recipient'])) ? $_POST['recipient'] : '';
        
                    // Sauvegarder  les permissions:
                    $permissionData = "$accessType\nRecipient: $recipient";
                    file_put_contents($permissionFile, $permissionData);
        
                    // afficher le lien de téléchargement:
                    $downloadLink = "download.php?uid=$fileUid";
                    $messageEnvoi .= "<br>Le lien pour télécharger votre fichier: <a href='$downloadLink'>$downloadLink</a>";
                } else {
                    $erreurs[] = "Il y a eu une erreur lors de l'envoi";
                }
            }
        }


        
 ?>

 <!-- form pour télécharger les fichier et créer les liens de télechargment -->

  <form action="link_generation.php" method="post">
    <label for="upload">choisir un fichier :</label>
    <input type="file" name="upload" id="upload" required><br>
      
    <label for="access_type">Type d'accés pour votr fichier: </label>
    <select name="access_type" id="access_type">
        <option value="public">Public</option>
        <option value="private">Privé</option>
    </select><br>
    <label for="recipient">Destinataire (si privé) :</label>
    <input type="text" name="recipient" id="recipient" placeholder="Email ou Nom d'utilisateur"><br>

    <button type="submit">Envoyer</button>
  </form>


  <?php

  //afficher les erreurs sinon le message de success 
  if (!empty($erreurs)) {
    echo "<ul>";
    foreach ($erreurs as $erreur) {
        echo "<li>$erreur</li>";
    };
    echo "</ul>";
 };

 if ($messageEnvoi != '') {
    echo "<p>$messageEnvoi</p>";
 };
   ?>