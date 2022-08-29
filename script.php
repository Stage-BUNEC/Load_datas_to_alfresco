<?php

# [ Date ]  :29-08-2022
# [ Description ] :Ce script sert à charger les fichiers PDF avec leurs Méta-données dans Alfresco
# [Authors ] : Mr GAEL MANI / NANFACK STEVE

// Verification des Arguments
if ($argc != 2) {
    echo "Mauvais usage du script\n";
    echo "Usage:	php script.php fichier_de_meta_donnees\n";
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

// Extraction du No de Registre et du Nom du fichier parmi les Meta-donnees
$registre = trim(shell_exec("cut -d '/' -f 10 " . $argv[1]));
$file = trim(shell_exec("cut -d '/' -f 11 " . $argv[1] . " | cut -d '#' -f 1"));

// Creation du Registre dans ALFRESCO
createRegister($url_alfresco, $port_alfresco, $ticket, $node_partage, $registre);

// Recuperation de TOUTES les meta-donnees
$postFields = get_all_metaDatas($argv[1], $registre, $file);
//print_r($postFields);

// Envoie des donnees à Alfresco
send_datas($url_alfresco, $port_alfresco, $ticket, $postFields);
