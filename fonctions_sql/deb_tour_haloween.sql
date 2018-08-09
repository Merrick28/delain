CREATE OR REPLACE FUNCTION public.deb_tour_haloween(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* fonction deb_tour_haloween: bazard dans les voies magiques    */
/* On passe en paramètres                                        */
/*  $1 = monstre                                                 */
/*  $2 = puissance pour determiner le nombre de cible            */
/*  $3 = chance de provoquer le bazard 				 */
/*****************************************************************/
/* Créé le 21/10/2009                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	monstre alias for $1;
	puissance alias for $2;
	chance alias for $3;
	code_retour text;	-- chaine html de sortie
	texte_evt text;		--texte d'évènement complété des dégâts
        nom_monstre text;       -- nom du monstre
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
	nom_cible text;	         -- nom de la cible
	pv_cible integer;
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
	px_gagne numeric;				-- PX gagnes
	ligne record;					-- enregistrements
	pos_lanceur integer;			-- pos_cod du lanceur
	x_lanceur integer;			-- x du lanceur
	y_lanceur integer;			-- y du lanceur
	e_lanceur integer;			-- etage du lanceur
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
	distance_cibles integer;	-- distance entre lanceur et cible
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	des integer;					-- lancer de dés
	compt integer;					-- fourre tout
	v_act_numero integer;
	nb_cible integer;
	nb_cible2 integer;
	v_pv_cible integer;

begin
-- fin de l'animation le 24/11/2009 donc un return immédiat.. tout ce qui suit ne sert plus à rien si ce n'est pour trace

return code_retour;



code_retour := 'pas d''action';
--On regarde si la fonction doit se déclencher
if  lancer_des(1,100) > chance then
	return code_retour;
end if;
-- on stock le nom du monstre pour le text
        select into nom_monstre perso_nom from perso
               where perso_cod = monstre;
-- on prend la position du monstre, pour trouver les cibles
	select into pos_lanceur,x_lanceur,y_lanceur,e_lanceur
		pos_cod,pos_x,pos_y,pos_etage
		from positions,perso_position
		where ppos_perso_cod = monstre
		and ppos_pos_cod = pos_cod;
	select into nb_cible count(perso_cod)
		from perso,perso_position
		where perso_actif = 'O'
		and perso_tangible = 'O'
		and ppos_perso_cod = perso_cod
		and perso_cod != monstre
		and ppos_pos_cod = pos_lanceur;
	
-- On détermine les CIBLES en fonction de la puissance
	for ligne in select perso_cod,perso_nom,perso_pv,perso_type_perso,perso_voie_magique,lancer_des(1,1000) as num
		from perso,perso_position,positions
		where perso_actif = 'O'
		and perso_tangible = 'O'
		and ppos_perso_cod = perso_cod
		and ppos_pos_cod = pos_cod
		and pos_cod = pos_lanceur
		and perso_cod != monstre
		and perso_type_perso = 1
                order by num limit puissance loop
		 if ligne.perso_type_perso = 1 then
                        des := lancer_des(1,6);
                        update perso set perso_voie_magique = des where perso_cod = ligne.perso_cod;
                        code_retour := code_retour||'Le vortex magique agit sur <b>'||ligne.perso_nom||'</b>, vous altérez complétement la voie magique de votre cible.';

	         texte_evt := ' La créature d''Haloween a modifié la nature magique de '||ligne.perso_nom||'.';
   insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     	values(nextval('seq_levt_cod'),14,now(),1,monstre,texte_evt,'O','O',monstre,ligne.perso_cod);
   insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     	values(nextval('seq_levt_cod'),14,now(),1,ligne.perso_cod,texte_evt,'N','O',monstre,ligne.perso_cod);
                 end if;	
end loop;
	return code_retour;
end;$function$

