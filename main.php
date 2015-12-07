<?php
/**
* Telegram Bot example for MateraEvents Lic CC-BY-SA 4.0
* @author Francesco Piero Paolicelli @piersoft
*/
//include("settings_t.php");
include("Telegram.php");

class mainloop{
const MAX_LENGTH = 4096;
function start($telegram,$update)
{

	date_default_timezone_set('Europe/Rome');
	$today = date("Y-m-d H:i:s");
	//$data=new getdata();
	// Instances the class

	/* If you need to manually take some parameters
	*  $result = $telegram->getData();
	*  $text = $result["message"] ["text"];
	*  $chat_id = $result["message"] ["chat"]["id"];
	*/


	$text = $update["message"] ["text"];
	$chat_id = $update["message"] ["chat"]["id"];
	$user_id=$update["message"]["from"]["id"];
	$location=$update["message"]["location"];
	$reply_to_msg=$update["message"]["reply_to_message"];

	$this->shell($telegram,$text,$chat_id,$user_id,$location,$reply_to_msg);
	$db = NULL;

}

//gestisce l'interfaccia utente
 function shell($telegram,$text,$chat_id,$user_id,$location,$reply_to_msg)
{
	date_default_timezone_set('Europe/Rome');
	$today = date("Y-m-d H:i:s");

	if ($text == "/start" || $text == "Informazioni") {
		$reply = "Benvenuto. Per ricercare un Evento censito da MateraEvents.it , digita il nome del Comune oppure clicca sulla graffetta (📎) e poi 'posizione' o ancora per ricercare per parola chiave nel titolo, anteponi il carattere ?. Verrà interrogato il DataBase openData utilizzabile con licenza CC-BY-SA presente sul portale openData del Comune di Matera. Se il tuo evento non è recensito puoi usare il form pubblico curato dalla Fondazione Matera 2019 disponibile a questo link http://goo.gl/qaf7fE .In qualsiasi momento scrivendo /start ti ripeterò questo messaggio di benvenuto.\nQuesto bot è stato realizzato da @piersoft e donato alla Fondazione Matera 2019 con licenza MIT. La propria posizione viene ricercata grazie al geocoder di openStreetMap con Lic. odbl.";
		$content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
		$telegram->sendMessage($content);
		$img = curl_file_create('mt2019.png','image/png');
		$contentp = array('chat_id' => $chat_id, 'photo' => $img);
		$telegram->sendPhoto($contentp);
		$log=$today. ";new chat started;" .$chat_id. "\n";
		$this->create_keyboard_temp($telegram,$chat_id);

		exit;
		}
		elseif ($text == "Città") {
			$reply = "Digita direttamente il nome del Comune.";
			$content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
			$telegram->sendMessage($content);
			$log=$today. ";new chat started;" .$chat_id. "\n";
	//		$this->create_keyboard_temp($telegram,$chat_id);
exit;
			}
			elseif ($text == "Ricerca") {
				$reply = "Scrivi la parola da cercare anteponendo il carattere ?, ad esempio: ?Mostra";
				$content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
				$telegram->sendMessage($content);
				$log=$today. ";new chat started;" .$chat_id. "\n";
	//			$this->create_keyboard_temp($telegram,$chat_id);
exit;
			}elseif ($text == "oggi" || $text == "Oggi"){
				$img = curl_file_create('materaevents.png','image/png');
				$contentp = array('chat_id' => $chat_id, 'photo' => $img);
				$telegram->sendPhoto($contentp);
					$location="Sto cercando gli eventi censiti da MateraEvents validi nella giornata di oggi";
					$content = array('chat_id' => $chat_id, 'text' => $location,'disable_web_page_preview'=>true);
					$telegram->sendMessage($content);
					$text=str_replace(" ","%20",$text);
					$urlgd  ="https://spreadsheets.google.com/tq?tqx=out:csv&tq=SELECT%20%2A%20WHERE%20L%20IS%20NOT%20NULL&key=1twgz4fIBERB2msz1WHGJ0MbcwmLsx82MLgaFfZ_46h0&gid=0";
					sleep (2);

						$inizio=1;
						$homepage ="";

						$csv = array_map('str_getcsv',file($urlgd));
//var_dump($csv[1][0]);
				$count = 0;
				foreach($csv as $data=>$csv1){
					$count = $count+1;
					}
					if ($count ==0 || $count ==1){
						$location="Nessun risultato trovato";
						$content = array('chat_id' => $chat_id, 'text' => $location,'disable_web_page_preview'=>true);
						$telegram->sendMessage($content);
					}

					date_default_timezone_set('Europe/Rome');
					date_default_timezone_set("UTC");
					$today=time();
//echo $count;
//  $count=3;
function decode_entities($text) {
		$text= html_entity_decode($text,ENT_QUOTES,"ISO-8859-1"); #NOTE: UTF-8 does not work!
		$text= preg_replace('/&#(\d+);/me',"chr(\\1)",$text); #decimal notation
		$text= preg_replace('/&#x([a-f0-9]+);/mei',"chr(0x\\1)",$text);  #hex notation
		return $text;
}

	for ($i=$inizio;$i<$count;$i++){

		$html =str_replace("/","-",$csv[$i][7]);
		$from = strtotime($html);
		$html1 =str_replace("/","-",$csv[$i][8]);
		$to = strtotime($html1);


		if ($today >= $from && $today <= $to) {

//$homepage .="da: ".$from." a: ".$to." con oggi: ".$today."\n";
$homepage .="\n";
$homepage .="Nome: ".$csv[$i][1]."\n";
$homepage .="Organizzato da: ".$csv[$i][2]."\n";
if($csv[$i][5] !=NULL)$homepage .="Pagamento: ".$csv[$i][5]."\n";
$homepage .="Tipologia: ".$csv[$i][6]."\n";
if($csv[$i][4] !=NULL)  $homepage .="Descrizione: ".decode_entities($csv[$i][4])."\n";
$homepage .="Inizio: ".$csv[$i][7]."\n";
$homepage .="Fine: ".$csv[$i][8]."\n";
$homepage .="Luogo: ".$csv[$i][9]."\n";
if($csv[$i][12] !=NULL) $homepage .="Web: ".$csv[$i][12]."\n";
if($csv[$i][13] !=NULL) $homepage .="Email: ".$csv[$i][13]."\n";
if($csv[$i][16] !=NULL)  $homepage .="Foto: ".$csv[$i][16]."\n";
$homepage .="____________\n";

}
}

//}

//	echo $alert;

$chunks = str_split($homepage, self::MAX_LENGTH);
foreach($chunks as $chunk) {
$content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true);
$telegram->sendMessage($content);

}
//	$this->create_keyboard_temp($telegram,$chat_id);

exit;

}elseif($location!=null)
		{

			$this->location_manager($telegram,$user_id,$chat_id,$location);
			exit;

		}
//elseif($text !=null)

		elseif(strpos($text,'/') === false){
			$img = curl_file_create('materaevents.png','image/png');
			$contentp = array('chat_id' => $chat_id, 'photo' => $img);
			$telegram->sendPhoto($contentp);

			if(strpos($text,'?') !== false){
			  $text=str_replace("?","",$text);
			  $location="Sto cercando gli eventi aventi nel titolo: ".$text;
			  $content = array('chat_id' => $chat_id, 'text' => $location,'disable_web_page_preview'=>true);
			  $telegram->sendMessage($content);
			  $text=str_replace(" ","%20",$text);
				$text=strtoupper($text);
			  $urlgd  ="https://spreadsheets.google.com/tq?tqx=out:csv&tq=SELECT%20%2A%20WHERE%20upper(B)%20LIKE%20%27%25";
			  $urlgd .=$text;
			  $urlgd .="%25%27&key=1twgz4fIBERB2msz1WHGJ0MbcwmLsx82MLgaFfZ_46h0&gid=0";
			  sleep (1);

			}else{

			  $location="Sto cercando gli eventi censiti da MateraEvents del Comune di: ".$text;
			  $content = array('chat_id' => $chat_id, 'text' => $location,'disable_web_page_preview'=>true);
			  $telegram->sendMessage($content);
			  $text=str_replace(" ","%20",$text);
				$text=strtoupper($text);
			  $urlgd  ="https://spreadsheets.google.com/tq?tqx=out:csv&tq=SELECT%20%2A%20WHERE%20upper(J)%20LIKE%20%27%25";
			  $urlgd .=$text;
			  $urlgd .="%25%27&key=1twgz4fIBERB2msz1WHGJ0MbcwmLsx82MLgaFfZ_46h0&gid=0";


			  sleep (1);
			}

			  $inizio=1;
			  $homepage ="";
			  //$comune="Lecce";

			//echo $urlgd;
			  $csv = array_map('str_getcsv',file($urlgd));
			//var_dump($csv[1][0]);
			  $count = 0;
			  foreach($csv as $data=>$csv1){
			    $count = $count+1;
			  }

			if ($count ==0 || $count ==1){
			      $location="Nessun risultato trovato";
			      $content = array('chat_id' => $chat_id, 'text' => $location,'disable_web_page_preview'=>true);
			      $telegram->sendMessage($content);
			    }
			    function decode_entities($text) {
						$text=htmlentities($text, ENT_COMPAT,'ISO-8859-1', true);
					$text= preg_replace('/&#(\d+);/me',"chr(\\1)",$text); #decimal notation
						$text= preg_replace('/&#x([a-f0-9]+);/mei',"chr(0x\\1)",$text);  #hex notation
					$text= html_entity_decode($text,ENT_COMPAT,"UTF-8"); #NOTE: UTF-8 does not work!
  return $text;
			    }
			  for ($i=$inizio;$i<$count;$i++){


			    $homepage .="\n";
			    $homepage .="Nome: ".$csv[$i][1]."\n";
			    $homepage .="Organizzato da: ".$csv[$i][2]."\n";
			    if($csv[$i][5] !=NULL)$homepage .="Pagamento: ".$csv[$i][5]."\n";
			    $homepage .="Tipologia: ".$csv[$i][6]."\n";
			    if($csv[$i][4] !=NULL)  $homepage .="Descrizione: ".decode_entities($csv[$i][4])."\n";
			    $homepage .="Inizio: ".$csv[$i][7]."\n";
			    $homepage .="Fine: ".$csv[$i][8]."\n";
			    $homepage .="Luogo: ".$csv[$i][9]."\n";
			    if($csv[$i][12] !=NULL) $homepage .="Web: ".$csv[$i][12]."\n";
			    if($csv[$i][13] !=NULL) $homepage .="Email: ".$csv[$i][13]."\n";
			  	if($csv[$i][16] !=NULL)  $homepage .="Foto: ".$csv[$i][16]."\n";

					if($csv[$i][10] !=NULL){
						$homepage .="Mappa:\n";
						$homepage .= "http://www.openstreetmap.org/?mlat=".$csv[$i][10]."&mlon=".$csv[$i][11]."#map=19/".$csv[$i][10]."/".$csv[$i][11];
					}
					    $homepage .="\n____________\n";
			    // lat 10, lon 11

			}

	//}

	//	echo $alert;

		$chunks = str_split($homepage, self::MAX_LENGTH);
		foreach($chunks as $chunk) {
			$content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true);
			$telegram->sendMessage($content);

		}
	//	$this->create_keyboard_temp($telegram,$chat_id);
exit;
}

	}

	function create_keyboard_temp($telegram, $chat_id)
	 {
			 $option = array(["Città","Ricerca"],["Oggi","Informazioni"]);
			 $keyb = $telegram->buildKeyBoard($option, $onetime=false);
			 $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "[Digita un Comune, una parola da ricerca anteponendo ? oppure invia la tua posizione tramite la graffetta (📎)]");
			 $telegram->sendMessage($content);
	 }



function location_manager($telegram,$user_id,$chat_id,$location)
	{
		$img = curl_file_create('materaevents.png','image/png');
		$contentp = array('chat_id' => $chat_id, 'photo' => $img);
		$telegram->sendPhoto($contentp);
			$lon=$location["longitude"];
			$lat=$location["latitude"];
			$response=$telegram->getData();
			$response=str_replace(" ","%20",$response);

				$reply="http://nominatim.openstreetmap.org/reverse?email=piersoft2@gmail.com&format=json&lat=".$lat."&lon=".$lon."&zoom=18&addressdetails=1";
				$json_string = file_get_contents($reply);
				$parsed_json = json_decode($json_string);
				//var_dump($parsed_json);
				$comune="";
				$temp_c1 =$parsed_json->{'display_name'};

				if ($parsed_json->{'address'}->{'town'}) {
					$temp_c1 .="\nCittà: ".$parsed_json->{'address'}->{'town'};
					$comune .=$parsed_json->{'address'}->{'town'};
				}else 	$comune .=$parsed_json->{'address'}->{'city'};

				if ($parsed_json->{'address'}->{'village'}) $comune .=$parsed_json->{'address'}->{'village'};
				$location="Comune di: ".$comune." tramite le coordinate che hai inviato: ".$lat.",".$lon;
				$content = array('chat_id' => $chat_id, 'text' => $location,'disable_web_page_preview'=>true);
				$telegram->sendMessage($content);

			  $alert="";
				echo $comune;
				$urlgd  ="https://spreadsheets.google.com/tq?tqx=out:csv&tq=SELECT%20%2A%20WHERE%20J%20LIKE%20%27%25";
				$urlgd .=$comune;
				$urlgd .="%25%27&key=1twgz4fIBERB2msz1WHGJ0MbcwmLsx82MLgaFfZ_46h0&gid=0";

				sleep (1);

					$inizio=1;
					$homepage ="";
				  //$comune="Lecce";

				//echo $urlgd;
					$csv = array_map('str_getcsv',file($urlgd));
				//var_dump($csv[1][0]);
				  $count = 0;
					foreach($csv as $data=>$csv1){
						$count = $count+1;
					}
					if ($count ==0 || $count ==1){
						$location="Nessun risultato trovato";
						$content = array('chat_id' => $chat_id, 'text' => $location,'disable_web_page_preview'=>true);
						$telegram->sendMessage($content);
					}
					function decode_entities($text) {
						$text=htmlentities($text, ENT_COMPAT,'ISO-8859-1', true);
					$text= preg_replace('/&#(\d+);/me',"chr(\\1)",$text); #decimal notation
						$text= preg_replace('/&#x([a-f0-9]+);/mei',"chr(0x\\1)",$text);  #hex notation
					$text= html_entity_decode($text,ENT_COMPAT,"UTF-8"); #NOTE: UTF-8 does not work!
					return $text;
					}
				  //echo $count;
				//  $count=3;
					for ($i=$inizio;$i<$count;$i++){

						$homepage .="\n";
						$homepage .="Nome: ".$csv[$i][1]."\n";
						$homepage .="Organizzato da: ".$csv[$i][2]."\n";
						if($csv[$i][5] !=NULL)$homepage .="Pagamento: ".$csv[$i][5]."\n";
						$homepage .="Tipologia: ".$csv[$i][6]."\n";
						if($csv[$i][4] !=NULL)  $homepage .="Descrizione: ".decode_entities($csv[$i][4])."\n";
						$homepage .="Inizio: ".$csv[$i][7]."\n";
						$homepage .="Fine: ".$csv[$i][8]."\n";
						$homepage .="Luogo: ".$csv[$i][9]."\n";
						if($csv[$i][12] !=NULL) $homepage .="Web: ".$csv[$i][12]."\n";
						if($csv[$i][13] !=NULL) $homepage .="Email: ".$csv[$i][13]."\n";
						if($csv[$i][16] !=NULL)  $homepage .="Foto: ".$csv[$i][16]."\n";
						if($csv[$i][10] !=NULL){
							$homepage .="Mappa:\n";
							$homepage .= "http://www.openstreetmap.org/?mlat=".$csv[$i][10]."&mlon=".$csv[$i][11]."#map=19/".$csv[$i][10]."/".$csv[$i][11];
						}
						$homepage .="\n____________\n";

					}

		//}

		//	echo $alert;

			$chunks = str_split($homepage, self::MAX_LENGTH);
			foreach($chunks as $chunk) {
				$content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true);
				$telegram->sendMessage($content);

			}
		//	$this->create_keyboard_temp($telegram,$chat_id);

	}


}

?>
