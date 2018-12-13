<script language="Javascript">
	var isMozilla = (navigator.userAgent.toLowerCase().indexOf('gecko')!=-1) ? true : false;
	var regexp = new RegExp("[\r]","gi");
	function storeCaret(selec) {
		if (isMozilla) {
			oField = document.forms['journal'].elements['contenu'];
			objectValue = oField.value;
			deb = oField.selectionStart;
			fin = oField.selectionEnd;
			objectValueDeb = objectValue.substring( 0 , oField.selectionStart );
			objectValueFin = objectValue.substring( oField.selectionEnd , oField.textLength );
			objectSelected = objectValue.substring( oField.selectionStart ,oField.selectionEnd );
			oField.value = objectValueDeb + "[" + selec + "]" + objectSelected + "[/" + selec + "]" + objectValueFin;
			oField.selectionStart = strlen(objectValueDeb);
			oField.selectionEnd = strlen(objectValueDeb + "[" + selec + "]" + objectSelected + "[/" + selec + "]");
			oField.focus();
			oField.setSelectionRange(
				objectValueDeb.length + selec.length + 2,
				objectValueDeb.length + selec.length + 2);
			} else {
			oField = document.forms['journal'].elements['contenu'];
			var str = document.selection.createRange().text;
			if (str.length>0) {
				var sel = document.selection.createRange();
				sel.text = "[" + selec + "]" + str + "[/" + selec + "]";
				sel.collapse();
				sel.select();
			} else {
				oField.focus(oField.caretPos);
				oField.focus(oField.value.length);
				oField.caretPos = document.selection.createRange().duplicate();
				var bidon = "%~%";
				var orig = oField.value;
				oField.caretPos.text = bidon;
				var i = oField.value.search(bidon);
				oField.value = orig.substr(0,i) + "[" + selec + "][/" + selec + "]" + orig.substr(i, oField.value.length);
				var r = 0;
				for(n = 0; n < i; n++)
				{if(regexp.test(oField.value.substr(n,2)) == true){r++;}};
				pos = i + 2 + selec.length - r;
				var r = oField.createTextRange();
				r.moveStart('character', pos);
				r.collapse();
				r.select();
			}
		}
	}
</script>


<?php /* Intégration du BBcode par Maverick le 18/10/11. *//*
$arrayBBCode=array(
		''=>			array('type'=>BBCODE_TYPE_ROOT),
		'b'=>		array('type'=>BBCODE_TYPE_NOARG, 'open_tag'=>'<strong>', 'close_tag'=>'</strong>'),
    'i'=>			array('type'=>BBCODE_TYPE_NOARG, 'open_tag'=>'<i>', 'close_tag'=>'</i>'),
		'u'=>		array('type'=>BBCODE_TYPE_NOARG, 'open_tag'=>'<u>', 'close_tag'=>'</u>'),
    'url'=>	array('type'=>BBCODE_TYPE_OPTARG, 'open_tag'=>'<a target="_blank" href="{PARAM}">', 'close_tag'=>'</a>', 'default_arg'=>'{CONTENT}'),
    'img'=>	array('type'=>BBCODE_TYPE_NOARG, 'open_tag'=>'<img alt="image" style="max-width:200px;max-height:200px;" src="', 'close_tag'=>'" />')
);
$BBHandler=bbcode_create($arrayBBCode);*/

/* Journal */
if(!isset($methode))
{
	$methode = "debut";
}
switch ($methode)
{
	case "debut":
		$req_journal = "select journal_cod,journal_perso_cod,to_char(journal_date,'dd/mm/yyyy hh24:mi:ss') as jdate,journal_titre,journal_date from journal
			where journal_perso_cod = $perso_cod
			order by journal_date desc ";
		$db->query($req_journal);
		$nb_journal = $db->nf();
		if ($nb_journal == 0)
		{
			echo("<p>Aucune entrée dans le journal.");
		}
		else
		{
			// ici on met le détail du journal
			?>
			<table>
			<?php 
			while($db->next_record())
			{
				$perso = $db->f("journal_perso_cod");
				if ($perso != $perso_cod)
				{
					echo "Vous ne pouvez pas avoir accès à cette entrée de journal !";
					break;
				}
				echo("<tr>");
//					echo '<td><p><a href=\"' . $PHP_SELF , "?journal_cod=" , $db->f("journal_cod") , "&t=" , $t , "&methode=voir\">" , $db->f("journal_titre") , "</a> (", $db->f("jdate") , ")</td>";
				echo '<td><a href="javascript:void(0);" onclick="getdata(\'' .  $type_flux . G_URL . 'jeu_test/fr_dr.php?methode=voir&t_frdr=3&journal_cod=' . $db->f("journal_cod") . '\',\'vue_droite\')">' . $db->f("journal_titre")  . '</a></td>';
				echo("</tr>");
			}
			echo("</table>");
		}
		echo("<hr>");
		echo '<div style="text-align:center;"><a href="javascript:void(0);" onclick="getdata(\'' .  $type_flux . G_URL . 'jeu_test/fr_dr.php?methode=ajout1&t_frdr=3\',\'vue_droite\')">Ajouter une entrée dans le journal.</a></div>';
		break;
	case "voir":
		?>
		<form name="inter_journal" method="post" action="<?php echo $PHP_SELF;?>">
		<input type="hidden" name="t" value="<?php echo $t_frdr;?>">
		<input type="hidden" name="methode" value="rien">
		<input type="hidden" name="journal_cod" value="<?php  echo("$journal_cod") ?>">
		<table>
		<tr><td class="soustitre2" colspan="2">
		<?php 
		$req_journal = "select journal_titre,journal_perso_cod,to_char(journal_date,'dd/mm/yyyy hh24:mi:ss') as jour_date,journal_texte from journal
														where journal_cod = $journal_cod ";
		$db->query($req_journal);
		$db->next_record();
		$perso = $db->f("journal_perso_cod");
		if ($perso != $perso_cod)
		{
			echo "Vous ne pouvez pas avoir accès à cette entrée de journal !";
			break;
		}
		print "<p><strong>" . $db->f("journal_titre") . "</strong><br />";
		printf("Ecrit le %s</p></td></tr>",$db->f("jour_date"));
		$texte = str_replace(chr(127),";",$db->f("journal_texte"));
		$texte = nl2br($texte);
		?>
		<tr><td colspan="2"><p><?php  /*echo bbcode_parse($BBHandler, $texte);*/ echo $texte; ?></td></tr>
		<tr><td class="soustitre2">
		<a href="javascript:void(0);" onclick="getdata('<?php  echo $type_flux . G_URL; ?>jeu_test/fr_dr.php?methode=modif1&journal_cod=<?php echo $journal_cod;?>&t_frdr=<?php echo $t_frdr;?>','vue_droite');">Modifier</a>
		</td><td class="soustitre2">
		<a href="javascript:void(0);" onclick="getdata('<?php  echo $type_flux . G_URL; ?>jeu_test/fr_dr.php?methode=effacer&journal_cod=<?php echo $journal_cod;?>&t_frdr=<?php echo $t_frdr;?>','vue_droite');">Effacer</a>
		</td></tr>
		</table>
		</form>
		
		<?php 
		break;
	case "effacer":
		$req_journal = "select journal_perso_cod from journal
														where journal_cod = $journal_cod ";
		$db->query($req_journal);
		$db->next_record();
		$perso = $db->f("journal_perso_cod");
		if ($perso != $perso_cod)
		{
			?>
      <p><strong>Vous ne pouvez pas effacer cette entrée de journal !</strong></p>
			<?php 
			break;
		}
   	$req_efface = "delete from journal where journal_cod = ".$journal_cod;
      $db->query($req_efface);
      ?>
		<p><strong>L'entrée dans le journal a bien été effacée !</strong></p>
		<?php 
    	break;
   case "ajout1":
   	?>
   	<form name="journal">
		<input type="hidden" name="methode" value="ajout2">
		<input type="hidden" name="t_frdr" value="<?php echo $t_frdr;?>">
		<center><table>
			<tr>
				<td><p>Titre : </p></td>
				<td><input type="text" name="titre"></td>
			</tr>
			<tr>
				<td><p>Contenu : </p></td>
				<td>
<noscript></noscript>
					<input type="button" value="b" style="width:50px;font-weight:bold" onclick="storeCaret('b')">
					<input type="button" value="i" style="width:50px;font-style:italic" onclick="storeCaret('i')">
					<input type="button" value="u" style="width:50px;text-decoration:underline" onclick="storeCaret('u')">
					<input type="button" value="url" style="width:50px" onclick="storeCaret('url')">
					<input type="button" value="img" style="width:50px" onclick="storeCaret('img')"><br />
					<textarea name="contenu" id="contenu" rows="10" wrap="virtual" cols="45"></textarea>
				</td>
<?php /*<td><textarea name="contenu" cols="30" rows="15"></textarea></td>*/	?>
			</tr>
			<tr>
				<td colspan="2"><p style="text-align:center"><input type="button" value="Valider" class="test" accesskey="s" onClick="voirList(this,'<?php echo $PHP_SELF;?>','vue_droite');"></td>
			</tr>
		</table></center>
		</form>
		<?php 
   	break;
   case "ajout2":
      if ((!isset($titre))||($titre == ''))
      {
	      ?>
		    <p><strong>Vous devez spécifier un titre !</strong></p>
		    <?php 
	       $erreur = 1;
      }
      if ((!isset($contenu))||($contenu == ''))
      {
	      ?>
	      <p><strong>Vous devez spécifier un contenu !</strong></p>
	      <?php 
        $erreur = 1;
      }
      if ($erreur == 0)
      {
	      $contenu = htmlspecialchars($contenu);
	      //$contenu = nl2br($contenu);
	      $contenu = str_replace(";",chr(127),$contenu);
	      $titre = str_replace(";",chr(127),$titre);
	      $contenu = pg_escape_string($contenu);
	      $titre = pg_escape_string($titre);
	      $req_ins = "insert into journal (journal_perso_cod,journal_date,journal_titre,journal_texte)
	      												values ($perso_cod,now(),e'$titre',e'$contenu') ";
	      $db->query($req_ins);
	      	?>
	        <p><strong>La nouvelle entrée dans le journal est enregistrée !</strong></p>
	        <?php 
      }
    break;
	case "modif1":
		?>
		<form name="journal" method="post">
		<input type="hidden" name="methode" value="modif2">
		<input type="hidden" name="t_frdr" value="<?php echo $t_frdr;?>">
		<input type="hidden" name="journal_cod" value="<?php  echo $journal_cod; ?>">
		<table>
		<?php $req_journal = "select journal_titre,journal_perso_cod,to_char(journal_date,'dd/mm/yyyy hh24:mi:ss') as dj,journal_texte from journal
														where journal_cod = ".$journal_cod;
		$db->query($req_journal);
		$db->next_record();
		$perso = $db->f("journal_perso_cod");
		if ($perso != $perso_cod)
		{
			echo "Vous ne pouvez pas effacer cette entrée de journal !";
			break;
		}
		$texte = str_replace("<br />","\n",$db->f("journal_texte"));
		?>
		<tr><td class="soustitre2" colspan="2">
		<p><strong><?php echo $db->f("journal_titre"); ?></strong><br />
		Ecrit le <?php echo $db->f("dj"); ?></p></td></tr>
		
		<tr>
				<td>
					<input type="button" value="b" style="width:50px;font-weight:bold" onclick="storeCaret('b')">
					<input type="button" value="i" style="width:50px;font-style:italic" onclick="storeCaret('i')">
					<input type="button" value="u" style="width:50px;text-decoration:underline" onclick="storeCaret('u')">
					<input type="button" value="url" style="width:50px" onclick="storeCaret('url')">
					<input type="button" value="img" style="width:50px" onclick="storeCaret('img')"><br />
					<textarea name="contenu" id="contenu" rows="10" wrap="virtual" cols="45"><?php  echo $texte;?></textarea>
				</td>
<?php /*<td colspan="2"><textarea cols="30" rows="15" name="contenu"><? echo $texte;?></textarea></td>*/	?>
		</tr>
		<tr><td>
		<p style="text-align:center;">
		<input type="button" value="Valider" class="test" accesskey="s" onClick="voirList(this,'<?php echo $PHP_SELF;?>','vue_droite');">
		</td></tr>
		</table>
		</form>
		<?php 
		break;
	case "modif2":
      $erreur = 0;
      if ((!isset($contenu))||($contenu == ''))
      {
	?>
	<p><strong>Vous devez spécifier un contenu !</strong></p>
	<?php 
	     $erreur = 1;
      }
      if ($erreur == 0)
      {
	      $contenu = htmlspecialchars($contenu);
	      $contenu = str_replace(";",chr(127),$contenu);
	      $contenu = pg_escape_string($contenu);
	      $req_upd = "update journal set journal_date = now(), ";
	      $req_upd = $req_upd . "journal_texte = e'$contenu' ";
	      $req_upd = $req_upd . "where journal_cod = $journal_cod ";
	      $db->query($req_upd);
	      ?>
		    <p><strong>La modification est enregistrée !</strong></p>
		    <?php 
      }
    break;
}

?>
<a href="javascript:void(0);" onclick="getdata('<?php  echo $type_flux . G_URL; ?>jeu_test/fr_dr.php?t_frdr=<?php echo $t_frdr;?>','vue_droite');">Retour au sommaire</a>
