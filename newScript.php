<?php

include_once 'connexion_fonctions.php';

// Connexion a Alfresco
$ticket = loginToAlfrsco($url_alfresco, $port_alfresco, $user_alfresco, $pass_alfresco);
//echo $ticket;

// Recuperation du Noeud 'Partage/Shared'
$node_partage = getShareNode($url_alfresco, $port_alfresco, $ticket);
//echo "Noeud = " . $node_partage;

// Extraction du No de Registre et du Nom du fichier parmi les Meta-donnees
$extract_Datas = extract_Register_And_Filename($argc, $argv[1]);

// Ajout des donnees Extrais a la fin du fichier des MetaDonnees
$registre = $extract_Datas['registre'];
$file = $extract_Datas['nom_fichier'];
$data = trim($registre) . '#';
$data1 = trim($file) . '#';
$filename = $argv[1];

// Creation du Registre dans ALFRESCO
createRegister($url_alfresco, $port_alfresco, $ticket, $node_partage, $registre);

// Recuperation de TOUTES les meta-donnees
$postFields = get_all_metaDatas($argc, $argv[1], $registre, $file);
//print_r($postFields);

// Envoie des donnees à Alfresco
send_datas($url_alfresco, $port_alfresco, $ticket, $postFields, $file);
