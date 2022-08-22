<?php

include_once 'connect_alfresco.php';

function loginToAlfrsco(String $url_alfresco, String $port_alfresco, String $user_alfresco, String $pass_alfresco): string
{

    $url = $url_alfresco . ':' . $port_alfresco . '/alfresco/service/api/login';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $headers = array(
        "Content-Type: application/json",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

//------Insertion des parametres de conexion à alfresco dans la variable data
    $data = '{"username":"' . $user_alfresco . '","password":"' . $pass_alfresco . '"}';
//--------Mise en option des parametres de connexion
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

//for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $resp = curl_exec($curl);
    $Json_ticket = json_decode($resp, true);
    $ticket = $Json_ticket["data"]["ticket"];

    return $ticket;
};

$ticket = loginToAlfrsco($url_alfresco, $port_alfresco, $user_alfresco, $pass_alfresco);
