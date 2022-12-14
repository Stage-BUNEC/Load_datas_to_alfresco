<?php

$url_alfresco = 'http://172.16.1.99'; // URL du serveur Alfresco
$port_alfresco = '8080'; // Port
$user_alfresco = 'admin'; // User Alfresco
$pass_alfresco = 'bunec'; // Mot de Passe de l'utilisateur

function loginToAlfrsco(String $url_alfresco, String $port_alfresco, String $user_alfresco, String $pass_alfresco): string
{
    # Cette fonction permet de recuperer le ticket de connexion à Alfresco
    # et retourne ce ticket/token dans ($ticket)

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
    # Cette fonction recupere l'ID du Noeud/Dossier "Shared/Partagé" de Alfresco
    # et retourne cet ID ($node_partage)

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

function createRegister(String $url_alfresco, String $port_alfresco, String $ticket, String $node_partage, String $registre)
{
    # Cette fonction cree un Noeud/Dossier (vide) dans Alfresco. Ce dossier est un registre
    # [$registre] : contient le numero de registre (sera le nom du dossier cree)
    # [$node_partage] : contient l'ID du dossier Shared/Partagé de Alfresco (ou sera cree le Noeud)

    $url_verif = $url_alfresco . ':' . $port_alfresco . '/alfresco/api/-default-/public/alfresco/versions/1/nodes/' . $node_partage . '/children?alf_ticket=' . $ticket; //verification du registre dans le dossier 'Partagé'
    $crl = curl_init();
    curl_setopt($crl, CURLOPT_URL, $url_verif);
    curl_setopt($crl, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($crl);
    $res = json_decode($response, true);

    foreach ($res as $key => $value) {

        foreach ($value as $key2 => $value2) {

            if ($key2 == 'entries') {
                $url = $url_alfresco . ':' . $port_alfresco . '/alfresco/api/-default-/public/alfresco/versions/1/nodes/-shared-/children?alf_ticket=' . $ticket;
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                $headers = array(
                    "Content-Type: application/json",
                );
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

                $name = $registre;
                $nodeType = "cm:folder";
                $data = '{"name":"' . $name . '","nodeType":"' . $nodeType . '"}';

                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                //for debug only!
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                $resp = curl_exec($curl);
            }
        }
    }
}

function renameFiles(String $oldFileName, String $oldMetaDatasName, String $newFileName, String $workPath): array
{
    # pour avoir le (nouveau) nom du fichier de meta-donnees on remplace 'pdf' par 'txt'
    $newMetaDatasName = str_replace("pdf", "txt", $newFileName);
    $newMetaDatasName = $workPath . $newMetaDatasName;
    shell_exec("mv " . $oldMetaDatasName . " " . $newMetaDatasName);

    # On ajoute le Path du dossier de travail au (nouveau) nom (extrait dans les meta-donnees precedemmant)
    $newFileName = $workPath . $newFileName;
    shell_exec("mv " . $oldFileName . " " . $newFileName);

    return array('newMetaDatasName' => $newMetaDatasName, 'newFileName' => $newFileName);
}

function get_all_metaDatas($metaDatas, String $registre, String $file): array
{
    # Cette fonction recupere toutes les meta-donnees et retourne le tableau ($postFields) les contenants
    # [$registre] : contient le numero de registre
    # [$file] : contient le nom du fichier pdf a envoyer

    if (file_exists($metaDatas)) {
        $data = '';
        // Recuperation des meta-donnees du fichier
        if (fopen($metaDatas, 'r')) {
            $handle = fopen($metaDatas, 'r');
            $data .= fread($handle, filesize($metaDatas));
            fclose($handle);

            // chargement des meta-donnees lues dans la variable $data
            $data = preg_split("/[#]/", $data);
        }
    } else {
        // chargement des meta-donnees lues dans la variable $data
        $data = preg_split("/[#]/", $metaDatas);
    }

    // Chargement de toutes les donnees dans $postFields
    $postFields = array(
        'relativePath' => $registre,
        'filedata' => new CURLFILE($file),
        'bc:numact' => $data[1],
        'bc:firstname' => $data[2],
        'bc:lastname' => $data[3],
        'bc:bornOnThe' => $data[4],
        'bc:bornAt' => $data[5],
        'bc:sex' => $data[6],
        'bc:of' => $data[7],
        'bc:fOnThe' => $data[8],
        'bc:fAt' => $data[9],
        'bc:fresid' => $data[10],
        'bc:foccupation' => $data[11],
        'bc:fnationality' => $data[12],
        'bc:fdocref' => $data[13],
        'bc:mof' => $data[14],
        'bc:mAt' => $data[15],
        'bc:mOnThe' => $data[16],
        'bc:mresid' => $data[17],
        'bc:mOccupation' => $data[18],
        'bc:mnationality' => $data[19],
        'bc:mdocref' => $data[20],
        'bc:drawingUp' => $data[21],
        'bc:ondecof' => $data[22],
        'bc:byUs' => $data[23],
        'bc:assistedof' => $data[24],
        'bc:onthe' => $data[25],
        'bc:mentionMarg' => $data[26],
    );

    return $postFields;
}

function send_datas(String $url_alfresco, String $port_alfresco, String $ticket, array $postFields)
{
    # Cette fonction permet d'envoyer les Meta-donnees et le fichier à Alfresco
    # [$postFields] : Ce parametre contient les meta-donnees et le fichier a envoyer

    $api = $url_alfresco . ':' . $port_alfresco . '/alfresco/api/-default-/public/alfresco/versions/1/nodes/-shared-/children?alf_ticket=' . $ticket;
    //Initiate cURL
    $ch = curl_init();
    //Set the URL
    curl_setopt($ch, CURLOPT_URL, $api);
    //Methode POST pour la requete HTTP
    curl_setopt($ch, CURLOPT_POST, true);
    //Tell cURL to return the output as a string.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Chargement des MetaDonnees dans la requete
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    //Execute the request
    $result = curl_exec($ch);
    //print_r($result);
    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }
}
