CREATE OR REPLACE FUNCTION public.decouvre_cachette(integer, numeric, numeric)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/************************************************/
/* Découverte d'une cachette                    */
/* les monstres ne sont pas affectés            */
/* 14/05/2006                                   */
/* perso : $1                                   */
/* Difficulté dex à trouver la cachette : $2    */
/* Difficulté int à trouver la cachette : $3    */
/************************************************/

declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	code_retour text;			-- texte pour action
	texte_evt text;				-- texte pour évènements
-------------------------------------------------------------
-- variables concernant le perso
-------------------------------------------------------------
	personnage alias for $1;        -- perso_cod du perso
	type_perso integer;             -- perso_type_perso du perso ou monstre
	pos_perso integer;		-- position du perso
	race integer;							-- race du perso, pour rajouter les bonus de race
	intel integer;							-- intelligence du perso
	dex integer; 							-- dextérité du perso
	v_bonus_decouvre integer; 					-- bonus pour découvrir une cachette
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	dif_dex alias for $2;			-- difficulté de découvrir une cachette par sa dextérité
	dif_int alias for $3;			-- difficulté de découvrir une cachette par son intelligence
	race_fac numeric;								-- facteur de modification de la race
	chance numeric;						-- chance de trouver la cachette
	cache_num integer;				-- numéro de la cachette
	cache_connue integer;			-- connaissance antérieure de cette cachette
	des integer; 				-- correspond à l'aléatoire


begin
		code_retour := '';
		select into type_perso,race,intel,dex
			perso_type_perso,perso_race_cod,perso_int,perso_dex
			from perso
			where perso_cod = personnage;
	if not found then
		return 'soucis sur la sélection de type de perso !';
	end if;
	select into cache_connue
					persocache_cache_cod
					from cachettes_perso,cachettes,perso_position
					where ppos_perso_cod = personnage
					and ppos_perso_cod = persocache_perso_cod
					and persocache_cache_cod = cache_cod	
					and cache_pos_cod = ppos_pos_cod; 
									

                
  if cache_connue != 0 then
    							--	le perso a déjà visité cette cachette auparavant
			return 'Vous vous souvenez être déjà passé par là. La dernière fois, vous aviez découvert une cachette. <br>Peut-être celle ci renferme t''elle à nouveau des trésors ? <br> <a href="cachette.php">Souhaitez vous l''explorer à nouveau ?</a><br>';
	end if;
	if type_perso = 2             --On teste si il s'agit d'un monstre

			then return 'Sans conséquence';
	else
												-- Un perso vient de rentrer sur la case
                         -- on teste si il découvre quelque chose en fonction des paramètres intelligence, dextérité, race
			    if race = 1 then
				    	race_fac = 1;
				    elsif race = 2 then
				    	race_fac = 0.8;
				    elsif race = 3 then
				    	race_fac = 1.2;
			    end if;
			   		select into cache_num
							cache_cod
							from cachettes,perso_position
							where ppos_perso_cod = personnage
							and cache_pos_cod = ppos_pos_cod;
							if found then
				    chance = (((intel*intel*intel*dif_int)+(dex*dex*dex*dif_dex))/500)*race_fac;
				    -- Bonus de découverte
					    	chance := chance + valeur_bonus(personnage, 'CAC');

						des := lancer_des(1,100);
						if des < chance then
							insert into cachettes_perso values (cache_num,personnage);
							code_retour := code_retour||'Un évènement insolite vient de se produire. En arrivant dans cet endroit, vous n''aviez pas remarqué cet interstice. <br>Pourtant, cela semble contenir quelque chose. En vous glissant à l''intérieur, vous pourriez surement assouvir votre curiosité.	<br> <a href="cachette.php">Souhaitez vous vous glisser à l''intérieur ?</a><br>';

						end if;
					end if;

		end if;
		return code_retour;
end;$function$

