<?php
$url_alfresco = 'http://172.16.1.99'; // URL du serveur Alfresco
$port_alfresco = '8080'; // Port
$user_alfresco = 'admin'; // User Alfresco
$pass_alfresco = 'bunec'; // Mot de Passe de l'utilisateur

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

function getShareNode(String $url_alfresco, String $port_alfresco, String $ticket): String
{
    $node_partage = '';
    $url = $url_alfresco . ':' . $port_alfresco . '/alfresco/api/-default-/public/alfresco/versions/1/nodes/-root-/children?alf_ticket=' . $ticket;
    $crl = curl_init();

    curl_setopt($crl, CURLOPT_URL, $url);
    curl_setopt($crl, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($crl);

    $res = json_decode($response, true);
    foreach ($res as $key => $value) {
        foreach ($value as $key2 => $value2) {
            if ($key2 == 'entries') {
                foreach ($value2 as $key3 => $value3) {
                    foreach ($value3 as $key4 => $value4) {
                        if ($value4['name'] == 'Partagé' || $value4['name'] == 'Shared') {
                            $node_partage = $value4['id'];
                        } else {

                        }
                    }
                }
            }
        }
    }

    return $node_partage;
}

function extract_Register_And_Filename(int $argc, String $metaDataFile): array
{
    if ($argc != 2) {
        echo "mauvais usage du script\n";
        echo "Usage:\n		php script.php fichier\n";
        exit(1);
    }

    $postFields = array(
        'uploadFieldName' => "",
    );
    $compte = 1; #cette variable aide a selectionner un champ different a chaque iteration
    #cette boucle permet de parcourir tout les champs du tableau postFields

    foreach ($postFields as $cle => $element) {

        $commande = 'cut -d# -f' . $compte . ' ' . $metaDataFile; # construction de la commande
        $resultat = shell_exec($commande); # execution de la commande et recuperation du reslutat
        $postFields[$cle] = $resultat; # ajout du resultat dans postFields
        $compte = $compte + 1;
    }

    foreach ($postFields as $cle => $element) {
        $path = "$cle => $element\n";

    }

    $registre = substr($path, -42, 16);
    $file_name = substr($path, -25, 25);

    return array('registre' => $registre, 'nom_fichier' => $file_name);
}
