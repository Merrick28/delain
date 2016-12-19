<html>
<!-- Date de création: 06/02/2003 -->
<head>
<script language="JavaScript">
  <!-- 
  function verifform()
  {
  	if(document.formuperso1.mail.value == "")
    {
    	alert("Veuillez entrer votre adresse e-mail !\n");
    	document.formuperso1.mail.focus();
    	return false;
    }
    if(document.formuperso1.nom.value == "")
    {
    	alert("Veuillez rentrer un nom de compte\n");
    	document.formuperso1.nom.focus();
    	return false;
    }

   	if(document.formuperso1.pass1.value == "")
    {
    	alert("Veuillez entrer votre mot de passe !\n");
    	document.formuperso1.pass1.focus();
    	return false;
    }
  	if(document.formuperso1.pass2.value == "")
    {
    	alert("Veuillez confirmer votre mot de passe !\n");
    	document.formuperso1.pass2.focus();
    	return false;
    }
	if(document.formuperso1.pass1.value != document.formuperso1.pass2.value)
	{
		alert("La confirmation du mot de passe a échoué !\n");
		document.formuperso1.pass1.value = "";
		document.formuperso1.pass2.value = "";
		document.formuperso1.pass1.focus();
		return false;
	}

  }
    //-->
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="author" content="a">
<meta name="generator" content="WebExpert 5">
<link rel="stylesheet" type="text/css" href="style.css" title="essai">



</head>
<body background="images/fond5.gif">
<?php 
include "jeu_test/tab_haut.php";
?>
<p class="titre">Création d'un compte</p>


<FORM method="post" action="formu_cree_compte2.php" name="formuperso1" OnSubmit="return verifform()">

<center><table width="80%" bgcolor="#EBE7E7" border="0" cellpadding="1" cellspacing="1">
<tr>
<td><p>Adresse e-mail : </p></td>
<td colspan="2"><input type="text" name="mail" size="40" maxlength="50"></td>
</tr>

<tr>
<td><p>Nom du compte : </p></td>
<td><input type="text" name="nom" size="40" maxlength="25"></td><td class="soustitre2"><p>Attention ! Ne mettez pas de caratères exotiques (espaces, apostrophes, etc...) dans votre nom de compte, sinon la validation risque d'échouer.</td>
</tr>

<tr>
<td><p>Mot de passe : </p></td>
<td><input type="password" name="pass1" size="40" maxlength="25"></td><td class="soustitre2"><p>Attention ! Merci de ne pas utiliser de point virgule dans le mot de passe !</td>
</tr>

<tr>
<td><p>Confirmer le mot de passe : </p></td>
<td colspan="2"><input type="password" name="pass2" size="40" maxlength="25"></td>
</tr>

<tr>
<td colspan="3"><p>
<center><IFRAME name="charte des joueurs" SRC="http://www.jdr-delain.net/charte.php" border=0 frameborder=0 height=350 width="80%"></IFRAME></center><br>
<input type="checkbox" class="vide" name="regles" value="1"> Je certifie avoir lu la <a href="charte.php" target="_blank">charte des joueurs</a> et l'accepter.</td>
</tr>



<tr><td><center><input type="reset" class="test" value="Tout effacer !"></td>
<td colspan="2"><input type="submit" class="test" value="Valider et continuer"></center></td>
</tr>

</table></center>

<?php 
include "jeu_test/tab_bas.php";
?>


</body>
</html>
