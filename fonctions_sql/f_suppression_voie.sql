CREATE OR REPLACE FUNCTION public.f_suppression_voie(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*******************************************************/
/* fonction suppression_voie                           */
/*  on passe en params                                 */
/*  $1 = perso_cod                                     */
/*                                                     */
/* on a en retour une chaine html exploitable          */
/*******************************************************/
declare
code_retour text;		-- code_html de retour
-- personnage
personnage alias for $1;	-- perso_cod du perso
v_pa integer;			-- pa du perso
v_nom_voie text;		-- Nom de l'ancienne voie
v_voie_magique integer;				-- Code voie magique
v_type_perso integer;
ligne record;
-- centre magique
centre_lieu text; 		-- Déterminer si on est sur un centre magique
-- sort détruit
v_sort_nom text;
v_sort  integer;
compteur integer;
des integer;
	
begin
code_retour := '';
-- on commence par faire les vérifs d'usage
select into 
	v_pa,v_type_perso
	perso_pa,perso_type_perso
		from perso
		where perso_cod = personnage;
	if not found then
		code_retour := '<p>Anomalie ! Personnage non trouvé !';
		return code_retour;
	end if;
	if v_pa < getparm_n(112) then
		code_retour := '<p>Anomalie ! Pas assez de PA pour supprimer votre voie magique!';
		return code_retour;
	end if;
	if v_type_perso = 3 then
		code_retour := '<p> Un familier ne peut pas avoir de voie magique!';
		return code_retour;
	end if;
-- Vérification que le perso est bien sur un un centre d'entrainement magique
	select into centre_lieu lieu_nom from perso_position,lieu_position,lieu
		where lieu_cod = lpos_lieu_cod
		and lpos_pos_cod = ppos_pos_cod
		and ppos_perso_cod = personnage
		and lieu_tlieu_cod = 13;
	if not found then 
		return '<p>Vous ne pouvez pas faire supprimer votre voie ici, vous n''êtes pas dans un centre magique!';
	end if;
-- On termine les vérifications en regardant si le joueur a bien une voie magique
	Select into v_voie_magique perso_voie_magique
					from perso
					where perso_cod = personnage;
            if v_voie_magique = 0 then
		code_retour := '<p> Vous n''avez pas de voie magique à supprimer!';
		return code_retour;
	    end if;
---- fin des vérifications on peut enfin s'amuser
-- On diminue les PA
	update perso set perso_pa = perso_pa - getparm_n(112) where perso_cod = personnage;
--- on commence par supprimer les sorts réservés si ils existent et concernant la voie magique en cours.
	 
	-- guerisseur
                     if v_voie_magique = 1 then
                      select into v_sort psort_sort_cod from perso_sorts
                                 where psort_perso_cod = personnage
                                 and psort_sort_cod = 150;
                               if found then
                               --- on supprime lenregistrement
                               delete from perso_sorts
                               where psort_perso_cod = personnage
                                     and psort_sort_cod = 150;
                               End if;
                     End if;
         --- mage de guerre
                     if v_voie_magique = 4 then
                      select into v_sort psort_sort_cod from perso_sorts
                                 where psort_perso_cod = personnage
                                 and psort_sort_cod = 152;
                               if found then
                               --- on supprime lenregistrement
                               delete from perso_sorts
                               where psort_perso_cod = personnage
                                     and psort_sort_cod = 152;
                               End if;
                     End if;
         --- mage de bataille
                     if v_voie_magique = 6 then
                      select into v_sort psort_sort_cod 
                                        from perso_sorts
                                       where psort_perso_cod = personnage
                                        and psort_sort_cod = 153;
                               if found then
                               --- on supprime lenregistrement
                                delete from perso_sorts
                              where psort_perso_cod = personnage
                                 and psort_sort_cod = 153;
                               End if;
                     End if;
       --- sorcier 
                     if v_voie_magique = 3 then
                      select into v_sort psort_sort_cod 
                                        from perso_sorts
                                       where psort_perso_cod = personnage
                                        and psort_sort_cod = 149;
                               if found then
                               --- on supprime lenregistrement
                                delete from perso_sorts 
                              where psort_perso_cod = personnage
                                 and psort_sort_cod = 149;
                               End if;
                     End if;
        --- maitre des arcanes
                     if v_voie_magique = 2 then
                      select into v_sort psort_sort_cod 
                                        from perso_sorts
                                       where psort_perso_cod = personnage
                                        and psort_sort_cod = 146;
                               if found then
                               --- on supprime lenregistrement
                                delete from perso_sorts 
                              where psort_perso_cod = personnage
                                 and psort_sort_cod = 146;
                               End if;
                     End if;
         --- enchanteur runique
                     if v_voie_magique = 5 then
                      select into v_sort psort_sort_cod 
                                        from perso_sorts
                                       where psort_perso_cod = personnage
                                        and psort_sort_cod = 138;
                               if found then
                               --- on supprime lenregistrement
                                delete from perso_sorts 
                              where psort_perso_cod = personnage
                                 and psort_sort_cod = 138;

                               End if;
                     End if;
		code_retour := code_retour||'Vous sentez une douleur immense dans votre cerveau, et proche de l''évanouissement vous vous rendez compte que vous avez oublié le sort réservé de votre ancienne voie.';                   

 /*on détermine tous les sorts du persos*/
		
		           compteur := 1;
		for ligne in select psort_sort_cod,perso_cod,lancer_des(1,30) as num

				from perso_sorts,perso
				where perso_actif = 'O'
				and perso_tangible = 'O'
				and psort_perso_cod = perso_cod
  order by num limit 10 loop
		
	if compteur = 1 then
		des := lancer_des (1,100);
                if des > 50 then
                --- on supprime le sort en question
                Compteur := 0 ;
                --- on récupère dans la table sort le nom du sort détruit
                 Select into v_sort_nom sort_nom from sorts
                 Where sort_cod = ligne.psort_sort_cod;
		  code_retour := code_retour||'<br>Vous vous évanouissez sous la douleur. A votre réveil vous savez que vous avez oublié définitivement '||v_sort_nom||'<br>';
		delete from perso_sorts
                where psort_perso_cod = personnage
                and psort_sort_cod = ligne.psort_sort_cod;
                End if ;
          End if ;
     End loop ;
--- on passé à la suppression de la voie magique.
             select into v_nom_voie mvoie_libelle 
                                               from voie_magique
                                               where mvoie_cod = v_voie_magique;
-- on supprime la voie magique du perso
    update perso set perso_voie_magique = 0
		where perso_cod = personnage;
   code_retour := code_retour||'<p> Vous n''êtes plus ' ||v_nom_voie|| ', vous avez payé le prix pour ce retour à une autre vie';
return code_retour;
end;
$function$

