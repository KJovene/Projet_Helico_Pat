<?php 
// il faut modifier ce fichier pour vérifier les permissions (public ou privé) et s'assurer que seuls les utilisateurs autorisés peuvent télécharger le fichier
// regardes le ficher link_generation.php 
session_start();
require_once 'fonctions.php';
obligationConnexion();

$identifiantHasher = hashIdentifiant();

if (isset($_GET['file'])) {
    $file = basename($_GET['file']); // Sécurisation du nom de fichier
    var_dump($file);
    $filepath = '../../uploads/' . $identifiantHasher . '/' . $file;
    var_dump($filepath);

    if (file_exists($filepath)) {
        // En-têtes pour forcer le téléchargement
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));

        ob_clean();
        flush();
        readfile($filepath);
        exit;
    } else {
        echo "Le fichier n'existe pas.";
    }
} else {
    echo "Aucun fichier spécifié.";
}
?>