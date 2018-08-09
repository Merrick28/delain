CREATE OR REPLACE FUNCTION public.f_esquive_distance(integer, integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function f_esquive_distance : tente une esquive               */
/* On passe en paramètres                                        */
/*    $1 = type de perso attaquant (1 joueur, 2 monstre)         */
/*    $2 = perso_cod                                             */
/* Le code sortie est un entier                                  */
/*     0 = esquive ratée                                         */
/*     1 = esquive réussie                                       */
/*     2 = réussite critique                                     */
/*     autre = anomalie :                                        */
/*        -1 = perso non trouvé                                  */
/*                                                               */
/*****************************************************************/
/* Créé le 01/04/2003                                            */
/* Liste des modifications :                                     */
/*     17/04/2003 : ajout de la procédure amelioration_com       */
/*		 26/03/2004 : ajout de la qualité d'attaque                */
/*****************************************************************/
declare
	code_retour integer;
	type_perso alias for $1;
	personnage alias for $2;
	qualite_attaque alias for $3;
	compt integer;
	valeur_esquive_init integer;
	nb_esquive integer;
	v_dex integer;
	valeur_esquive integer;
	des integer;
        bonus_air integer;
        temp_bonus_air numeric;
-- variable pour les evts
	nom_perso perso.perso_nom%type;
	texte_evt text;
	temp_amelioration text;
	admin_texte text;
	bonus_esquive integer;
        bonmal integer;
        malus integer;
begin
	code_retour := 0;
	select into v_dex,nb_esquive,nom_perso
			perso_dex,perso_nb_esquive,perso_nom
			from perso
			where perso_cod = personnage;
	if not found then
		code_retour := -1;
		return code_retour;
	end if; -- compt = 1
-- modif azaghal 02/03/2009 on impacte plus les chances d'esquives, pour les dext > 20
       if v_dex > 20 then
       valeur_esquive_init := (v_dex - 20)* 2;
       valeur_esquive_init := valeur_esquive_init + (v_dex * 5); 
       else
	valeur_esquive_init := v_dex * 5;
       end if;
/*******************************************/
/* Etape 1 : on calcule l esquive actuelle */	
/*******************************************/
	valeur_esquive := valeur_esquive + valeur_bonus(personnage, 'ESQ') + valeur_bonus(personnage, 'MAE');
        -- modif de az, en cas de compteur esquive à 0, le bonus n'etait pas pris en cpte.
        valeur_esquive_init := valeur_esquive_init + valeur_bonus(personnage, 'ESQ') + valeur_bonus(personnage, 'MAE');
	if nb_esquive = 0 then
		valeur_esquive := valeur_esquive_init;
	else
                       -- traitement du bonus du mage de bataille
               bonus_air := valeur_bonus(personnage, 'MUR');
	       if bonus_air != 0 then
                   temp_bonus_air := bonus_air / 100;
                   temp_bonus_air := nb_esquive - (nb_esquive * temp_bonus_air);
                     if temp_bonus_air < 1 then
                     temp_bonus_air := 1;
                     end if;
		   valeur_esquive := round(valeur_esquive_init /temp_bonus_air);
                else
                valeur_esquive := round(valeur_esquive_init / (1.5 * nb_esquive));   
                end if;  
	end if;
/******************************/
/* Etape 2 : on lance les des */	
/******************************/
        --  on regarde si la cible est bénie ou maudite
         bonmal = 0;
         malus = 0;
	 bonmal := valeur_bonus(personnage, 'BEN') + valeur_bonus(personnage, 'MAU');
	if bonmal <> 0 then
        des := lancer_des3(1,100,bonmal);
                else
        des := lancer_des(1,100);
        end if;
      
	if des >= 96 then -- echec critique
		code_retour := 0;
		texte_evt := '[perso_cod1] a joliment raté une esquive... ';
		update perso
			set perso_nb_esquive = nb_esquive + 2
			where perso_cod = personnage;
		insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_visible)
			values(nextval('seq_levt_cod'),8,now(),1,personnage,texte_evt,'N');
		return code_retour;
	end if; -- des
	
	if des <= 5 then -- réussite critique
		code_retour := 2;
		texte_evt := '[perso_cod1] a réussi une esquive parfaite. ';
		insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_visible)
			values(nextval('seq_levt_cod'),8,now(),1,personnage,texte_evt,'N');
		return code_retour;
	end if; -- des
	
	if des > valeur_esquive then
		if valeur_esquive_init <= getparm_n(1) then
		end if;
		code_retour := 0;
		update perso
			set perso_nb_esquive = nb_esquive + 1
			where perso_cod = personnage;
		texte_evt := '[perso_cod1] a raté une esquive... ';
		insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_visible)
			values(nextval('seq_levt_cod'),8,now(),1,personnage,texte_evt,'N');
		return code_retour;
	end if;
-- attaque critique	
-- explication : l'esquive est frocément loupée à ce niveau
--  on a déjà traité les esquives <= 5 plus haut	
	if qualite_attaque = 2 then
		code_retour := 0;
		update perso
			set perso_nb_esquive = nb_esquive + 1
			where perso_cod = personnage;
		texte_evt := '[perso_cod1] a raté une esquive sur une attaque critique ';
		insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_visible)
			values(nextval('seq_levt_cod'),8,now(),1,personnage,texte_evt,'N');
		return code_retour;
	end if;
-- attaque spéciale		
	if qualite_attaque = 1 then
		if des <= (valeur_esquive / 5) then -- réussite critique
			code_retour := 2;
                        if des <=5 then
			        texte_evt := '[perso_cod1] a réussi une esquive parfaite. ';
                        else  
                                update perso set perso_nb_esquive = nb_esquive + 1 where perso_cod = personnage;  
			        texte_evt := '[perso_cod1] a réussi une esquive sur une attaque spéciale. ';
                        end if;
			insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_visible)
				values(nextval('seq_levt_cod'),8,now(),1,personnage,texte_evt,'N');				
			return code_retour;
		else
			code_retour := 0;
			update perso
				set perso_nb_esquive = nb_esquive + 1
				where perso_cod = personnage;
			texte_evt := '[perso_cod1] a raté une esquive sur une attaque spéciale ';
			insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_visible)
				values(nextval('seq_levt_cod'),8,now(),1,personnage,texte_evt,'N');
			return code_retour;
		end if; -- reussite critique
	end if;
-- attaques normales
	if qualite_attaque = 0 then
		if des <=  5 then -- réussite critique
			code_retour := 2;
			texte_evt := '[perso_cod1] a réussi une esquive parfaite. ';
			insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_visible)
				values(nextval('seq_levt_cod'),8,now(),1,personnage,texte_evt,'N');					
			return code_retour;
		end if; -- reusiite critique
		code_retour := 1;
		update perso
			set perso_nb_esquive = nb_esquive + 1
			where perso_cod = personnage;
		texte_evt := '[perso_cod1] a réussi une esquive. ';
		insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_visible)
			values(nextval('seq_levt_cod'),8,now(),1,personnage,texte_evt,'N');				
		return code_retour;
	end if; -- des > esquive
end;
$function$

