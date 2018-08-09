CREATE OR REPLACE FUNCTION public.defi_creer(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction defi_creer                                       */
/*   Actions à exécuter lorsqu’un défi est instancié         */
/*   on passe en paramètres :                                */
/*   $1 = v_lanceur : le perso qui lance le défi             */
/*   $2 = v_cible : le perso qui reçoit le défi              */
/* Sortie de la forme n#message                              */
/*   n == statut : -1 (erreur) ou 0 (ok)                     */
/*   message == message d’erreur si nécessaire               */
/*************************************************************/
/* Créé le 09/01/2014                                        */
/*************************************************************/
declare
	v_lanceur alias for $1;   -- Le code de l’instance de mission
	v_cible alias for $2;     -- Le perso qui relève la mission

	v_actif text;             -- Perso actif ou non
	v_perso_type integer;     -- Le type de perso
	v_emplacement integer;    -- Le code de l’emplacement du défi
	v_resultat text;

begin
	select into v_resultat defi_possible(v_lanceur, v_cible);

	if v_resultat LIKE '-1%' then
		return v_resultat;
	end if;

	-- Vérification de la zone où le défi se résoudra
	select into v_emplacement zone_cod from defi_zone where zone_libre = 'O' limit 1;
	if not found then
		return '-1#Aucune arène pour relever votre défi n’est disponible ! Réessayez plus tard...';
	end if;

	-- On réserve l’emplacement
	update defi_zone set zone_libre = 'N' where zone_cod = v_emplacement;

	-- On crée le défi
	insert into defi (defi_lanceur_cod, defi_cible_cod, defi_zone_cod)
	values (v_lanceur, v_cible, v_emplacement);

	-- On place un événement
	perform insere_evenement(v_lanceur, v_cible, 95, '[attaquant] a défié [cible].', 'O', null);

	-- Et on termine;
	return v_resultat;
end;$function$

