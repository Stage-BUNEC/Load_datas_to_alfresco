<?php

include_once 'connect_alfresco.php';

// Connexion a Alfresco
$ticket = loginToAlfrsco($url_alfresco, $port_alfresco, $user_alfresco, $pass_alfresco);
//echo $ticket;

// Recuperation du Noeud 'Partage/Shared'
$node = getShareNode($url_alfresco, $port_alfresco, $ticket);
//echo "Noeud = " . $node;

// Extraction du No de Registre et du Nom du fichier
$extract_Datas = extract_Register_And_Filename($argc, $argv[1]);

// $uploadFieldName = 'filedata';
// $data = trim($registre) . '#';
// $data1 = trim($file_name) . '#';
// $filename = $argv[1];
// ############################# AJOUT DU NOM DU FICHIER PDF ET REPERTOIRE D'ECRITURE DANS ALFRESCO #######################
// $output = shell_exec('echo ' . $data . '  >> ' . $filename);
// $output = shell_exec('echo ' . $data1 . '  >> ' . $filename);
