<?php
include "blocks/_header_page_jeu.php";
ob_start();

$is_log = 1;


//
// gestion des vote
//
$cv           = new compte_vote();
$votes        = $cv->getStats($compt_cod);

$totalXpGagne = $votes['totalXpGagne'];
$nbrVote      = $votes['nbrVote'];
$nbrVoteMois  = $votes['nbrVoteMois'];
$VoteAValider = $votes['VoteAValider'];
$votesRefusee = $votes['votesRefusee'];


?>
    <!--script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script-->
    <script language="javascript">//# sourceURL=switch.php.js

        function AddNewVote() {
            var php_var = "<?php echo $compt_cod; ?>".trim();
            var compte = "21703";

            if (php_var == compte) {
                alert('récupération de l\'ip');
            }

            // On essaye d'abord avec https://ipinfo.io/json
            $.ajax({
                type: 'GET',
                url: 'https://ipinfo.io/json',
                success: function (json) {
                    console.log('ip : ' + json.ip);
                    $.ajax({
                        type: 'post',
                        url: 'Add_vote.php',
                        data: {IP: json.ip},
                        success: function (data) {
                            console.log(data);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            console.log(xhr.status);
                            console.log(thrownError);
                        }
                    });
                },

                // si https://ipinfo.io/json ne marche pas, autre tentative avec https://api.ipify.org
                error: function (xhr, ajaxOptions, thrownError) {
                    $.ajax({
                        type: 'GET',
                        url: 'https://api.ipify.org?format=json',
                        data: {get_param: 'value'},
                        success: function (json) {
                            if (php_var == compte) {
                                alert('ip : ' + json.ip);
                            }
                            $.ajax({
                                type: 'post',
                                url: 'Add_vote.php',
                                data: {IP: json.ip},
                                success: function (data) {
                                    console.log(data)
                                },
                                error: function (xhr, ajaxOptions, thrownError) {
                                    console.log(xhr.status);
                                    console.log(thrownError);
                                }
                            });
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            console.log(xhr.status);
                            console.log(thrownError);
                        }
                    });
                }
            });

        }

        function montre(id) {
            objet = document.getElementById(id);
            objet.style.display = (objet.style.display == "" ? "none" : "");
        }
    </script>
<?php
$admin = 'N';

$compte = new compte;
$compte = $verif_connexion->compte;

if ($compte->is_admin_monstre())
{
    $admin  = 'O';
    $chemin = '.';
    include "switch_monstre.php";
}
if ($compte->is_admin())
{
    include "switch_admin.php";
}
if ((!$compte->is_admin()) && (!$compte->is_admin_monstre()))
{
    $req_perso =
        "select autorise_4e_perso(compt_quatre_perso, compt_dcreat) as autorise, compte_nombre_perso(compt_cod) as nb, compt_quete ";
    $req_perso = $req_perso . " from compte where compt_cod = $compt_cod ";
    $stmt = $pdo->query($req_perso);
    $result = $stmt->fetch();
    $nb_perso           = $result['nb'];
    $compt_quatre_perso = ($result['autorise'] == 't');
    $compt_quete        = $result['compt_quete'];
    if ($nb_perso == 0)
    {
        ?>
        Aucun joueur dirigé.
        <?php
    } else
    {
        ?>
        <!--table border="0"-->
        <form name="login" method="post" action="../validation_login3.php">
        <input type="hidden" name="perso"/>
        <input type="hidden" name="change_perso"/>
        <input type="hidden" name="activeTout" value="0"/>
        <?php
        echo("<input type=\"hidden\" name=\"compte\" value=\"$compt_cod\"/>");

        echo '<div class="container-fluid">';
        $benchmark = $profiler->start('Debut tab switch');
        include "../tab_switch.php";
        $benchmark->stop();
        echo '</div>';
        echo '</form>';

        ?>
        <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 col-sm-6 col-xs-12 centrer">
                <hr/>
                <div style="centrer">
                    Numéro de compte : <?php echo $compt_cod; ?><br/><a href="change_pass.php">Changer de mot de
                        passe !</a><br/>
                    <a href="change_mail.php">Changer d’adresse e-mail !</a><br/>
                    <a href="rec_mail.php">Réception des comptes rendus par mail</a><br/>
                    <a href="declare_sitting.php">Déclarer un sitting</a><br/>
                    <?php


                    echo "<a href=\"../suppr_perso.php?compt_cod=$compt_cod\">Supprimer un perso ! </A><br>";
                    ?>
                    <a href="hibernation.php">Mettre ses persos en hibernation</a><br>
                    <?php if ($compt_quatre_perso)
                    {
                        echo '<a href="options_quatrieme_perso.php">Paramétrer son 4e personnage</a><br>';
                    }

                    if ($compt_quete == 'O')
                    {
                        echo "<a href=\"admin_quete.php\">Aller dans les options admins quêtes</a>";
                        $erreur = 1;
                    }
                    $time = rand(1, 100);
                    ?>

                </div>
                <div class="col-md-4 col-sm-6 col-xs-12 centrer">
                    <table>
                        <tr>
                            <td>
                                <strong>Je vote pour delain!</strong>
                            </td>
                            <td>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="http://www.jeux-alternatifs.com/Les-Souterrains-de-Delain-jeu715_hit-parade_1_1.html"
                                   onclick="AddNewVote()" target="_blank" rel="noopener noreferrer"><img
                                            src="https://www.jeux-alternatifs.com/im/bandeau/hitP_88x31_v1.gif"
                                            border="0" alt="Jeux alternatifs"/></a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Total XP/Perso : <?php echo $totalXpGagne ?>

                            </td>
                        </tr>
                        <tr>
                            <td>

                                Total de votes pour delain: <?php echo $nbrVote ?>
                            </td>
                        </tr>
                        <tr>
                            <td>

                                Votes pour delain du mois: <?php echo $nbrVoteMois ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Votes refusés du mois : <?php echo $votesRefusee ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Votes en attente validation : <?php echo $VoteAValider ?>
                            </td>
                        </tr>


                    </table>
                </div>
            </div>
        </div>


        <script>//# sourceURL=_switch.php.js
            $(document).ready(function () {
                $(".delain-tooltip").tooltip({
                    content: function () {
                        return $(this).prop('title');
                    }});
            });
        </script>

        <?php
        echo "<div class='centrer'><br /><em>Date et heure serveur : " . date('d/m/Y H:i:s') . "</em></div>";
    }
}


$barre_switch_rapide =
    '<div id="colonne0-hide"><div class="container-fluid" ><div class="row centrer"></div></div></div>';


$contenu_page = ob_get_contents();
ob_end_clean();
//include "blocks/_footer_page_jeu.php";

include "variables_menu.php";


$template     = $twig->load('switch.twig');
$options_twig = array(
    'CONTENU_PAGE' => $contenu_page
);
echo $template->render(array_merge($var_twig_defaut, $options_twig_defaut, $options_twig));
