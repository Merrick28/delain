<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');
ob_start();

$is_log = 1;

?>
 <script src="../scripts/jquery.js"></script>
<script language="javascript">

 function AddNewVote()
 {
      var php_var = "<?php echo  $compt_cod; ?>".trim();
      var compte = "7014";
     
     if(php_var  == compte)
     {
        alert('récupération de l\'ip');
    }
    $.ajax({ 
    type: 'GET', 
    url: 'https://api.ipify.org?format=json', 
    data: { get_param: 'value' }, 
    success:function(json){
         if(php_var  == compte)
            {
               alert('ip : '+ json.ip);
           }
          $.ajax({
            type: 'post',
            url:'Add_vote.php',
            data: {IP: json.ip},
            success: function (data) {
                if(php_var  == compte)
                {
                    alert(data)
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                 if(php_var  == compte)
                {
                       alert(xhr.status);
                       alert(thrownError);
                   }
            }
          });    
      },
    error: function (xhr, ajaxOptions, thrownError) {
         if(php_var  == compte)
                {
               alert(xhr.status);
               alert(thrownError);
           }
    }
  });
 }
function montre(id){
	objet=document.getElementById(id);
	objet.style.display=(objet.style.display==""?"none":"");
}
</script>
<?php $admin = 'N';
if ($db->is_admin_monstre($compt_cod))
{
	$admin = 'O';
	$chemin = '.';
	include "switch_monstre.php";
}
if ($db->is_admin($compt_cod))
{
	include "switch_admin.php";
}
if ((!$db->is_admin($compt_cod)) && (!$db->is_admin_monstre($compt_cod)))
{
	$req_perso = "select autorise_4e_perso(compt_quatre_perso, compt_dcreat) as autorise, compte_nombre_perso(compt_cod) as nb, compt_quete ";
	$req_perso = $req_perso . " from compte where compt_cod = $compt_cod ";
	$db->query($req_perso);
	$db->next_record();
	$nb_perso = $db->f('nb');
	$compt_quatre_perso = ($db->f("autorise") == 't');
	$compt_quete = $db->f('compt_quete');
	if ($nb_perso == 0)
	{
		?>
		Aucun joueur dirigé.
		<?php 
	}
	else
	{
		?>
		<table border="0">
		<form name="login" method="post" action="../validation_login3.php">
		<input type="hidden" name="perso"/>
		<input type="hidden" name="change_perso"/>
		<input type="hidden" name="activeTout" value="0"/>
		<?php 
		echo("<input type=\"hidden\" name=\"compte\" value=\"$compt_cod\"/>");
		include "../tab_switch.php";
		?>
		<tr>
	
	<td colspan="2">
	<?php // Bonus XP pour les 10 ans du jeu (Maverick)
	if ((int)date('Y') == 2014 && (int)date('m') < 2) {
	  echo '<hr /><p style="text-align:center;"><a href="repart_xp_compte.php" style="font-size:14px;">Cadeau pour les 10 ans du jeu !</a></p>';
	}
	?>
	<hr /><div style="text-align:center">
	Numéro de compte : <?php echo  $compt_cod; ?><br /><a href="change_pass.php">Changer de mot de passe !</a><br />
	<a href="change_mail.php">Changer d’adresse e-mail !</a><br />
	<a href="options_clef_forum.php">Demander une clef d’accès au forum</a><br />
	<a href="rec_mail.php">Réception des comptes rendus par mail</a><br />
	<a href="declare_sitting.php">Déclarer un sitting</a><br />
	<?php 
	$req = "select * from auth.demande_temp
		where dtemp_compt_cod = " . $compt_cod . "
		and not dtemp_valide";
	$db->query($req);
	if($db->nf() != 0)
	{
		echo '<a href="gestion_api.php">Gestion des programmes externes</a><br />';
	}
	
	echo "<a href=\"../suppr_perso.php?compt_cod=$compt_cod\">Supprimer un perso ! </A><br>";
	?>
    <a href="hibernation.php">Mettre ses persos en hibernation</a><br>
    <?php     if ($compt_quatre_perso)
        echo '<a href="options_quatrieme_perso.php">Paramétrer son 4e personnage</a><br>';

	if ($compt_quete == 'O')
	{
		echo "<a href=\"admin_quete.php\">Aller dans les options admins quêtes</a>";
		$erreur = 1;
	}
	$time = rand(1,100);
	?>
	
	<?php 
	//if(($_SERVER["REMOTE_ADDR"] == '217.109.103.1') || ($_SERVER["REMOTE_ADDR"] == '2a01:e35:2f10:3150:21e:64ff:fe41:d2c8') || ($_SERVER["REMOTE_ADDR"] == '82.241.3.21'))
	//{
		echo '<div style="text-align:center">
		<a href="autres_auth.php">Autres authentifications</a>
		</div>';
	//}
	?></td>
        <td colspan="2">
                    <table>
                        <tr>
                            <td >
                                <b>Je vote pour delain!</b>
                            </td>
                            <td>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="http://www.jeux-alternatifs.com/Les-Souterrains-de-Delain-jeu715_hit-parade_1_1.html" onclick="AddNewVote()" target="_blank"><img src="http://www.jeux-alternatifs.com/im/bandeau/hitP_88x31_v1.gif" border="0" /></a>
                            </td>
                        </tr>
                        <tr>
                             <td>
                               Total XP/Perso : <?php echo $totalXpGagne ?>
                               
                            </td>
                        </tr>
                        <tr>
                             <td>

                                Nombres total de votes: <?php echo $nbrVote ?>
                            </td>
                        </tr>
                        <tr>
                             <td>

                                Nombres de votes du mois: <?php echo $nbrVoteMois ?>
                            </td>
                        </tr>
                        <tr>
                             <td>
                               Votes non validés du mois : <?php echo $votesRefusee ?>
                            </td>
                        </tr>
                        <tr>
                             <td>
                               Votes à valider : <?php echo $VoteAValider ?>
                            </td>
                        </tr>
                        
                        
                        
                    </table>
                </td>
	<?php  if ($time < 20)
	{ ?>

	<td colspan="2">
          
                  </td>
		<?php }?>
                
	</tr>
      
	</table>
	</form>

	<?php
echo "<div style=\"text-align:center;\"><br /><i>Date et heure serveur : " . date('d/m/Y H:i:s') . "</i></div>";
	}
}


$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
?>

