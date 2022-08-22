<?php  
if($argc != 2){
		echo "mauvais usage du script\n";
		echo "Usage:\n		php script.php fichier\n";
		exit(1);
	}
	
	$filename = $argv[1];
	$data = '';
	if(fopen($filename,'r')){
	    $handle = fopen($filename,'r');
	    $data .= fread($handle,filesize($filename));
	    
	    fclose($handle);
	}
	$data = preg_split("/[#]/",$data);
	$postFields = array(
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
	    'relativePath' => $data[27],
	    'uploadFieldName' => $data[28],

	);

