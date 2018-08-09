CREATE OR REPLACE FUNCTION public.trajectoire_magie(integer, integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/**************************************************/
/* fonction trajectoire : calcule une trajectoire */
/*  pour une arme à distance                      */
/* on passe en paramètres :                       */
/*  $1 = pos_cod 1                                */
/*  $2 = pos_cod 2                                */
/*  $3 = perso_cod cible                          */
/*  $4 = perso_cod du lanceur                     */
/* on a en retour une chaine séparée par ;        */
/*  pos0 = 0 tout est OK, la cible est atteinte   */
/*  pos0 = 1 on atteint un mur (pos_cod en 1)     */
/*  pos0 = 2 on atteint un autre perso            */
/*      (perso_cod en 1)                          */
/**************************************************/
/* créé le 15/09/2003                             */
/**************************************************/
declare
	code_retour text;
	pos1 alias for $1;
	pos2 alias for $2;
	v_cible alias for $3;
	personnage alias for $4;
	temp_pos integer;
	diff_x integer;
	diff_y integer;
	temp_diff_x integer;
	temp_diff_y integer;
	ordre integer;
	etage1 integer;
	etage2 integer;
	distance_init integer;
	v_distance integer;
	temp_distance integer;
	r_pos record;
	compteur integer;
	nb_mur integer;
	nb_perso integer;
	toucher_intermediaire integer;
	temp_perso integer;
	nv_cible integer;
	compteur2 integer;
	v_modif_change_cible integer;
	v_x integer;
	v_y integer;
	v_etage integer;
	tangible text;
	
begin
	v_modif_change_cible := modif_change_cible(personnage);
	code_retour := '0;0;';
	distance_init := distance(pos1,pos2);
	v_distance := distance_init;
	temp_pos := pos1;
	diff_x := delta_x(pos1,pos2);
	diff_y := delta_y(pos1,pos2);
	select into etage1 pos_etage from positions
		where pos_cod = pos1;
	select into etage2 pos_etage from positions
		where pos_cod = pos2;
	if (etage1 != etage2) then
		code_retour := '9;0;';
		return code_retour;
	end if;
	compteur := 0;
	while (v_distance != 0) loop
		select into v_x,v_y,v_etage pos_x,pos_y,pos_etage
			from positions
			where pos_cod = temp_pos;
		compteur := compteur + 1;
		exit when compteur >= 30;
		for r_pos in select * from positions
			where pos_x >= (v_x - 1) and pos_x <= (v_x + 1)
			and pos_y >= (v_y - 1) and pos_y <= (v_y + 1)
			and pos_etage = etage1 loop
			temp_distance := distance(r_pos.pos_cod, pos2);		
			if (temp_distance < v_distance) then -- on s approche
				temp_diff_x := delta_x(r_pos.pos_cod,pos2);
				temp_diff_y := delta_y(r_pos.pos_cod,pos2);
				if ((temp_diff_x <= diff_x) and (temp_diff_y <= diff_y)) then -- ok pour la position suivante
					-- maintenant, on fait les tests qui vont bien
					-- 1 => les murs
					select into tangible,nb_mur mur_tangible,mur_pos_cod from murs
						where mur_pos_cod = r_pos.pos_cod;
					if found then
						if tangible = 'O' then
							code_retour := '1;'||trim(to_char(nb_mur,'99999'))||';';
							return code_retour;
						end if;
					end if;
					temp_pos := r_pos.pos_cod;
					v_distance := temp_distance;
					diff_x := temp_diff_x;
					diff_y := temp_diff_y;
				end if;
			end if;
		end loop;
	end loop;
	if compteur >= 30 then
		code_retour := 'erreur';
	end if;
	return code_retour;	
end;
$function$

