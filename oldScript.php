<?php
//archivage dans alfresco methode CURL/BASCH
include('connect_alfresco.php');
	//--------definition de l'URL de login alfresco
$url = $url_alfresco.':'.$port_alfresco.'/alfresco/service/api/login';
	//--------Initialisation de l'URL
$curl = curl_init($url);
	//--------Mise en option de l'URL
curl_setopt($curl, CURLOPT_URL, $url);
	//--------Mise en option de la  METHODE
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	//--------Encodage en JSON
$headers = array(
   "Content-Type: application/json",
);
	//--------Mise en option de l'encodage
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
//------Insertion des parametres de conexion à alfresco dans la variable data
 $data = '{"username":"'.$user_alfresco.'","password":"'.$pass_alfresco.'"}';
	//--------Mise en option des parametres de connexion
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

//for debug only!
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	//-------Execution
$resp = curl_exec($curl);
    $Json_ticket = json_decode($resp, true);

$ticket = $Json_ticket["data"]["ticket"];
//echo $ticket."\n";
// ******************************************************************************************************************* RECUPERATION DU NOEUD PARTAGE

$url = $url_alfresco.':'.$port_alfresco.'/alfresco/api/-default-/public/alfresco/versions/1/nodes/-root-/children?alf_ticket='.$ticket;
           $crl = curl_init();
                    
            curl_setopt($crl, CURLOPT_URL, $url);
            curl_setopt($crl, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($crl);

            $res = json_decode($response,true);
       	 foreach ($res as $key => $value) {
           foreach ($value as $key2 => $value2) {		
                if($key2 == 'entries'){			
                    foreach ($value2 as $key3 => $value3){			
                    	foreach($value3 as $key4 => $value4){
				if($value4['name'] == 'Partagé'){
					$node_partage=$value4['id'];
				}else{ 
					
   			   }
                        }
                    }
                }
            }            
        }
############################################################################################# FIN DEMANDE TICKET ALFRESCO
	if($argc != 2){
		echo "mauvais usage du script\n";
		echo "Usage:\n		php script.php fichier\n";
		exit(1);
	}
	
	$postFields = array(
	    'uploadFieldName' => ""
	
    );
	$compte = 1; #cette variable aide a selectionner un champ different a chaque iteration
	#cette boucle permet de parcourir tout les champs du tableau postFields

	foreach($postFields as $cle => $element){
		
		
		$commande = 'cut -d# -f'.$compte.' '.$argv[1]; # construction de la commande
		$resultat = shell_exec($commande);	       # execution de la commande et recuperation du reslutat
		echo $postFields[$cle] = $resultat;             # ajout du resultat dans postFields
		$compte = $compte + 1;
	}

	foreach($postFields as $cle => $element){
		  $path="$cle => $element\n";
		
		}
		
		$registre=substr($path, -42, 16);
		$file_name=substr($path, -25, 25);
		$filePath = $file_name;
		$uploadFieldName = 'filedata';
		$data=trim($registre).'#';
		$data1=trim($filePath).'#';
		$filename = $argv[1];
############################# AJOUT DU NOM DU FICHIER PDF ET REPERTOIRE D'ECRITURE DANS ALFRESCO #######################
		 $output = shell_exec('echo '.$data.'  >> '.$filename);
		 $output = shell_exec('echo '.$data1.'  >> '.$filename);
########################################################################################################################
	$url_verif = $url_alfresco.':'.$port_alfresco.'/alfresco/api/-default-/public/alfresco/versions/1/nodes/'.$node_partage.'/children?alf_ticket='.$ticket; //verification du registre dans le dossier 'Partagé'
            $crl = curl_init();
                    
            curl_setopt($crl, CURLOPT_URL, $url_verif);
            curl_setopt($crl, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($crl);
            $res = json_decode($response,true);
            foreach ($res as $key => $value) {
		
			
            foreach ($value as $key2 => $value2) {
			
                if($key2 == 'entries'){ 
			
                    foreach ($value2 as $key3 => $value3){
				
                    	foreach($value3 as $key4 => $value4){
                                $url = $url_alfresco.':'.$port_alfresco.'/alfresco/api/-default-/public/alfresco/versions/1/nodes/-shared-/children?alf_ticket='.$ticket;
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

                                $data = '{"name":"'.$name.'","nodeType":"'.$nodeType.'"}';
			
                                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                                //for debug only!
                                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

                                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                                $resp = curl_exec($curl);
						}
					}
				}
			}
		}
		include("script_original.php");
?>
