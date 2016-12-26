<?php
include "classes.php";
include G_CHE . 'includes/classes.php'?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.php">
<link rel="shortcut icon" href="http://www.jdr-delain.net/drake_head_red.gif" type="image/gif">
</head>
<body>
<?php include 'jeu_test/tableau.php';
Titre('FAQ')?>
<div class="barrLbord"><div class="barrRbord">
<p class="texteNorm">
<a name="haut"></a>
<?php 
$db=new base_delain;
$db_faq=new base_delain;
$db->query("select tfaq_cod,tfaq_libelle from faq_type order by tfaq_cod");
while($db->next_record())
{
	echo '<br><b>'.$db->f("tfaq_libelle").'</b><br>';
	$tfaq_cod=$db->f("tfaq_cod");
	$db_faq->query("select faq_cod,faq_question,faq_reponse from faq where faq_tfaq_cod=".$tfaq_cod." order by faq_cod");
	while($db_faq->next_record())
		echo '<a href="#'.$db_faq->f("faq_cod").'">'.$db_faq->f("faq_question").'</a><br>';
}
?>
</p>
</div></div>
<?php Bordure_Tab()?>
</div>
<?php 
$db->query("select tfaq_cod,tfaq_libelle from faq_type order by tfaq_cod");
while($db->next_record())
{
	$tfaq_cod=$db->f("tfaq_cod");
	Titre($db->f("tfaq_libelle"));
?>
<div class="barrLbord"><div class="barrRbord">
<p class="texteNorm">
<?php 
	$db_faq->query("select faq_cod,faq_question,faq_reponse from faq where faq_tfaq_cod=".$tfaq_cod." order by faq_cod");
	while($db_faq->next_record())
		echo '<b><a name="'.$db_faq->f("faq_cod").'">'.$db_faq->f("faq_question").'</a></b><br>'.$db_faq->f("faq_reponse").'<br><a href="#haut">Haut de page</a><br><br>';
?>
</p>
</div></div>
<?php Bordure_Tab()?>
</div>
<?php }?>
</body>
</html>
