<?php

include_once 'connect_alfresco.php';

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
$uploadFieldName = 'filedata';
$output = shell_exec('echo ' . $data . '  >> ' . $filename);
$output = shell_exec('echo ' . $data1 . '  >> ' . $filename);

// Creation du Registre dans ALFRESCO
createRegister($url_alfresco, $port_alfresco, $ticket, $node_partage, $registre);

// function load_all_metaDatas():array
// {

// }
