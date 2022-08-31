<?php

# [ Date ]        : 31-08-2022
# [ Description ] : Ce script sert à charger les fichiers PDF avec leurs Méta-données dans Alfresco
# [Author(s) ]    : Mr GAEL MANI / NANFACK STEVE

// Verification des Arguments
if ($argc != 3) {
    echo "Mauvais usage du script\n";
    echo "Usage:	php script_anter_v1.php fichier_pdf fichier_de_meta_donnees\n";
    die();
}

// Inclusion des fonctions Utiles
include_once 'connexion_fonctions_anter_v1.php';
$dossier_cible = "/opt/consolidation/";

// Connexion a Alfresco
//$ticket = loginToAlfrsco($url_alfresco, $port_alfresco, $user_alfresco, $pass_alfresco);
//echo $ticket;

// Recuperation du Noeud 'Partage/Shared'
//$node_partage = getShareNode($url_alfresco, $port_alfresco, $ticket);
//echo "Noeud = " . $node_partage;

// Extraction du No de Registre et du Nom du fichier parmi les Meta-donnees
$registre = trim(shell_exec("head -n 1 " . $argv[2] . " | cut -d '#' -f 1 | awk -F/ '{print $(NF-1)}' "));
$fileName = trim(shell_exec("head -n 1 " . $argv[2] . " | cut -d '#' -f 1 | awk -F/ '{print $(NF)}' "));

// Creation du Registre dans ALFRESCO
//createRegister($url_alfresco, $port_alfresco, $ticket, $node_partage, $registre);

// Renommage des fichiers
$newNames = renameFiles($argv[1], $argv[2], $fileName);

// Recuperation de TOUTES les meta-donnees
$postFields = get_all_metaDatas($newNames['newMetaDatasName'], $registre, $fileName);
//print_r($postFields);

// Envoie des donnees à Alfresco
//send_datas($url_alfresco, $port_alfresco, $ticket, $postFields);

// Deplacement des fichiers [pdf] et [metadDonnees] dans un dossier journalier cible
$dossier_journalier = trim(shell_exec("date -I"));
$chemin_complet = $dossier_cible . $dossier_journalier;
shell_exec("if [ ! -d " . $chemin_complet . " ]; then mkdir -p " . $chemin_complet . " ; fi");

shell_exec("mv " . $newNames['newMetaDatasName'] . " " . $chemin_complet);
shell_exec("mv " . $newNames['newFileName'] . " " . $chemin_complet);
