<?php 
if(!defined("APPEL"))
	die("Erreur d'appel de page !");
if(!isset($db))
	include_once "verif_connexion.php";

// on regarde si le joueur est bien sur un pavage
$erreur = 0;
$db = new base_delain;
if (!$db->is_lieu($perso_cod))
{
	echo("<p>Erreur ! Vous n'êtes pas sur une dalle magique !!!");
	$erreur = 1;
}
if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	if ($tab_lieu['type_lieu'] != 24)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n'êtes pas sur une dalle magique !!!");
	}
}
if ($erreur == 0)
{
	?>
	<p><strong>
	Ces dalles ressemblent aux autres de prime abord malgré des couleurs changeantes. Pourtant, un observateur un tant soit peu versé dans les arts magiques peut constater la puissance de flux magiques entremêlés à la surface des pavés.<br>
		D'ailleurs, à y regarder de plus près, il constatera que ce n'est pas de la magie, mais bien plutôt de l'anti-magie. A croire que certains sortilèges pourraient être bloqués ... Cela serait-il une réponse apportée par les Shamans morbelins pour maitriser cet art ?
<br><br>

<?php 
}
?>
