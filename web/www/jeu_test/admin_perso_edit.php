<?php
include "blocks/_header_page_jeu.php";
ob_start();
$pdo = new bddpdo;
?>
    <title>ÉDITION D’UN PERSO / MONSTRE</title>
    <SCRIPT language="javascript" src="../scripts/controlUtils.js"></SCRIPT>
    <script language="javascript" src="../scripts/validation.js"></script>
    <script language="javascript" src="../scripts/manip_css.js"></script>
    <script language="javascript" src="../scripts/admin_effets_auto.js?20191130"></script>
<?php
$erreur = 0;


$droit_modif = 'dcompt_modif_perso';
include "blocks/_test_droit_modif_generique.php";

if ($erreur == 0)
{
    include "admin_edition_header.php";
    include 'sadmin.php';

    if (!isset($mod_perso_cod))
        $mod_perso_cod = '';
    ?>

    <p id="p_haut">RECHERCHE</p>
    <TABLE width="80%" align="center">
        <TR>
            <TD>
                <form name="login2" method="post">
                    Numéro du perso<input type="text" id="mod_perso_cod" name="mod_perso_cod"
                                          value="<?php echo $mod_perso_cod ?>">
                    Tapez un nom de perso pour trouver son numéro :
                    <input type="text" name="foo" id="foo" value=""
                           onkeyup="loadData();document.getElementById('zoneResultats').style.visibility = 'hidden'"/>
                    <ul id="zoneResultats" style="visibility: hidden;"></ul>
                    <input type="submit" value="Valider">
                </form>
            </TD>
        </TR>
    </TABLE>

    <hr/>
    Sauter vers... <a href="#p_perso">PERSO</a> - <a href="#p_comp">COMPÉTENCES</a> - <a href="#p_bonmal">BONUS /
    MALUS</a> - <a href="#p_inv">INVENTAIRE</a> - <a href="#p_sort">SORTS</a>
    - <a href="#p_effet">EFFETS AUTO</a> - <a href="#p_fam">FAMILIER</a> - <a href="#p_rel">RELIGION</a> - <a
        href="#p_titre">TITRES</a> - <a href="#p_pos">POSITION</a>
    <hr/>

    <p id='p_perso'>PERSO</p>

    <?php
    // TRAITEMENT DU FORMULAIRE

    if (isset($_POST['methode']))
    {
        define('APPEL', 1);
        include "admin_traitement_perso_edit.php";
    }

    if (isset($_POST['mod_perso_cod']))
    {

        // affichage des attributs principaux
        $req_perso =
            "select perso_nom,perso_for,perso_dex,perso_int,perso_con,perso_sex,perso_race_cod,perso_pv,perso_pv_max"
            . ",perso_amelioration_degats,perso_amel_deg_dex,perso_amelioration_armure,perso_amelioration_vue"
            . ",to_char(perso_dcreat,'DD/MM/YYYY hh24:mi:ss') as date_creation,to_char(perso_der_connex,'DD/MM/YYYY hh24:mi:ss') as date_derniere_connexion,to_char(perso_dlt,'DD/MM/YYYY hh24:mi:ss') as dlt"
            . ",perso_temps_tour,perso_pa,perso_vue,	perso_des_regen,perso_valeur_regen,perso_po,perso_nb_esquive,perso_niveau,perso_type_perso,perso_px"
            . ",perso_tangible,perso_nb_tour_intangible,perso_enc_max,perso_amelioration_nb_sort,perso_capa_repar,coalesce(perso_nb_amel_repar,0) as perso_nb_amel_repar,perso_nb_receptacle,perso_nb_amel_chance_memo"
            . ",perso_nb_mort,perso_nb_monstre_tue,perso_nb_joueur_tue,perso_renommee_magie,perso_kharma,perso_renommee, perso_taille"
            . ",perso_nb_des_degats, perso_val_des_degats, perso_nb_amel_comp, perso_actif, coalesce(perso_prestige, 0) as perso_prestige, perso_pnj, perso_effets_auto"
            . ",perso_voie_magique"
            . " from perso where perso_cod = $mod_perso_cod";

        //echo "QUERY = ".$req_perso;


        $stmt   = $pdo->query($req_perso);
        $result = $stmt->fetch();

        $perso_nom = $result['perso_nom'];
        $perso_for = $result['perso_for'];
        $perso_dex = $result['perso_dex'];
        $perso_int = $result['perso_int'];
        $perso_con = $result['perso_con'];

        $perso_sex      = $result['perso_sex'];
        $perso_race_cod = $result['perso_race_cod'];

        $perso_pv     = $result['perso_pv'];
        $perso_pv_max = $result['perso_pv_max'];

        $perso_amelioration_degats = $result['perso_amelioration_degats'];
        $perso_amel_deg_dex        = $result['perso_amel_deg_dex'];
        $perso_amelioration_armure = $result['perso_amelioration_armure'];
        $perso_amelioration_vue    = $result['perso_amelioration_vue'];

        $perso_temps_tour   = $result['perso_temps_tour'];
        $perso_pa           = $result['perso_pa'];
        $perso_des_regen    = $result['perso_des_regen'];
        $perso_valeur_regen = $result['perso_valeur_regen'];
        $perso_vue          = $result['perso_vue'];
        $perso_po           = $result['perso_po'];
        $perso_nb_esquive   = $result['perso_nb_esquive'];
        $perso_niveau       = $result['perso_niveau'];
        $perso_type_perso   = $result['perso_type_perso'];
        $perso_px           = $result['perso_px'];
        $perso_taille       = $result['perso_taille'];

        $perso_tangible             = $result['perso_tangible'];
        $perso_nb_tour_intangible   = $result['perso_nb_tour_intangible'];
        $perso_enc_max              = $result['perso_enc_max'];
        $perso_amelioration_nb_sort = $result['perso_amelioration_nb_sort'];
        $perso_capa_repar           = $result['perso_capa_repar'];
        $perso_nb_amel_repar        = $result['perso_nb_amel_repar'];
        $perso_nb_receptacle        = $result['perso_nb_receptacle'];
        $perso_nb_amel_chance_memo  = $result['perso_nb_amel_chance_memo'];

        $perso_nb_mort        = $result['perso_nb_mort'];
        $perso_nb_monstre_tue = $result['perso_nb_monstre_tue'];
        $perso_nb_joueur_tue  = $result['perso_nb_joueur_tue'];
        $perso_renommee_magie = $result['perso_renommee_magie'];
        $perso_kharma         = $result['perso_kharma'];
        $perso_renommee       = $result['perso_renommee'];

        $perso_nb_des_degats  = $result['perso_nb_des_degats'];
        $perso_val_des_degats = $result['perso_val_des_degats'];
        $perso_nb_amel_comp   = $result['perso_nb_amel_comp'];
        $perso_actif          = $result['perso_actif'];
        $perso_prestige       = $result['perso_prestige'];
        $perso_pnj            = $result['perso_pnj'];
        $perso_effets_auto    = $result['perso_effets_auto'];
        $perso_voie_magique   = $result['perso_voie_magique'];

        ?>

        <br>
        <form method="post" action="#">
            <input type="hidden" name="methode" value="update_perso">
            <input type="hidden" name="mod_perso_cod" value="<?php echo $mod_perso_cod ?>">

            <TABLE width="80%" align="center">
                <TR>
                    <TD colspan="4">Perso n°<?php echo $mod_perso_cod ?> Nom : <input type="text" name="mod_perso_nom"
                                                                                      value="<?php echo $perso_nom ?>"><BR>
                        Date de création : <?php echo $result['date_creation']; ?><BR>
                        Date de dernière connexion : <?php echo $result['date_derniere_connexion']; ?><BR>
                        DLT : <?php echo $result['dlt']; ?></TD>
                </TR>
                <TR>
                    <TH width="25%">CHAMP</TH>
                    <TH width="25%">VALEUR</TH>
                    <TH width="25%">CHAMP</TH>
                    <TH width="25%">VALEUR</TH>
                </TR>
                <TR>
                    <TD class="soustitre2">Force</TD>
                    <TD><INPUT type="text" name="perso_for" value="<?php echo $perso_for ?>"></TD>
                    <TD class="soustitre2">Amélioration dégâts c-à-c</TD>
                    <TD><INPUT type="text" name="perso_amelioration_degats"
                               value="<?php echo $perso_amelioration_degats ?>"></TD>
                </TR>
                <TR>
                    <TD class="soustitre2">Dextérite</TD>
                    <TD><INPUT type="text" name="perso_dex" value="<?php echo $perso_dex ?>"></TD>
                    <TD class="soustitre2">Amélioration dégâts Dist</TD>
                    <TD><INPUT type="text" name="perso_amel_deg_dex" value="<?php echo $perso_amel_deg_dex ?>"></TD>
                </TR>
                <TR>
                    <TD class="soustitre2">Intelligence</TD>
                    <TD><INPUT type="text" name="perso_int" value="<?php echo $perso_int ?>"></TD>
                    <TD class="soustitre2">Amélioration Armure</TD>
                    <TD><INPUT type="text" name="perso_amelioration_armure"
                               value="<?php echo $perso_amelioration_armure ?>"></TD>
                </TR>
                <TR>
                    <TD class="soustitre2">Constitution</TD>
                    <TD><INPUT type="text" name="perso_con" value="<?php echo $perso_con ?>"></TD>
                    <TD class="soustitre2">Amélioration vue</TD>
                    <TD><INPUT type="text" name="perso_amelioration_vue" value="<?php echo $perso_amelioration_vue ?>">
                    </TD>
                </TR>
                <TR>
                    <TD class="soustitre2">Encombrement maximal</TD>
                    <TD><INPUT type="text" name="perso_enc_max" value="<?php echo $perso_enc_max ?>"></TD>
                    <TD class="soustitre2">Amélioration nombre de sorts</TD>
                    <TD><INPUT type="text" name="perso_amelioration_nb_sort"
                               value="<?php echo $perso_amelioration_nb_sort ?>"></TD>
                </TR>
                <TR>
                    <TD class="soustitre2">Capacité de réparation</TD>
                    <TD><INPUT type="text" name="perso_capa_repar" value="<?php echo $perso_capa_repar ?>"></TD>
                    <TD class="soustitre2">Amélioration capacité de réparation</TD>
                    <TD><INPUT type="text" name="perso_nb_amel_repar" value="<?php echo $perso_nb_amel_repar ?>"></TD>
                </TR>
                <TR>
                    <TD class="soustitre2">Nombre de réceptacles</TD>
                    <TD><INPUT type="text" name="perso_nb_receptacle" value="<?php echo $perso_nb_receptacle ?>"></TD>
                    <TD class="soustitre2">Amélioration chances de mémo</TD>
                    <TD><INPUT type="text" name="perso_nb_amel_chance_memo"
                               value="<?php echo $perso_nb_amel_chance_memo ?>"></TD>
                </TR>
                <TR>
                    <TD class="soustitre2" colspan="4">&nbsp;</TD>
                </TR>
                <TR>
                    <TD class="soustitre2">Nombre de morts</TD>
                    <TD><INPUT type="text" name="perso_nb_mort" value="<?php echo $perso_nb_mort ?>"></TD>
                    <TD class="soustitre2">Renommée</TD>
                    <TD><INPUT type="text" name="perso_renommee" value="<?php echo $perso_renommee ?>"></TD>
                </TR>
                <TR>
                    <TD class="soustitre2">Nombre de monstres tués</TD>
                    <TD><INPUT type="text" name="perso_nb_monstre_tue" value="<?php echo $perso_nb_monstre_tue ?>"></TD>
                    <TD class="soustitre2">Renommée Magique</TD>
                    <TD><INPUT type="text" name="perso_renommee_magie" value="<?php echo $perso_renommee_magie ?>"></TD>
                </TR>
                <TR>
                    <TD class="soustitre2">Nombre de joueurs tués</TD>
                    <TD><INPUT type="text" name="perso_nb_joueur_tue" value="<?php echo $perso_nb_joueur_tue ?>"></TD>
                    <TD class="soustitre2">Karma</TD>
                    <TD><INPUT type="text" name="perso_kharma" value="<?php echo $perso_kharma ?>"></TD>
                </TR>
                <TR>
                    <TD class="soustitre2">Actif (O ou N)</TD>
                    <TD><INPUT type="text" name="perso_actif" value="<?php echo $perso_actif ?>"></TD>
                    <TD class="soustitre2">Nombre de super-améliorations</TD>
                    <TD><INPUT type="text" name="perso_nb_amel_comp" value="<?php echo $perso_nb_amel_comp ?>"></TD>
                </TR>
                <TR>
                    <TD class="soustitre2" colspan="4">&nbsp;</TD>
                </TR>
                <TR>
                    <TD class="soustitre2">Sexe</TD>
                    <TD>
                        <SELECT name="perso_sex">
                            <OPTION value="M" <?php if ($perso_sex == "M")
                            {
                                echo "selected";
                            } ?>>Mâle
                            </OPTION>
                            <OPTION value="F" <?php if ($perso_sex == "F")
                            {
                                echo "selected";
                            } ?>>Femelle
                            </OPTION>
                            <OPTION value="A" <?php if ($perso_sex == "A")
                            {
                                echo "selected";
                            } ?>>Androgyne
                            </OPTION>
                            <OPTION value="H" <?php if ($perso_sex == "H")
                            {
                                echo "selected";
                            } ?>>Hermaphrodite
                            </OPTION>
                            <OPTION value="I" <?php if ($perso_sex == "I")
                            {
                                echo "selected";
                            } ?>>Inconnu
                            </OPTION>
                        </SELECT></TD>
                    <TD class="soustitre2">Race</TD>
                    <TD>
                        <SELECT name="perso_race_cod">
                            <?php // LISTE DES RACES
                            $req_races = "select race_cod,race_nom from race order by race_nom";
                            echo $html->select_from_query($req_races, 'race_cod', 'race_nom', $perso_race_cod);
                            ?>

                        </SELECT></TD>
                </TR>

                <TR>
                    <TD class="soustitre2">PV</TD>
                    <TD><INPUT type="text" name="perso_pv" value="<?php echo $perso_pv ?>"></TD>
                    <TD class="soustitre2">PV Max</TD>
                    <TD><INPUT type="text" name="perso_pv_max" value="<?php echo $perso_pv_max ?>"></TD>
                </TR>

                <TR>
                    <TD class="soustitre2">Temps de tour</TD>
                    <TD><INPUT type="text" name="perso_temps_tour" value="<?php echo $perso_temps_tour ?>"></TD>
                    <TD class="soustitre2">PA</TD>
                    <TD><INPUT type="text" name="perso_pa" value="<?php echo $perso_pa ?>"></TD>
                </TR>

                <TR>
                    <TD class="soustitre2">Dés de dégâts</TD>
                    <TD><INPUT type="text" name="perso_nb_des_degats" value="<?php echo $perso_nb_des_degats ?>"></TD>
                    <TD class="soustitre2">Valeur dégâts</TD>
                    <TD><INPUT type="text" name="perso_val_des_degats" value="<?php echo $perso_val_des_degats ?>"></TD>
                </TR>
                <TR>
                    <TD class="soustitre2">Dés de régen</TD>
                    <TD><INPUT type="text" name="perso_des_regen" value="<?php echo $perso_des_regen ?>"></TD>
                    <TD class="soustitre2">Valeur régen</TD>
                    <TD><INPUT type="text" name="perso_valeur_regen" value="<?php echo $perso_valeur_regen ?>"></TD>
                </TR>
                <TR>
                    <TD class="soustitre2">Vue</TD>
                    <TD><INPUT type="text" name="perso_vue" value="<?php echo $perso_vue ?>"></TD>
                    <TD class="soustitre2">Brouzoufs</TD>
                    <TD><INPUT type="text" name="perso_po" value="<?php echo $perso_po ?>"></TD>
                </TR>
                <TR>
                    <TD class="soustitre2">Nombre d’esquives</TD>
                    <TD><INPUT type="text" name="perso_nb_esquive" value="<?php echo $perso_nb_esquive ?>"></TD>
                    <TD class="soustitre2">Type de perso</TD>
                    <TD><INPUT type="text" name="perso_type_perso" value="<?php echo $perso_type_perso ?>"></TD>
                </TR>
                <TR>
                    <TD class="soustitre2">Niveau</TD>
                    <TD><INPUT type="text" name="perso_niveau" value="<?php echo $perso_niveau ?>"></TD>
                    <TD class="soustitre2">PX</TD>
                    <TD><INPUT type="text" name="perso_px" value="<?php echo $perso_px ?>"></TD>
                </TR>
                <TR>
                    <TD class="soustitre2">Tangible</TD>
                    <TD>
                        <select name="perso_tangible">
                            <option value="O" <?php if ($perso_tangible == "O")
                            {
                                echo "selected";
                            } ?>>Oui
                            </option>
                            <option value="N" <?php if ($perso_tangible == "N")
                            {
                                echo "selected";
                            } ?>>Non
                            </option>
                        </select>
                    </TD>
                    <TD class="soustitre2">Tours d’intangibilité</TD>
                    <TD><INPUT type="text" name="perso_nb_tour_intangible"
                               value="<?php echo $perso_nb_tour_intangible ?>"></TD>
                </TR>
                <TR>
                    <TD class="soustitre2">Points de prestige</TD>
                    <TD><INPUT type="text" name="perso_prestige" value="<?php echo $perso_prestige ?>"></TD>
                    <TD class="soustitre2">Perso PNJ (0 = joueur, 1 = PNJ, 2 = 4e perso)</TD>
                    <TD><INPUT type="text" name="perso_pnj" value="<?php echo $perso_pnj ?>"></TD>
                <tr>
                    <td class="soustitre2">Effets automatiques</td>
                    <td>
                        <select name="perso_effets_auto">
                            <option value="1" <?php if ($perso_effets_auto == 1)
                            {
                                echo "selected";
                            } ?>>Activés (Par défaut)
                            </option>
                            <option value="0" <?php if ($perso_effets_auto == 0)
                            {
                                echo "selected";
                            } ?>>Désactivés
                            </option>
                        </select>
                    </td>
                    <TD class="soustitre2">Taille</TD>
                    <TD><INPUT type="text" name="perso_taille" value="<?php echo $perso_taille ?>"></TD>
                </tr>
                <tr>
                    <td class="soustitre2">Voie magique</td>
                    <td>
                        <select name="perso_voie_magique">
                            <option value="0">Aucune</option>
                            <?php // LISTE DES VOIES MAGIQUES
                            $req_vm = "select mvoie_cod, mvoie_libelle from voie_magique order by mvoie_libelle";
                            echo $html->select_from_query($req_vm, 'mvoie_cod', 'mvoie_libelle', $perso_voie_magique);
                            ?>
                        </select></td>
                    <td class="soustitre2"></td>
                    <td class="soustitre2"></td>
                </tr>
                <TR>
                    <TD colspan="4" align="center"><input type="submit" value="Modifier le personnage"></TD>
                </TR>
            </TABLE>
        </form>
        <HR>
        <p id='p_comp'>COMPÉTENCES (<a href="#p_haut">Retour en haut</a>)</p>
        <TABLE width="80%" align="center">
            <form method="post" name="suppr_competence" action="#">
                <input type="hidden" name="methode" value="suppr_competence">
                <input type="hidden" name="mod_perso_cod" value="<?php echo $mod_perso_cod ?>">
                <input type="hidden" name="comp_cod" value="">
            </form>
            <form method="post" action="#">
                <input type="hidden" name="methode" value="update_competences">
                <input type="hidden" name="mod_perso_cod" value="<?php echo $mod_perso_cod ?>">
                <?php // LISTE DES COMPETENCES
                $req_comp = "select comp_cod,comp_libelle,pcomp_modificateur from perso_competences,competences ";
                $req_comp = $req_comp . "where pcomp_perso_cod = $mod_perso_cod ";
                //$req_comp = $req_comp . "and pcomp_modificateur != 0 ";
                $req_comp = $req_comp . "and pcomp_pcomp_cod = comp_cod ";
                $req_comp = $req_comp . "order by comp_libelle ";
                //ECHO $req_comp;
                $stmt = $pdo->query($req_comp);
                while ($result = $stmt->fetch())
                { ?>
                    <TR>
                        <TD class="soustitre2"><?php echo $result['comp_libelle']; ?></TD>
                        <TD><INPUT type="text" size="6" name="PERSO_COMP_<?php echo $result['comp_cod']; ?>"
                                   value="<?php echo $result['pcomp_modificateur'] ?>">
                            <a href="javascript:document.suppr_competence.comp_cod.value='<?php echo $result['comp_cod']; ?>';document.suppr_competence.submit();">Supprimer</a>
                        </TD>
                        <?php if ($result = $stmt->fetch()) { ?>
                            <TD class="soustitre2"><?php echo $result['comp_libelle']; ?></TD>
                            <TD><INPUT type="text" size="6" name="PERSO_COMP_<?php echo $result['comp_cod']; ?>"
                                       value="<?php echo $result['pcomp_modificateur'] ?>">
                                <a href="javascript:document.suppr_competence.comp_cod.value='<?php echo $result['comp_cod']; ?>';document.suppr_competence.submit();">Supprimer</a>
                            </TD>
                        <?php } ?>
                    </TR>
                <?php } ?>
                <TR>
                <TR>
                    <TD colspan="4" align="center"><input type="submit" value="Modifier les compétences"></TD>
                </TR>
            </form>
            <form method="post" action="#">
                <input type="hidden" name="methode" value="add_competence">
                <input type="hidden" name="mod_perso_cod" value="<?php echo $mod_perso_cod ?>">
                <TD>AJOUTER UNE COMPÉTENCE</TD>
                <TD><select name="comp_cod">
                        <OPTION value=""> --</OPTION>
                        <?php
                        // LISTE DES COMPETENCES NON APPRISES
                        $req_comp_manquante = "select comp_cod,comp_libelle from competences where ";
                        $req_comp_manquante .= "not exists (select 1 from perso_competences where pcomp_perso_cod = $mod_perso_cod and pcomp_pcomp_cod = comp_cod) ";
                        $req_comp_manquante .= "order by comp_libelle ";

                        echo $html->select_from_query($req_comp_manquante, 'comp_cod', 'comp_libelle');
                        ?>
                    </select>
                </TD>
                <TD><input type="text" name="comp_modificateur" size="6">%</TD>
                <TD><input type="submit" value="Ajouter"></TD>
                </TR>
            </form>
        </TABLE>
        <hr>
        <p id='p_bonmal'>BONUS / MALUS (<a href="#p_haut">Retour en haut</a>)</p>

        <TABLE width="80%" align="center">
            <form method="post" name="suppr_bonmal" action="#">
                <input type="hidden" name="methode" value="suppr_bonmal"/>
                <input type="hidden" name="mod_perso_cod" value="<?php echo $mod_perso_cod ?>"/>
                <input type="hidden" name="bonmal_cod" value=""/>
                <input type="hidden" name="type_bm" value="">
                <input type="hidden" name="bonmal_valeur_debut" value=""/>
            </form>
            <form method="post" action="#">
                <input type="hidden" name="methode" value="update_bonmal">
                <input type="hidden" name="mod_perso_cod" value="<?php echo $mod_perso_cod ?>">
                <tr>
                    <td class='titre' colspan='2'>Liste des bonus malus temporaires existants</td>
                </tr>
                <tr>
                    <td class='titre'>Bonus</td>
                    <td class='titre'>Malus</td>
                </tr>
                <tr>
                    <td width='50%'>
                        <table>
                            <?php // LISTE DES BONUS Standards
                            $req_bon = "select tonbus_libelle || CASE WHEN bonus_mode='C' THEN ' - [cumulatif]'  ELSE '' END as tonbus_libelle, bonus_cod, bonus_tbonus_libc, bonus_valeur, bonus_nb_tours
                                        from bonus
                                        inner join bonus_type on tbonus_libc = bonus_tbonus_libc
                                        where bonus_perso_cod = $mod_perso_cod and  bonus_mode!='E'
                                           and
                                            (tbonus_gentil_positif = 't' and bonus_valeur > 0
                                            or tbonus_gentil_positif = 'f' and bonus_valeur < 0)
                                        order by bonus_tbonus_libc";
                            $stmt    = $pdo->query($req_bon);
                            while ($result = $stmt->fetch())
                            {
                                $lib  = $result['tonbus_libelle'];
                                $val  = $result['bonus_valeur'];
                                $dur  = $result['bonus_nb_tours'];
                                $tbon = $result['bonus_cod'];
                                $id   = $tbon;
                                echo "<TR>
			<TD class='soustitre2'>$lib</TD>
			<TD><INPUT type='text' size='6' name='PERSO_BM_val_$id' value='$val' />
			pendant <INPUT type='text' size='6' name='PERSO_BM_dur_$id' value='$dur' /> tours.
			<a href=\"javascript:document.suppr_bonmal.bonmal_cod.value='$tbon';
				document.suppr_bonmal.bonmal_valeur_debut.value='$val';
				document.suppr_bonmal.type_bm.value='STD';
				document.suppr_bonmal.submit();\">Supprimer</a></TD>";
                                echo '</tr>';
                            }
                            ?>
                            <?php // LISTE DES BONUS de Caracs
                            $req_bon = "select tonbus_libelle || CASE WHEN corig_mode='C' THEN ' - [cumulatif]' ELSE '' END as tonbus_libelle, corig_cod, tbonus_libc as bonus_tbonus_libc,
                                          corig_valeur as bonus_valeur,
                                          COALESCE(corig_nb_tours::text, (DATE_PART('HOUR', corig_dfin-now())::text)||'h') as bonus_nb_tours
                                        from carac_orig
                                        inner join bonus_type on tbonus_libc = corig_type_carac
                                        inner join perso on perso_cod=corig_perso_cod
                                        where corig_perso_cod = $mod_perso_cod and corig_valeur >0 and  corig_mode!='E'
                                        order by tbonus_libc";
                            $stmt    = $pdo->query($req_bon);
                            while ($result = $stmt->fetch())
                            {
                                $lib  = $result['tonbus_libelle'];
                                $val  = $result['bonus_valeur'];
                                $dur  = $result['bonus_nb_tours'];
                                $tbon = $result['corig_cod'];
                                $id   = $tbon;
                                echo "<TR>
			<TD class='soustitre2'>$lib</TD>
			<TD><INPUT disabled type='text' size='6' name='PERSO_BM_val_$id' value='$val' />
			pendant <INPUT disabled type='text' size='6' name='PERSO_BM_dur_$id' value='$dur' /> tours.
			<a href=\"javascript:document.suppr_bonmal.bonmal_cod.value='$tbon';
				document.suppr_bonmal.bonmal_valeur_debut.value='$val';
				document.suppr_bonmal.type_bm.value='CARAC';
				document.suppr_bonmal.submit();\">Supprimer</a>(<em style='font-size: 8px;'>non-éditable, supprimez/recréez pour modifier<em>)</TD>";
                                echo '</tr>';
                            }
                            ?>
                        </table>
                    </td>
                    <td width='50%'>
                        <table>
                            <?php // LISTE DES MALUS Standards
                            $req_mal = "select tonbus_libelle || CASE WHEN bonus_mode='C' THEN ' - [cumulatif]'  ELSE '' END as tonbus_libelle, bonus_cod, bonus_tbonus_libc, bonus_valeur, bonus_nb_tours
		from bonus
		inner join bonus_type on tbonus_libc = bonus_tbonus_libc
		where bonus_perso_cod = $mod_perso_cod and  bonus_mode!='E'
		and
			(tbonus_gentil_positif = 't' and bonus_valeur < 0
			or tbonus_gentil_positif = 'f' and bonus_valeur > 0)
		order by bonus_tbonus_libc";
                            $stmt    = $pdo->query($req_mal);
                            while ($result = $stmt->fetch())
                            {
                                $lib  = $result['tonbus_libelle'];
                                $val  = $result['bonus_valeur'];
                                $dur  = $result['bonus_nb_tours'];
                                $tbon = $result['bonus_cod'];
                                $id   = $tbon;
                                echo "<TR>
			<TD class='soustitre2'>$lib</TD>
			<TD><INPUT type='text' size='6' name='PERSO_BM_val_$id' value='$val' />
			pendant <INPUT type='text' size='6' name='PERSO_BM_dur_$id' value='$dur' /> tours.
			<a href=\"javascript:document.suppr_bonmal.bonmal_cod.value='$tbon';
				document.suppr_bonmal.bonmal_valeur_debut.value='$val';
				document.suppr_bonmal.type_bm.value='STD';
				document.suppr_bonmal.submit();\">Supprimer</a></TD>";
                                echo '</tr>';
                            }
                            ?>
                            <?php // LISTE DES MALUS de Caracs
                            $req_bon = "select tonbus_libelle || CASE WHEN corig_mode='C' THEN ' - [cumulatif]'  ELSE '' END as tonbus_libelle, corig_cod, tbonus_libc as bonus_tbonus_libc,
                                          corig_valeur as bonus_valeur,
                                        COALESCE(corig_nb_tours::text, (DATE_PART('HOUR', corig_dfin-now())::text)||'h') as bonus_nb_tours
                                        from carac_orig
                                        inner join bonus_type on tbonus_libc = corig_type_carac
                                        inner join perso on perso_cod=corig_perso_cod
                                        where corig_perso_cod = $mod_perso_cod  and corig_valeur <0  and corig_mode!='E'
                                        order by tbonus_libc";
                            $stmt    = $pdo->query($req_bon);
                            while ($result = $stmt->fetch())
                            {
                                $lib  = $result['tonbus_libelle'];
                                $val  = $result['bonus_valeur'];
                                $dur  = $result['bonus_nb_tours'];
                                $tbon = $result['corig_cod'];
                                $id   = $tbon;
                                echo "<TR>
			<TD class='soustitre2'>$lib</TD>
			<TD><INPUT disabled type='text' size='6' name='PERSO_BM_val_$id' value='$val' />
			pendant <INPUT disabled type='text' size='6' name='PERSO_BM_dur_$id' value='$dur' /> tours.
			<a href=\"javascript:document.suppr_bonmal.bonmal_cod.value='$tbon';
				document.suppr_bonmal.bonmal_valeur_debut.value='$val';
				document.suppr_bonmal.type_bm.value='CARAC';
				document.suppr_bonmal.submit();\">Supprimer</a>(<em style='font-size: 8px;'>non-éditable, supprimez/recréez pour modifier<em>)</TD>";
                                echo '</tr>';
                            }
                            ?>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center"><input type="submit" value="Modifier les bonus / malus"/></td>
                </tr>
            </form>
        </table>
        <table width="80%" align="center">
            <form method="post" action="#">
                <input type="hidden" name="methode" value="add_bonmal">
                <input type="hidden" name="mod_perso_cod" value="<?php echo $mod_perso_cod ?>">
                <tr>
                    <td class='titre'>Ajouter un bonus malus</td>
                </tr>
                <tr>
                    <td>L’ajout se passe comme dans le jeu. Il n’est donc pas possible de cumuler des BM allant dans le
                        même sens (sauf pour les bonus/malus définis en cumulatif)
                    </td>
                </tr>
                <tr>
                    <td>
                        <script type='text/javascript'>
                            function change_bm(clef) {
                                var bonus = document.getElementById('div_aide_bonus');
                                var malus = document.getElementById('div_aide_malus');
                                var afficheBonus = (clef != '') && arr_bonmal[clef] == 'BON';
                                var afficheMalus = (clef != '') && arr_bonmal[clef] == 'MAL';

                                bonus.style.display = afficheBonus ? 'block' : 'none';
                                malus.style.display = afficheMalus ? 'block' : 'none';
                            }
                        </script>
                        Effet : <select name="bonmal_cod" onchange="change_bm(this.value);">
                            <option value=""> --</option>
                            <?php
                            // LISTE DES Bonus
                            $req_bm = "select tbonus_libc, tonbus_libelle || CASE WHEN tbonus_cumulable='O' THEN ' - [cumulable]' ELSE '' END as tonbus_libelle, tbonus_gentil_positif
		from bonus_type
		order by tonbus_libelle ";

                            echo $html->select_from_query($req_bm, 'tbonus_libc', 'tonbus_libelle');

                            // Écriture du JS qui dit si on a un bonus ou un malus
                            $stmt = $pdo->query($req_bm);
                            echo "</select><script type='text/javascript'>var arr_bonmal = new Array();\n";
                            while ($result = $stmt->fetch())
                            {
                                $clef   = $result['tbonus_libc'];
                                $valeur = ($result['tbonus_gentil_positif'] == 't') ? 'BON' : 'MAL';
                                echo "arr_bonmal['$clef'] = '$valeur';\n";
                            }
                            echo "</script>";
                            ?>
                            <div id='div_aide_bonus' style='display: none;'>Une valeur <strong>positive</strong> est
                                <strong>bénéfique</strong>, et une valeur <strong>négative</strong> est
                                <strong>délétère</strong></div>
                            <div id='div_aide_malus' style='display: none;'>Une valeur <strong>positive</strong> est
                                <strong>délétère</strong>, et une valeur <strong>négative</strong> est
                                <strong>bénéfique</strong></div>
                    </td>
                </tr>
                <tr>
                    <td>Puissance : <input type="text" name="bonmal_valeur" size='6'> pendant <input type="text"
                                                                                                     name="bonmal_duree"
                                                                                                     size='6'> tours,
                        cumulatif: <input type="checkbox" name="bonmal_cumul" size='6'> (<em>ignoré si le bonus/malus
                            n'est pas cumulable</em>)
                    </td>
                </tr>
                <tr>
                    <td><input type="submit" value="Ajouter"/></td>
                </tr>
            </form>
        </table>
        <hr>
        <p id='p_inv'>INVENTAIRE (objets normaux uniquement) (<a href="#p_haut">Retour en haut</a>)</p>
        <a href="admin_objet_edit.php?methode=perso&num_perso2=<?php echo $mod_perso_cod; ?>">Modifier les caracs de
            l’équipement</a><br>
        <!-- INITIALISATION DES VALEURS DES CONTROLS -->
        <SCRIPT language="javascript">
            var listeBase = new Array();
            <?php // LISTE DES OBJETS POSSIBLES
            $nb_tobj = 0;
            $req_tobj = "select gobj_cod, gobj_nom, tobj_libelle, gobj_valeur from objet_generique
			inner join type_objet on tobj_cod = gobj_tobj_cod
			order by tobj_libelle, gobj_nom";
            $stmt = $pdo->query($req_tobj);
            while ($result = $stmt->fetch())
            {
                $gobj_nom     = $result['gobj_nom'];
                $gobj_nom     = str_replace("\"", "", $gobj_nom);
                $tobj_libelle = str_replace("\"", "", $result['tobj_libelle']);
                $gobj_valeur  = $result['gobj_valeur'];
                echo("listeBase[$nb_tobj] = new Array(0); \n");
                echo("listeBase[$nb_tobj][0] = \"" . $result['gobj_cod'] . "\"; \n");
                echo("listeBase[$nb_tobj][1] = \"" . $gobj_nom . "\"; \n");
                echo("listeBase[$nb_tobj][2] = \"" . $tobj_libelle . "\"; \n");
                echo("listeBase[$nb_tobj][3] = \"" . $gobj_valeur . "\"; \n");
                $nb_tobj++;
            }
            ?>

            var listeCurrent = new Array();

            <?php
            // LISTE DES OBJETS DANS L’INVENTAIRE
            $req_inv = "select count(obj_cod) as nombre,gobj_cod,obj_nom ";
            $req_inv = $req_inv . "from perso_objets,objets,objet_generique ";
            $req_inv = $req_inv . "where perobj_perso_cod = $mod_perso_cod ";
            $req_inv = $req_inv . "and perobj_equipe <> 'O' ";
            $req_inv = $req_inv . "and perobj_obj_cod = obj_cod ";
            $req_inv = $req_inv . "and obj_gobj_cod = gobj_cod ";
            $req_inv = $req_inv . "and obj_nom = gobj_nom ";
            $req_inv = $req_inv . "group by gobj_cod,obj_nom ";
            $req_inv = $req_inv . "order by obj_nom ";
            $stmt = $pdo->query($req_inv);
            $nb_tobj = 0;
            while ($result = $stmt->fetch())
            {
                echo("listeCurrent[$nb_tobj] = new Array(0); \n");
                echo("listeCurrent[$nb_tobj][0] = \"" . $result['gobj_cod'] . "\"; \n");
                echo("listeCurrent[$nb_tobj][1] = \"" . $result['nombre'] . "\"; \n");
                echo("listeCurrent[$nb_tobj][2] = \"\"; \n");
                echo("listeCurrent[$nb_tobj][3] = \"\"; \n");
                $nb_tobj++;
            }

            ?>
        </SCRIPT>
        <form method="post" name="formInventaire" action="#">
            <input type="hidden" name="methode" value="update_inventaire">
            <input type="hidden" name="mod_perso_cod" value="<?php echo $mod_perso_cod ?>">
            <TABLE width="80%" align="center">
                <TR>
                    <TD>
                        <select multiple name="select1" size="10" style="width:280px;">
                        </select>
                    </TD>
                    <TD>
                        <input type="button" value="<- 1 "
                               onClick="addToOptions(document.formInventaire.select2,document.formInventaire.select1,1,document.formInventaire.compiledInv);">
                        <br><input type="button" value="<- 5 "
                                   onClick="addToOptions(document.formInventaire.select2,document.formInventaire.select1,5,document.formInventaire.compiledInv);">
                        <br><input type="button" value="-> 1 "
                                   onClick="substractToOptions(document.formInventaire.select1,1,document.formInventaire.compiledInv);">
                        <br><input type="button" value="-> 5 "
                                   onClick="substractToOptions(document.formInventaire.select1,5,document.formInventaire.compiledInv);">
                    </TD>
                    <TD>
                        <select style="width: 280px;" name="selecttype"
                                onchange='cleanOption(document.formInventaire.select2); addOptionArray(document.formInventaire.select2, listeBase, this.value, document.formInventaire.selectvaleur.value);'>
                            <option value=''>Tous types d’objets</option>
                            <?php
                            $req_tobj = "select distinct tobj_libelle from type_objet order by tobj_libelle";
                            $stmt     = $pdo->query($req_tobj);
                            while ($result = $stmt->fetch())
                            {
                                $tobj_libelle = str_replace("\"", "", $result['tobj_libelle']);
                                echo "<option value='$tobj_libelle'>$tobj_libelle</option>";
                            }
                            ?>
                        </select><br/>
                        <select style="width: 280px;" name="selectvaleur"
                                onchange='cleanOption(document.formInventaire.select2); addOptionArray(document.formInventaire.select2, listeBase, document.formInventaire.selecttype.value, this.value);'>
                            <option value=''>Valeur indéfinie</option>
                            <option value='0;1000'>Moins de 1 000 brouzoufs</option>
                            <option value='1000;5000'>Entre 1 000 et 5 000 brouzoufs</option>
                            <option value='5000;10000'>Entre 5 000 et 10 000 brouzoufs</option>
                            <option value='10000;20000'>Entre 10 000 et 20 000 brouzoufs</option>
                            <option value='20000;50000'>Entre 20 000 et 50 000 brouzoufs</option>
                            <option value='50000;100000'>Entre 50 000 et 100 000 brouzoufs</option>
                            <option value='100000;100000000'>Plus de 100 000 brouzoufs</option>
                        </select><br/>
                        <select multiple name="select2" size="10" style="width:280px;">
                        </select>
                    </TD>
                </TR>
            </TABLE>

            <input type="hidden" name="compiledInv" value="">
            <SCRIPT>
                addOptionArray(document.formInventaire.select2, listeBase, '', '');
                fillOptions(document.formInventaire.select2, document.formInventaire.select1, listeCurrent);
                compileAccumulatorCounter(document.formInventaire.select1, document.formInventaire.compiledInv);
            </SCRIPT>
            <input type="submit" value="Modifier l’inventaire">
        </form>
        <hr>
        <p id='p_sort'>SORTS (<a href="#p_haut">Retour en haut</a>)</p>
        <form method="post" action="#">
            <input type="hidden" name="methode" value="update_sorts">
            <input type="hidden" name="mod_perso_cod" value="<?php echo $mod_perso_cod ?>">
            <TABLE width="80%" align="center">
                <TR>
                    <?php // LISTE DES SORTS
                    $req_sorts =
                        "select sort_cod,sort_nom,sort_cout from sorts where sort_comp_cod <> 69 order by sort_nom ";
                    $stmt      = $pdo->query($req_sorts);
                    $nbs       = 0;
                    $req_sm    = "select sort_cod,sort_nom,sort_cout from sorts,perso_sorts
                              where psort_perso_cod = :mod_perso_cod
                              and psort_sort_cod = :s_cod ";
                    $stmt2     = $pdo->prepare($req_sm);
                    while ($result = $stmt->fetch())
                    {
                        $s_cod = $result['sort_cod'];
                        $stmt2 = $pdo->execute(array(":mod_perso_cod" => $mod_perso_cod,
                                                     ":s_cod"         => $s_cod), $stmt2);
                        $memo  = "";
                        if ($stmt2->fetch())
                        {
                            $memo = "checked";
                        }
                        ?>

                        <TD><INPUT type="checkbox" class="vide" name="PERSO_SORT_<?php echo $s_cod; ?>"
                                   value="MEMORISE" <?php echo $memo; ?> > &nbsp; <?php echo $result['sort_nom']; ?>
                            &nbsp;
                        </TD>

                        <?php if ($nbs % 4 == 0)
                    {
                        echo "</TR><TR>";
                    }
                        $nbs++;
                    } ?>
                </TR>
                <TR>
                    <TD colspan="4" align="center"><input type="submit" value="Modifier les sorts"></TD>
                </TR>
            </TABLE>
        </form>
        <hr>
        <p id='p_effet'>EFFETS AUTO (<a href="#p_haut">Retour en haut</a>)</p>
        <?php // Liste des monstres
        $req = 'select gmon_nom, gmon_cod from monstre_generique order by gmon_nom';
        echo '<select id="liste_monstre_modele" style="display:none;">' . $html->select_from_query($req, 'gmon_cod', 'gmon_nom') . '</select>';

        // Liste des Bonus-malus
        $req = "select tbonus_libc, tonbus_libelle || case when tbonus_cumulable = 'O' then ' [cumulable]' else '' end || case when tbonus_gentil_positif then ' (+)' else ' (-)' end as tonbus_libelle
		from bonus_type
		order by tonbus_libelle ";
        echo '<select id="liste_bm_modele" style="display:none;">' . $html->select_from_query($req, 'tbonus_libc', 'tonbus_libelle') . '</select>';
        ?>
        <form method="post" action="#" onsubmit="return Validation.Valide ();">
            <input type="hidden" name="methode" value="add_effet_auto">
            <input type="hidden" name="mod_perso_cod" value="<?php echo $mod_perso_cod ?>">
            <input type='hidden' name='fonctions_supprimees' id='fonctions_supprimees' value=''/>
            <input type='hidden' name='fonctions_ajoutees' id='fonctions_ajoutees' value=''/>
            <input type='hidden' name='fonctions_annulees' id='fonctions_annulees' value=''/>
            <input type='hidden' name='fonctions_existantes' id='fonctions_existantes' value=''/>
            <div id="liste_fonctions"></div>
            <script>EffetAuto.MontreValidite = true;</script>
            <?php // D’abord en lecture seule les effets liés au type de monstre du perso
            $req  = "select fonc_cod, fonc_nom, fonc_type, substr(fonc_effet, 1, 3) as fonc_effet, CASE WHEN length(fonc_effet)>3 THEN 'O' ELSE 'N' END as fonc_cumulatif, fonc_force, fonc_duree, fonc_type_cible, fonc_nombre_cible, fonc_portee, fonc_proba, fonc_message
				from fonction_specifique
				inner join perso on perso_gmon_cod = fonc_gmon_cod
				where perso_cod = $mod_perso_cod AND fonc_gmon_cod IS NOT NULL";
            $stmt = $pdo->query($req);
            while ($result = $stmt->fetch())
            {
                $fonc_id           = $result['fonc_cod'];
                $fonc_type         = $result['fonc_type'];
                $fonc_nom          = $result['fonc_nom'];
                $fonc_effet        = $result['fonc_effet'];
                $fonc_cumulatif    = $result['fonc_cumulatif'];
                $fonc_force        = $result['fonc_force'];
                $fonc_duree        = $result['fonc_duree'];
                $fonc_type_cible   = $result['fonc_type_cible'];
                $fonc_nombre_cible = $result['fonc_nombre_cible'];
                $fonc_portee       = $result['fonc_portee'];
                $fonc_proba        = $result['fonc_proba'];
                $fonc_message      = $result['fonc_message'];
                echo "
		<script>EffetAuto.EcritEffetAutoExistant('$fonc_type', '$fonc_nom', $fonc_id, '$fonc_force', '$fonc_duree', '$fonc_message', '$fonc_effet', '$fonc_cumulatif', '$fonc_proba', '$fonc_type_cible', '$fonc_portee', '$fonc_nombre_cible', '0', true);</script>";
            }

            $req  = "select fonc_cod, fonc_nom, fonc_type, substr(fonc_effet, 1, 3) as fonc_effet, CASE WHEN length(fonc_effet)>3 THEN 'O' ELSE 'N' END as fonc_cumulatif, fonc_force, fonc_duree, fonc_type_cible, fonc_nombre_cible, fonc_portee, fonc_proba, fonc_message,
					coalesce(EXTRACT(EPOCH FROM (fonc_date_limite - now())::INTERVAL) / 60, 0)::integer as validite
				from fonction_specifique where fonc_perso_cod = $mod_perso_cod";
            $stmt = $pdo->query($req);
            while ($result = $stmt->fetch())
            {
                $fonc_id           = $result['fonc_cod'];
                $fonc_type         = $result['fonc_type'];
                $fonc_nom          = $result['fonc_nom'];
                $fonc_effet        = $result['fonc_effet'];
                $fonc_cumulatif    = $result['fonc_cumulatif'];
                $fonc_force        = $result['fonc_force'];
                $fonc_duree        = $result['fonc_duree'];
                $fonc_type_cible   = $result['fonc_type_cible'];
                $fonc_nombre_cible = $result['fonc_nombre_cible'];
                $fonc_portee       = $result['fonc_portee'];
                $fonc_proba        = $result['fonc_proba'];
                $fonc_message      = $result['fonc_message'];
                $fonc_validite     = $result['validite'];
                echo "
		<script>EffetAuto.EcritEffetAutoExistant('$fonc_type', '$fonc_nom', $fonc_id, '$fonc_force', '$fonc_duree', '$fonc_message', '$fonc_effet', '$fonc_cumulatif', '$fonc_proba', '$fonc_type_cible', '$fonc_portee', '$fonc_nombre_cible', '$fonc_validite', false);</script>";
            }
            ?>
            <div style='clear: both'>
                <a href="#" onclick='EffetAuto.NouvelEffetAuto (); return false;'>Nouvel effet</a><br/>
                <input type="submit" value="Valider les suppressions / modifications / ajouts d’effets !" class='test'/>
            </div>
        </form>
        <hr/>
        <p id='p_fam'>FAMILIER (<a href="#p_haut">Retour en haut</a>)</p>
        <?php $req_fam = "select pfam_familier_cod, perso_nom, coalesce(pfam_duree_vie, 0) as pfam_duree_vie from perso_familier
	inner join perso on perso_cod = pfam_familier_cod AND  perso_actif='O'
	where pfam_perso_cod = $mod_perso_cod order by pfam_familier_cod desc";
        $stmt          = $pdo->query($req_fam);
        if ($result = $stmt->fetch())
        {
            $fam_cod       = $result['pfam_familier_cod'];
            $fam_nom       = $result['perso_nom'];
            $fam_duree_vie = $result['pfam_duree_vie'];
            ?>
            <form method="post" name="familier" action="#">
                <input type="hidden" name="methode" value="update_familier">
                <input type="hidden" name="mod_perso_cod" value="<?php echo $mod_perso_cod ?>">
                Modifier familier<br>
                <?php echo "$fam_nom (n°$fam_cod)"; ?><br/>
                Durée de vie (en nombre de DLTs du maître) : <input type='text' name='fam_duree_vie' size='4'
                                                                    value='<?php echo $fam_duree_vie; ?>'/>. (0 = aucune
                limite).<br/>
                <input type="submit" value="Modifier">&nbsp;<input type="submit" value="Supprimer"
                                                                   onClick="document.familier.methode.value='suppr_familier';">
            </form>
        <?php } else
        {
            ?>
            <form method="post" action="#">
                <input type="hidden" name="methode" value="ajout_familier">
                <input type="hidden" name="mod_perso_cod" value="<?php echo $mod_perso_cod ?>">
                Le perso n’a aucun familier actif.<br>
                Rattacher un monstre en tant que familier ? Attention, le monstre doit se trouver sur la même case que
                le perso !<br><br>
                Numéro du monstre à rattacher : <input type='text' name='fam_cod' value=''/><br/>
                Durée de vie (en nombre de DLTs du maître) : <input type='text' name='fam_duree_vie' size='4'
                                                                    value='0'/>. (0 = aucune limite).<br/>
                <input type="submit" value="Ajouter"/>
            </form>
        <?php } ?>
        <hr>
        <p id='p_rel'>RELIGION (<a href="#p_haut">Retour en haut</a>)</p>
        <?php $req_religion = "select 	dper_dieu_cod,dper_niveau,dper_points"
                              . " from  	dieu_perso where dper_perso_cod = $mod_perso_cod";
        $stmt               = $pdo->query($req_religion);
        if ($result = $stmt->fetch())
        {
            $dieu_cod = $result['dper_dieu_cod'];
            ?>
            <form method="post" name="religion" action="#">
                <input type="hidden" name="methode" value="update_religion">
                <input type="hidden" name="mod_perso_cod" value="<?php echo $mod_perso_cod ?>">
                Modifier religion<br>
                Dieu :
                <select name="dper_dieu_cod">
                    <?php
                    $dieu    = new dieu;
                    $alldieu = $dieu->getAll();

                    foreach ($alldieu as $detaildieu)
                    {
                        $nom_dieu = $detaildieu->dieu_nom;
                        $cod_dieu = $detaildieu->dieu_cod;
                        $selected = "";
                        if ($cod_dieu == $dieu_cod)
                        {
                            $selected = "selected";
                        }
                        echo "<option value=\"$cod_dieu\" $selected>$nom_dieu</option>\n";
                    }
                    ?>
                </select>&nbsp;
                Niveau :
                <select name="dper_niveau">
                    <?php $req_dieu = "select dniv_niveau ,dniv_libelle"
                                      . " from dieu_niveau where dniv_dieu_cod = $dieu_cod order by dniv_niveau";
                    $stmt2          = $pdo->query($req_dieu);

                    $has_niveau = false;
                    while ($result2 = $stmt2->fetch())
                    {
                        $has_niveau   = true;
                        $dniv_libelle = $result2['dniv_libelle'];
                        $dniv_niveau  = $result2['dniv_niveau'];
                        $selected     = "";
                        if ($dniv_niveau == $result['dper_niveau'])
                        {
                            $selected = "selected";
                        }
                        echo "<option value=\"$dniv_niveau\" $selected>$dniv_niveau - $dniv_libelle</option>\n";
                    }
                    if (!$has_niveau)
                    {
                        echo "<option value=\"0\" >0 - pas de niveau</option>\n";
                    }
                    ?>
                </select>&nbsp;
                Points : <input type="text" name="dper_points" value="<?php echo $result['dper_points']; ?>"><br><br>
                <input type="submit" value="Modifier">&nbsp;<input type="submit" value="Supprimer"
                                                                   onClick="document.religion.methode.value='supr_religion';">
            </form>
        <?php } else { ?>
            <form method="post" action="#">
                <input type="hidden" name="methode" value="add_religion">
                <input type="hidden" name="mod_perso_cod" value="<?php echo $mod_perso_cod ?>">
                Aucune religion sélectionnée<br>
                Dieu :
                <select name="dper_dieu_cod">
                    <?php
                    $dieu    = new dieu;
                    $alldieu = $dieu->getAll();
                    foreach ($alldieu as $detaildieu)
                    {
                        $nom_dieu = $detaildieu->dieu_nom;
                        $cod_dieu = $detaildieu->dieu_cod;
                        echo "<option value=\"$cod_dieu\">$nom_dieu</option>\n";
                    }
                    ?>
                </select>&nbsp;
                Niveau : <input type="text" name="dper_niveau" value="0">&nbsp;
                Points : <input type="text" name="dper_points" value="0"><br><br>
                <input type="submit" value="Ajouter">

            </form>
        <?php } ?>
        <hr>
        <p id='p_titre'>TITRES (<a href="#p_haut">Retour en haut</a>)</p>
        <TABLE width="80%" align="center">

            <?php // LISTE DES TITRES

            $req_titres = "select 	ptitre_cod,ptitre_titre,to_char(ptitre_date,'DD/MM/YYYY hh24:mi:ss') as date"
                          . " from perso_titre where ptitre_perso_cod = $mod_perso_cod order by ptitre_titre";
            $stmt       = $pdo->query($req_titres);
            $nbs        = 0;
            while ($result = $stmt->fetch())
            {
                ?>
                <TR>
                    <TD class="soustitre2">
                        <form method="post" name="updtitre<?php echo $result['ptitre_cod']; ?>" action="#">
                            <input type="hidden" name="methode" value="update_titre">
                            <input type="hidden" name="mod_perso_cod" value="<?php echo $mod_perso_cod ?>">
                            <input type="hidden" name="ptitre_cod" value="<?php echo $result['ptitre_cod']; ?>">
                            <input type="text" name="ptitre_titre" value="<?php echo $result['ptitre_titre']; ?>">
                            <input type="submit" value="Modifier">
                            <input type="submit" value="Supprimer"
                                   onClick="document.updtitre<?php echo $result['ptitre_cod']; ?>.methode.value='supr_titre';">
                        </form>

                    </TD>
                    <TD>Obtenu le <?php echo $result['date']; ?></TD>
                </TR>
            <?php } ?>
            <TR>
                <TD colspan="2">
                    <form method="post" action="#">
                        <input type="hidden" name="methode" value="update_titres">
                        <input type="hidden" name="mod_perso_cod" value="<?php echo $mod_perso_cod ?>">
                        Ajouter un titre : <input type="text" name="ptitre_titre">
                        <input type="submit" value="Ajouter">
                    </form>
                </TD>
            </TR>
        </TABLE>

        <hr>
        <p id='p_pos'>POSITION (<a href="#p_haut">Retour en haut</a>)</p>
        <TABLE width="80%" align="center">

            <?php // POSITION DU PERSONNAGE
            $pdo = new bddpdo;
            $req_pos   = " select pos_x,pos_y,pos_etage from positions,perso_position
                where ppos_pos_cod = pos_cod and ppos_perso_cod = :perso ";

            $stmt      = $pdo->prepare($req_pos);
            $stmt = $pdo->execute(array(":perso" =>$mod_perso_cod ),$stmt);

            $pos_x     = 0;
            $pos_y     = 0;
            $pos_etage = 0;
            if ($result = $stmt->fetch())
            {
                $pos_x     = $result['pos_x'];
                $pos_y     = $result['pos_y'];
                $pos_etage = $result['pos_etage'];
            }
            $tmpperso = new perso;
            $tmpperso->charge($mod_perso_cod);
            if ($tmpperso->is_locked())
            { ?>
                <TR>
                    <TD colspan="2"><strong>Ce perso est locké en combat !</strong> Son déplacement va rompre tous les
                        locks de combat.
                    </TD>
                </TR>
            <?php } ?>
            <form method="post" action="#">
                <input type="hidden" name="methode" value="move_perso">
                <input type="hidden" name="mod_perso_cod" value="<?php echo $mod_perso_cod ?>">
                <TR>
                    <TD>

                        <p>Entrez la position à laquelle vous souhaitez déplacer ce perso :<br>
                    </TD>
                    <TD>
                        X : <input type="text" name="pos_x" maxlength="5" size="5" value="<?php echo $pos_x ?>"> -
                        Y : <input type="text" name="pos_y" maxlength="5" size="5" value="<?php echo $pos_y ?>"> -
                        Étage : <select name="etage">
                            <?php
                            echo $html->etage_select($pos_etage);
                            ?>
                        </select><br>
                        <center><input type="submit" class="test" value="Déplacer !">
                    </TD>
                </TR>
            </form>
        </TABLE>
        <hr>
        AJOUTER UN OBJET DE QUÊTE QUI N’EXISTE PAS ENCORE.
        <p><strong>Attention ! </strong>Cette procédure n’a pour but que de créer de nouveaux objets (première
            apparition dans le jeu) dans l’inventaire d’un perso.</p>

        <form method="post" action="#">
            <input type="hidden" name="methode" value="add_new_object">
            <input type="hidden" name="mod_perso_cod" value="<?php echo $mod_perso_cod ?>">
            <TABLE width="80%" align="center">
                <tr>
                    <td class="soustitre2"><p>Nom de l’objet (une fois identifié) :</td>
                    <td><input type="text" name="nom_objet" size="50"></td>
                </tr>
                <tr>
                    <td class="soustitre2"><p>Nom de l’objet (pas encore identifié) :</td>
                    <td><input type="text" name="nom_objet_non_iden" size="50"></td>
                </tr>
                <tr>
                    <td class="soustitre2"><p>Description :</td>
                    <td><textarea name="desc" rows="10" cols="30"></textarea></td>
                </tr>
                <tr>
                    <td class="soustitre2"><p>Poids de l’objet :</td>
                    <td><input type="text" name="poids_objet"></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" class="test" value="Créer !">
                    </td>
                </TR>
            </TABLE>
        </form>
    <?php } else
    {
        echo "Entrez un numéro de perso";
    }
}
?>
    <p style="text-align:center;"><a href="<?php echo $PHP_SELF ?>">Retour au début</a>
<?php $contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";