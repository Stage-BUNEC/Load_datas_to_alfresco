<?php

include_once 'connect_alfresco.php';

// Connexion a Alfresco
$ticket = loginToAlfrsco($url_alfresco, $port_alfresco, $user_alfresco, $pass_alfresco);
//echo $ticket;

// Recuperation du Noeud 'Partage'
$node = getShareNode($url_alfresco, $port_alfresco, $ticket);
echo "Noeud = " . $node;
