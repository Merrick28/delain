<?php // gestion des quêtes sur les banques.

if(!defined("APPEL"))
	die("Erreur d’appel de page !");

$methode2          = get_request_var('methode2', 'debut');

$perso        = new perso;
$perso->charge($perso_cod);

switch($methode2)
{
	case "debut":
		// gobj_cod = 380 <=> caisses de minerais
		//Quête du forgeron Trelmar Mogresh ayant perdu ses caisses de minerais volées par des brigands
		$req = "select distinct obj_gobj_cod, perobj_obj_cod
			from objets, perso_objets
			where perobj_obj_cod = obj_cod
				and perobj_perso_cod = $perso_cod 
				and perobj_identifie = 'O' 
				and obj_gobj_cod in ('380')
			order by obj_gobj_cod ";
		$stmt = $pdo->query($req);
		while($result = $stmt->fetch())
		{
			$obj_gen_quete = $result['obj_gobj_cod'];
			$obj_quete = $result['perobj_obj_cod'];
			if ($obj_gen_quete == 380)
			{
				$nb_caisses = $perso->compte_objet(380);
				?>
				<form name="cede" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
					<p><em>Vous voilà bien chargé. Souhaitez vous faire un dépôt ?
							<br>Ou alors peut-être souhaiteriez vous vous délester de ces lourdes caisses ?
							(Vous êtes en possession de <?php echo $nb_caisses; ?> caisses)
							<br>Dans ce cas, posez donc ces caisses dans ce coin, nous transmettrons cet échange à leur
							propriétaire.
							<br>Il nous a chargé de vous remettre quelques babioles en récompense de votre effort.</em>
						<input type="hidden" name="methode2" value="cede_objet1">
						<table>
							<tr>
								<td class="soustitre2">
					<p>Poser les caisses à la banque</p></td>
					<td><input type="radio" class="vide" name="controle1" value="cede_objet1"></td>
						</tr>
						<tr>
							<td class="soustitre2"><p>Non, finalement, je vais les garder.
							Je pourrais en retirer sûrement plus de brouzoufs plus tard...</td>
							<td><input type="radio" class="vide" name="controle1" value="non"></td>
						</tr>
					</table>
					<input type="hidden" class="vide" name="obj_gen_quete" value="<?php echo $obj_gen_quete;?>">
					<input type="hidden" class="vide" name="nb_caisses" value="<?php echo $nb_caisses;?>">
					<input type="submit" class="test" value="Valider !">
				</form>
			<?php 			}
		}
	break;

	//Résultat Quête du forgeron Trelmar Mogresh
	case "cede_objet1":
		$cede_objet = $_POST['controle1'];
		if($cede_objet == 'cede_objet1')
		{
		?>
			<br>Nous vous remercions.
			<br>Votre dépôt a été signalé au propriétaire de ces objets.
			Une dépêche est partie de votre part (<em>message visible dans votre boîte d’envoi</em>)
			<br>Nous avons pris la liberté de vous fournir directement la récompense (<em>visible dans votre inventaire</em>).
		<?php 			$req = "select vente_caisses($perso_cod, $obj_gen_quete)";
			$stmt = $pdo->query($req);

			// Envoi de message
			$msg = new message;
			$msg->corps = "Livraison effectuée - STOP -
				transmettre propriétaire - STOP -
				Cargaison bon état - STOP -
				$nb_caisses caisses livrées - STOP -
				Fin Transmission - STOP -";
			$msg->sujet = 'Livraison caisses';
			$msg->expediteur = $perso_cod;
			$msg->ajouteDestinataire(200017);
			$msg->envoieMessage();
		}
		else if($cede_objet == 'non')
		{
		?>
			<br>Quelle étrange attitude de s’encombrer de tels objets...
			<br>Vous changerez peut-être d’idée plus tard...
		<?php 		}
		else	// on gère le cas où il y a risque de triche
		{
			echo '<br>Pensez vous que ce soit bien malin de tenter de tricher ?';
		}
	break;
}
?>