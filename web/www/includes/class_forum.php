<?php 
class base_forum extends DB_Sql {
var $begin;
var $commit;
var $transaction = true;
var $classname = "base_delain";
var $Database = "z_forum";
//var $Port = 9999;
var $User     = "sdewitte";
var $Password = "";

function begin() {
	if ($this->transaction)
   {
		$this->begin = pg_Exec($this->Link_ID, "begin;");
	}
}
function commit() {
	if ($this->transaction)
   {
		$this->commit = pg_Exec($this->Link_ID, "commit;");
	}
}

function query($Query_String) {
    /* No empty queries, please, since PHP4 chokes on them. */
    if ($Query_String == "")
      /* The empty query string is passed on from the constructor,
       * when calling the class without a query, e.g. in situations
       * like these: '$db = new DB_Sql_Subclass;'
       */
      return 0;
      /*$pos = strpos($Query_String,";");
     	if ($pos != false)
     	{
     		$Query_String = substr($Query_String,0,$pos);
     	}*/
		
		$pos = strpos($Query_String,"pg_tables");
		if ($pos != false)
     	{
     		$Query_String = substr($Query_String,0,$pos);
     	}
    $this->connect();
    
    if ($this->Debug) 
      printf("<br>Debug: query = %s<br>\n", $Query_String);flush();

    $this->Query_ID = pg_Exec($this->Link_ID, $Query_String);
    $retour = $this->Query_ID; 
    $this->Row   = 0;

    $this->Error = pg_ErrorMessage($this->Link_ID);
    $this->Errno = ($this->Error == "")?0:1;
    
    if (!$this->Query_ID) {
    	$this->halt("Invalid SQL: ".$Query_String);
    }
    return $this->Query_ID;
  }


  function halt($msg) {
  	$message = $msg;
  	$requete = $this->Errno;
  	$libelle = $this->Error;
    printf("<p class=\"titre\"><b>Erreur base de données (forum):</b> %s</p>\n", $message);
    printf("<p><b>Erreur PostgreSQL</b>: %s (%s)<br>\n",
      $requete,
      $libelle);
   $envoi = 1;
   if ($message == 'Invalid SQL: select compt_admin from compte where compt_cod =  ')
   {
   	$envoi = 0;
	}
   $mail = 'merrick@jdr-delain.net';
   /*$message = str_replace(";",str(127),$message);  
   $requete = str_replace(";",str(127),$requete); 
   $libelle = str_replace(";",str(127),$libelle);  */
	$texte_mail = "Erreur Delain :\r\n";
	$texte_mail = $texte_mail . "message : *" . $message . "*\r\n";
	$texte_mail = $texte_mail . "requete : *" . $requete . "*\r\n";
	$texte_mail = $texte_mail . "libelle : *" .$libelle . "*\r\n";
	$entete = "From: merrick@jdr-delain.net\r\n";
	$entete = $entete . "Reply-To: merrick@jdr-delain.net\r\n";
	$entete = $entete . "Error-To: merrick@jdr-delain.net\n";
	$sujet = "Erreur SQL ^Forum\r\n";
	if ($envoi == 1)
	{
		if(mail($mail,$sujet,$texte_mail,$entete))
		{
			$ok = 1;
		}
		else
		{
			$ok = 0;
		}
	}
    die("Session arrêtée.");
  }

}
?>