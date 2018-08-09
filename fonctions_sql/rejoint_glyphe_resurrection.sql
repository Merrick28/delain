CREATE OR REPLACE FUNCTION public.rejoint_glyphe_resurrection(integer)
 RETURNS integer
 LANGUAGE plpgsql
 STRICT
AS $function$/*****************************************************************/
/* function rejoint_glyphe_resurrection :                        */
/*  si le perso a un glyphe de résurrection, on le supprime et   */
/*  retourne la position libre la plus proche du glyphe          */
/* On passe en paramètres                                        */
/*   $1 = perso                                                  */
/* Le code sortie est la nouvelle position après la mort, ou -1  */
/*****************************************************************/
/* Créé le 30/01/2011                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	code_retour integer;               -- nouvelle position
-------------------------------------------------------------
-- variables concernant le lanceur  
-------------------------------------------------------------
	v_perso alias for $1;		-- perso_cod du décédé
	v_glyphe	integer;	-- obj_cod du glyphe
	v_distance	integer;	--distance au glyphe
	v_pos_glyphe	integer;	--Position du glyphe
	v_etage		integer;
	v_x		integer;
	v_y		integer;
	etage_gm	integer;	-- Valeur de l’étage du garde manger

begin
	-- Initialisation
	etage_gm	:= 90;

	select into v_glyphe pglyphe_obj_cod 
	from perso_glyphes 
	where pglyphe_perso_cod = v_perso
		and pglyphe_type = 'R';
	if not found then
 		return -1;
	end if;
    
	-- On récupère la position du glyphe au sol.
	select into v_pos_glyphe pobj_pos_cod from objet_position
		where v_glyphe = pobj_obj_cod;
	if not found then
		-- On cherche dans les inventaires
		select into v_pos_glyphe ppos_pos_cod 
		from perso_position, perso_objets
		where ppos_perso_cod = perobj_perso_cod
			and perobj_obj_cod = v_glyphe;
		if not found then
			-- Impossible que l''objet soit perdu, mais on ne sait jamais
			return -1;
		end if;
	end if;
    
	-- On a la position du glyphe.
	-- On rejoint la position
	select into v_etage, v_x, v_y pos_etage, pos_x, pos_y from positions
	where pos_cod = v_pos_glyphe;

	-- Si le glyphe est au garde-manger, il est inaccessible
	if v_etage = etage_gm then
		return -1;
	end if;

	v_distance := 0;
	loop 
		select into code_retour pos_cod from positions
		where pos_etage = v_etage 
			and abs(pos_x - v_x) <= v_distance and abs(pos_y - v_y) <= v_distance
			and not exists (select 1 from murs where mur_pos_cod = pos_cod );
		if found then
			-- Trouvé une position
			return code_retour; 
		end if;
		if v_distance < 10 then 
			v_distance := v_distance + 1; 
		else 
			-- Pas de position libre à moins de 10 cases. Glyphe non atteignable.
			return -1;
		end if;
	end loop;
    
	-- On n''arrive jamais ici.
	return -1;
end;
$function$

