<?php
ini_set('include_path', '.:/home/delain/delain/web/phplib-7.4a/php:/home/delain/delain/web/www/includes');

include_once "delain_header.php";
include "classes.php";
$pdo = new bddpdo();

writelog( "-----" , 'verif_vote');
writelog( date("d/m/y - H:i")." : *** Démarrage ***", 'verif_vote' );

$IpReq1 = "SELECT icompt_compt_ip,  compte_vote_compte_cod,compte_vote_cod,compte_vote_ip_compte
            FROM public.compte_ip
            INNER JOIN public.compte_vote_ip ON public.compte_ip.icompt_cod = public.compte_vote_ip.compte_vote_icompt_cod where  public.compte_vote_ip.compte_vote_verifier = FALSE 
            and public.compte_vote_ip.compte_vote_date = current_date and compte_vote_ip is not null FETCH NEXT 100 ROWS ONLY";
$IpReq2 = "SELECT icompt_compt_ip,  compte_vote_compte_cod,compte_vote_cod,compte_vote_ip_compte
            FROM public.compte_ip
            INNER JOIN public.compte_vote_ip ON public.compte_ip.icompt_cod = public.compte_vote_ip.compte_vote_icompt_cod where  public.compte_vote_ip.compte_vote_verifier = FALSE 
            and public.compte_vote_ip.compte_vote_date = (current_date - 1) and compte_vote_ip is not null FETCH NEXT 100 ROWS ONLY";

$ips                = array();
$Compte_code_ips    = array();
$Compte_vote_codeId = array();
$stmt               = $pdo->query($IpReq1);
while ($result = $stmt->fetch())
{
    $Code_ip = trim($result['compte_vote_ip_compte']);
    array_push($ips, $Code_ip);
    $Code_compte_vote             = $result['compte_vote_compte_cod'];
    $compte_vote_codeid           = $result['compte_vote_cod'];
    $Compte_code_ips[$Code_ip]    = $Code_compte_vote;
    $Compte_vote_codeId[$Code_ip] = $compte_vote_codeid;
}

$postdata = http_build_query(
    array(
        'nomFonction' => 'IPVote',
        'idJeu'       => 715,
        'dateVote'    => date("Y-m-d"),
        'IP'          => $ips
    )
);

if (count($ips) != 0)
{
    writelog( date("d/m/y - H:i")." : call API jeux-alternatifs - Postdata=[{$postdata}]", 'verif_vote' );
    $opts = array('http' =>
                      array(
                          'method'  => 'POST',
                          'header'  => 'Content-type: application/x-www-form-urlencoded',
                          'content' => $postdata
                      )
    );

    $context = stream_context_create($opts);
    writelog( date("d/m/y - H:i")." : stream_context_create => {$context}", 'verif_vote' );
    $result      = file_get_contents('http://www.jeux-alternatifs.com/API.php', false, $context);
    //echo "<br>Check : Résultat jeux-alternatifs: ".$result;
    writelog( date("d/m/y - H:i")." : Résultat jeux-alternatifs => {$result}", 'verif_vote' );

    $list        = json_decode(substr($result, 3), true);
    $listContenu = $list['contenu'];
    if ($listContenu != null)
    {
        //echo "<br>Check : Traitement des données de  jeux-alternatifs: ";
        //echo "<pre>"; print_r($listContenu); echo "</pre>";

        foreach ($listContenu as $key => $IPs)
        {

            foreach ($IPs as $IPkey => $IPValue)
            {
                $IP = $IPkey;

                $etatVote = $IPValue["etatVote"];

                $compteVoteCod = $Compte_code_ips[$IP];

                // il faut maintenant faire la mise à jours de la ligne si etat vote n'est pas a 2// 1 : à voté, 2 : n’a pas voté
                // si c'est à deux c'est qu'il a cliqué mais pas voté pour delain
                if ($etatVote != 1)
                {
                    $reqUpdate =
                        "UPDATE public.compte_vote_ip SET  compte_vote_verifier= true,compte_vote_pour_delain = false WHERE compte_vote_compte_cod = :compte and compte_vote_ip_compte=:ip";
                    $stmt      = $pdo->prepare($reqUpdate);
                    $stmt      = $pdo->execute(array(":compte" => $compteVoteCod,
                                                     ":ip"     => $IP), $stmt);

                } else
                {

                    $reqUpdate =
                        "UPDATE public.compte_vote_ip SET  compte_vote_verifier= true,compte_vote_pour_delain = true WHERE compte_vote_compte_cod = :compte and compte_vote_ip_compte=:ip";
                    $stmt      = $pdo->prepare($reqUpdate);
                    $stmt      = $pdo->execute(array(":compte" => $compteVoteCod,
                                                     ":ip"     => $IP), $stmt);

                    $reqselectVotecompte = "SELECT compte_vote_cod, compte_vote_total_px_gagner, compte_vote_nbr, 
                                    compte_vote_compte_cod
                               FROM public.compte_vote where compte_vote_compte_cod=:compte";
                    $stmt                = $pdo->prepare($reqselectVotecompte);
                    $stmt                = $pdo->execute(array(":compte" => $compteVoteCod), $stmt);


                    if ($result = $stmt->fetch())
                    {
                        // mise à jours du compte
                        $reqCompteVote =
                            "UPDATE public.compte_vote SET   compte_vote_nbr=(compte_vote_nbr+1) WHERE   compte_vote_compte_cod=:compte";

                    } else
                    {

                        $reqCompteVote =
                            "INSERT INTO public.compte_vote( compte_vote_nbr, compte_vote_compte_cod) VALUES (1, :compte )";
                    }
                    $stmt = $pdo->prepare($reqCompteVote);
                    $stmt = $pdo->execute(array(":compte" => $compteVoteCod), $stmt);

                }


            }

        }
    }
}

// Maintenant il faut vérifier le jour que nous sommes. Si on est le 1er du mois et qu'il n'y a rien dans vote_list  pour le mois d'avant, on mets a jour l'xp et on rajoute une colonne dans vote_list
//if(date("d")== "05")
//{
$dateDuJour = date("Y-m-d");
$dateDuMois = date("Y-m");
// on regarde par rapport au mois
$reqVoteList =
    "SELECT vote_list_date FROM public.vote_list where to_char(vote_list_date, 'yyyy-mm') = :date";
$stmt        = $pdo->prepare($reqVoteList);
$stmt        = $pdo->execute(array(":date" => $dateDuMois), $stmt);

// on ne fait quelque chose que s'il n'y a pas de requete sinon cela veut dire qu'on est deja passé ce mois-ci
if (!$result = $stmt->fetch())
{

    // on commence par mettre a jour (#LAG pour eviter une multiple distribution)
    $repUpdateVoteList = " INSERT INTO public.vote_list(vote_list_date) VALUES (:date)";
    $stmt              = $pdo->prepare($repUpdateVoteList);
    $stmt              = $pdo->execute(array(":date" => $dateDuJour), $stmt);
    // puis on récupere tous les personnes qui ont voté ce mois ci et on fonction du nombre de vote on donne les xp correspondants.
    $requGetCompteVote = "select compte_vote_compte_cod FROM public.compte_vote";
    $stmt              = $pdo->query($requGetCompteVote);

    $ReqNbrVote = "select count(*)as nbrvote from public.compte_vote_ip where to_char(compte_vote_date, 'yyyy-mm') 
				= :mois and  compte_vote_pour_delain=true and compte_vote_compte_cod = :compte";
    $stmt2      = $pdo->prepare($ReqNbrVote);

    $ReqGainXp = "select vote_xp_gain_xp from public.vote_xp where vote_xp_nombre_vote= :vote";
    $stmt3     = $pdo->prepare($ReqGainXp);

    $reqPerso = "select pcompt_perso_cod from public.perso_compte where pcompt_compt_cod= :compte";
    $stmt4    = $pdo->prepare($reqPerso);

    $ReqSelectFafaCod = "select pfam_familier_cod from public.perso 
                                    inner join public.perso_familier ON public.perso_familier.pfam_perso_cod = public.perso.perso_cod
                                    inner join public.perso fam ON fam.perso_cod = public.perso_familier.pfam_familier_cod
                                    where public.perso_familier.pfam_perso_cod = :perso and fam.perso_actif='O' ";
    $stmt5            = $pdo->prepare($ReqSelectFafaCod);

    $ReqUpdateCompteVote = "UPDATE public.compte_vote
                SET compte_vote_total_px_gagner=(compte_vote_total_px_gagner + :gain)
              WHERE compte_vote_compte_cod= :compte";
    $stmt6               = $pdo->prepare($ReqUpdateCompteVote);
    // pour chacun des comptes on ajoute l'xp

    while ($result = $stmt->fetch())
    {
        $compteCod = $result['compte_vote_compte_cod'];

        $moisAnnee = date('Y-m', mktime(12, 0, 0, date("m") - 1, 1, date("Y")));
        // il faut récuperer dans compte_vote_ip pour le nombre de vote
        //echo "<br>Check : $moisAnnee * $compteCod ";
        writelog( date("d/m/y - H:i")." : Check : $moisAnnee * $compteCod", 'verif_vote' );
        $stmt2   = $pdo->execute(array(
                                     ":mois"   => $moisAnnee,
                                     ":compte" => $compteCod
                                 ), $stmt2);
        $result2 = $stmt2->fetch();
        $nbrVote = $result2['nbrvote'];
        //echo "Nbr de Vote : $nbrVote ";
        writelog( date("d/m/y - H:i")." : Nbr de Vote : $nbrVote", 'verif_vote' );

        // il n'y a plus qu'a récuperer l'xp a faire gagner par perso et les perso puis ajouter l'xp gagné
        $stmt3   = $pdo->execute(array(":vote" => $nbrVote), $stmt3);
        $result3 = $stmt3->fetch();
        $GainXp  = 1 * $result3['vote_xp_gain_xp'];
        //echo 'gain: ' . $GainXp;
        writelog( date("d/m/y - H:i")." : gain: : $GainXp", 'verif_vote' );

        // pour chacun des perso du comptes, on rajoute  l'xp gagné
        if ($GainXp > 0)
        {

            $stmt4 = $pdo->execute(array(":compte" => $compteCod), $stmt4);
            while ($result4 = $stmt4->fetch())
            {
                //echo " Perso={$result4['pcompt_perso_cod']} ";
                writelog( date("d/m/y - H:i")." : Perso={$result4['pcompt_perso_cod']}", 'verif_vote' );

                $codPerso  = $result4['pcompt_perso_cod'];
                $tempperso = new perso;
                $tempperso->charge($codPerso);
                $tempperso->perso_px = $tempperso->perso_px + $GainXp;
                $tempperso->stocke();
                unset($tempperso);

                $ligne_evt = new ligne_evt;
                $ligne_evt->levt_tevt_cod = 18 ;
                $ligne_evt->levt_lu = 'N';
                $ligne_evt->levt_visible = 'O';
                $ligne_evt->levt_perso_cod1 = $codPerso   ;
                $ligne_evt->levt_cible = $codPerso ;
                $ligne_evt->levt_texte = "Grace à ses contributions pour l'essor de Delain, [perso_cod1] a reçu {$GainXp} px bien mérité(s).";
                $ligne_evt->stocke(true);		// Nouvel évènement


                // et on cherche s'il y a un fafa rattaché, si oui, on ajoute aussi de l'xp! Parce qu'on est sympas ^^
                $stmt5 = $pdo->execute(array(":perso" => $codPerso), $stmt5);


                if ($result5 = $stmt5->fetch())
                {
                    //echo " Familier={$result5['pfam_familier_cod']} ";
                    writelog( date("d/m/y - H:i")." :  Familier={$result5['pfam_familier_cod']}", 'verif_vote' );

                    $tempperso = new perso;
                    $tempperso->charge($result5['pfam_familier_cod']);
                    $tempperso->perso_px = $tempperso->perso_px + $GainXp;
                    $tempperso->stocke();
                    unset($tempperso);

                    $ligne_evt = new ligne_evt;
                    $ligne_evt->levt_tevt_cod = 18 ;
                    $ligne_evt->levt_lu = 'N';
                    $ligne_evt->levt_visible = 'O';
                    $ligne_evt->levt_perso_cod1 = $result5['pfam_familier_cod']   ;
                    $ligne_evt->levt_cible = $result5['pfam_familier_cod'] ;
                    $ligne_evt->levt_texte = "Grace à ses contributions pour l'essor de Delain, [perso_cod1] a reçu {$GainXp} px bien mérité(s).";
                    $ligne_evt->stocke(true);		// Nouvel évènement
                }


            }

            // une fois la mise a jour faite, on met a jour compte_vote
            $stmt6 = $pdo->execute(array(":gain"   => $GainXp,
                ":compte" => $compteCod), $stmt6);

        }

    }

}

