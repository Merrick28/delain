<?php
ini_set('include_path', '.:/home/delain/delain/web/phplib-7.4a/php:/home/delain/delain/web/www/includes');

include "delain_header.php";
include "classes.php";
$db =new base_delain;

$IpReq1 = "SELECT icompt_compt_ip,  compte_vote_compte_cod,compte_vote_cod,compte_vote_ip_compte
            FROM public.compte_ip
            INNER JOIN public.compte_vote_ip ON public.compte_ip.icompt_cod = public.compte_vote_ip.compte_vote_icompt_cod where  public.compte_vote_ip.compte_vote_verifier = FALSE 
            and public.compte_vote_ip.compte_vote_date = current_date  FETCH NEXT 100 ROWS ONLY";
$IpReq2 = "SELECT icompt_compt_ip,  compte_vote_compte_cod,compte_vote_cod,compte_vote_ip_compte
            FROM public.compte_ip
            INNER JOIN public.compte_vote_ip ON public.compte_ip.icompt_cod = public.compte_vote_ip.compte_vote_icompt_cod where  public.compte_vote_ip.compte_vote_verifier = FALSE 
            and public.compte_vote_ip.compte_vote_date = (current_date - 1)  FETCH NEXT 100 ROWS ONLY ) ";

$ips = array();
$Compte_code_ips = array();
$Compte_vote_codeId = array();
$db->query($IpReq1);
while($db->next_record())
{ 
    $Code_ip = trim($db->f('compte_vote_ip_compte'));
    array_push($ips, $Code_ip);
    $Code_compte_vote = $db->f('compte_vote_compte_cod');
    $compte_vote_codeid = $db->f('compte_vote_cod');
    $Compte_code_ips[$Code_ip] = $Code_compte_vote;
    $Compte_vote_codeId[$Code_ip] = $compte_vote_codeid;
}

$postdata = http_build_query(
    array(
                'nomFonction' => 'IPVote',
                'idJeu' => 715,
                'dateVote' => date("Y-m-d"),
                'IP' => $ips
    )
);

if(count($ips) != 0 )
{
    $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );

    $context  = stream_context_create($opts);

    $result = file_get_contents('http://www.jeux-alternatifs.com/API.php', false, $context);
    $list  = json_decode(substr($result,3),true);
    $listContenu = $list['contenu'];
    if($listContenu != null)
    {
        foreach ($listContenu as $key => $IPs){

             foreach ($IPs as $IPkey => $IPValue){
                 $IP = $IPkey;

                 $etatVote = $IPValue["etatVote"];
                
                 $compteVoteCod =$Compte_code_ips[$IP];

                 // il faut maintenant faire la mise à jours de la ligne si etat vote n'est pas a 2// 1 : à voté, 2 : n’a pas voté
                 // si c'est à deux c'est qu'il a cliqué mais pas voté pour delain
                 if($etatVote != 1)
                 {
                    $reqUpdate = "UPDATE public.compte_vote_ip SET  compte_vote_verifier= true,compte_vote_pour_delain = false WHERE compte_vote_compte_cod = ".$compteVoteCod." and compte_vote_ip_compte='".$IP."'";    
                    $db->query($reqUpdate);
                 }
                 else
                 {
                   
                    $reqUpdate = "UPDATE public.compte_vote_ip SET  compte_vote_verifier= true,compte_vote_pour_delain = true WHERE compte_vote_compte_cod = ".$compteVoteCod."and compte_vote_ip_compte='".$IP."'";   
                    $db->query($reqUpdate);

                    $reqselectVotecompte = "SELECT compte_vote_cod, compte_vote_total_px_gagner, compte_vote_nbr, 
                                    compte_vote_compte_cod
                               FROM public.compte_vote where compte_vote_compte_cod=".$compteVoteCod;    
                    $db->query($reqselectVotecompte);
                    // s'il n'y a rien il faut creer la ligne
                    // sinon on update
                    if($db->next_record())
                    {
                         
                       // mise à jours du compte

                        $reqCompteVote = "UPDATE public.compte_vote SET   compte_vote_nbr=(compte_vote_nbr+1) WHERE   compte_vote_compte_cod=".$compteVoteCod;
                        $db->query($reqCompteVote);
                    }
                    else {
                        
                         $reqCompteVote ="INSERT INTO public.compte_vote( compte_vote_nbr, compte_vote_compte_cod) VALUES (1, ".$compteVoteCod.")";
                         $db->query($reqCompteVote);
                    }

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
    $reqVoteList  = "SELECT vote_list_date FROM public.vote_list where to_char(vote_list_date, 'yyyy-mm') ='".$dateDuMois."'";
    $db->query($reqVoteList);
    // on ne fait quelque chose que s'il n'y a pas de requete sinon cela veut dire qu'on est deja passé ce mois si
    if(!$db->next_record())
    {
        
        // on commence par mettre a jours 
       $repUpdateVoteList = " INSERT INTO public.vote_list(vote_list_date) VALUES ('".$dateDuJour."'::date)";
       $db->query($reqVoteList);
       // puis on récupere tous les personnes qui ont voté ce mois ci et on fonction du nombre de vote on donne les xp correspondants.
        $requGetCompteVote = "select compte_vote_compte_cod FROM public.compte_vote";
        $db->query($requGetCompteVote);
        // pour chacun des comptes on ajoute l'xp
        $db2 = new base_delain;
        while($db->next_record())
        {
            $compteCod = $db->f('compte_vote_compte_cod');

            $moisAnnee = date('Y-m',mktime(12, 0, 0, date("m")-1,1, date("Y"))); 
            // il faut récuperer dans compte_vote_ip pour le nombre de vote
            $ReqNbrVote = "select count(*)as nbrvote from public.compte_vote_ip where to_char(compte_vote_date, 'yyyy-mm') 
				= '".$moisAnnee."' and  compte_vote_pour_delain=true and compte_vote_compte_cod = ".$compteCod;
            
             $db2->query($ReqNbrVote);
             $db2->next_record();
             $nbrVote =  $db2->f('nbrvote');
            
             // il n'y a plus qu'a récuperer l'xp a faire gagner par perso et les perso puis ajouter l'xp gagné
             $ReqGainXp = "select vote_xp_gain_xp from public.vote_xp where vote_xp_nombre_vote=".$nbrVote;
             $db2->query( $ReqGainXp);
             $db2->next_record();
             $GainXp =  $db2->f('vote_xp_gain_xp');
             echo 'gain: '.$GainXp;
             // pour chacun des perso du comptes, on rajoute  l'xp gagné
             $reqPerso = "select pcompt_perso_cod from public.perso_compte where pcompt_compt_cod=".$compteCod;
             $db2->query($reqPerso);
             $db3 = new base_delain;
             while($db2->next_record())
             {
                 $codPerso = $db2->f('pcompt_perso_cod');
                 
                 $ReqUpdatePerso="UPDATE public.perso
                            SET perso_px = (perso_px +".$GainXp.")
                          WHERE perso_cod = ".$codPerso;
                 
                 $db3->query($ReqUpdatePerso);
                 
                 // et on cherche s'il y a un fafa rattaché, si oui, on ajoute aussi de l'xp! Parce qu'on est sympas ^^
                 $ReqSelectFafaCod="select pfam_familier_cod from public.perso 
                                    inner join public.perso_familier ON public.perso_familier.pfam_perso_cod = public.perso.perso_cod
                                    where public.perso_familier.pfam_perso_cod = ".$codPerso;
                 
                 $db3->query($ReqSelectFafaCod);
                 if($db3->next_record())
                 {
                    $fafaCod = $db3->f('pfam_familier_cod');
                    $ReqUpdateFafa = "UPDATE public.perso
                            SET perso_px = (perso_px +".$GainXp.")
                          WHERE perso_cod = ".$fafaCod;
                    $db3->query($ReqUpdateFafa);
                 }
                 
                 
             }

             // une fois la mise a jour faite, on met a jour compte_vote
            $ReqUpdateCompteVote = "UPDATE public.compte_vote
                SET compte_vote_total_px_gagner=(compte_vote_total_px_gagner + ".$GainXp.")
              WHERE compte_vote_compte_cod=".$compteCod;
            $db3->query($ReqUpdateCompteVote);
                 
             
        }
        // on cree à la bonne date
        $ReqCreationVoteList="INSERT INTO public.vote_list(
             vote_list_date)
                 VALUES ( '".$dateDuJour."'::date)";
         $db->query($ReqCreationVoteList);
        
    }
//}

?>
