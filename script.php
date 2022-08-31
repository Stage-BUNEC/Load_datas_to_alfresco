<?php

# [ Date ]  :29-08-2022
# [ Description ] :Ce script sert à charger les fichiers PDF avec leurs Méta-données dans Alfresco
# [Authors ] : Mr GAEL MANI / NANFACK STEVE

// Verification des Arguments
if ($argc != 3) {
    echo "Mauvais usage du script\n";
    echo "Usage:	php script.php dossier fichier_de_meta_donnees\n";
    die();
}

// Inclusion des fonctions Utiles
include_once 'connexion_fonctions.php';

// Connexion a Alfresco
$ticket = loginToAlfrsco($url_alfresco, $port_alfresco, $user_alfresco, $pass_alfresco);
//echo $ticket;

// Recuperation du Noeud 'Partage/Shared'
$node_partage = getShareNode($url_alfresco, $port_alfresco, $ticket);
//echo "Noeud = " . $node_partage;

// Creation du Registre dans ALFRESCO
$dossier = trim($argv[1]);
createRegister($url_alfresco, $port_alfresco, $ticket, $node_partage, $dossier);

// Lecture du fichier de meta-donnees recu ligne par ligne

$fp = fopen($argv[2], "r");
if ($fp) {
    while (($ligne = fgets($fp)) !== false) {

        // Extraction du No de Registre et du Nom du fichier parmi les Meta-donnees
        $registre = trim(preg_split("/[#]/", $ligne)[6]);
        $fileName = trim($registre . "_original.pdf");

        // Recuperation de TOUTES les meta-donnees
        $postFields = get_all_metaDatas($dossier, $ligne, $registre, $fileName);
        //print_r($postFields);

        // Envoie des donnees à Alfresco
        send_datas($url_alfresco, $port_alfresco, $ticket, $postFields);
    }
    if (!feof($fp)) {
        echo "Erreur: Impossible de Lire une Ligne \n";
    }
    fclose($fp);
}
