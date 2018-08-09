CREATE OR REPLACE FUNCTION public.poissons(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*********************************/
/* poissons                      */
/*********************************/
declare
	code_retour text;
	personnage alias for $1;
	nb_pois 	alias for $2;
	duree integer;
	num_mes integer;
	texte_mes text;
	temp integer;
begin
	select into temp perso_cod
	from perso
	where perso_cod = personnage;
	if found then
	-- titre
	insert into perso_titre (ptitre_perso_cod,ptitre_titre,ptitre_date)
		values
		(personnage,'Pue le poisson pourri',now());
	-- malus
	delete from bonus
		where bonus_perso_cod = personnage
		and bonus_tbonus_libc in ('POI','PAA','VUE');
	duree := nb_pois + 2;
	insert into bonus
		(bonus_perso_cod,bonus_tbonus_libc,bonus_valeur,bonus_nb_tours)
		values
		(personnage,'POI',8,duree);
	insert into bonus
		(bonus_perso_cod,bonus_tbonus_libc,bonus_valeur,bonus_nb_tours)
		values
		(personnage,'PAA',2,duree);
	insert into bonus
		(bonus_perso_cod,bonus_tbonus_libc,bonus_valeur,bonus_nb_tours)
		values
		(personnage,'VUE',-10,duree);
	num_mes := nextval('seq_msg_cod');
	texte_mes := 'Vous avedz accumulé des poissons dans votre inventaire.<br>L''odeur vous monte à la tête, vous tombez malade....';
	insert into messages (msg_cod,msg_titre,msg_corps)
		values
		(num_mes,'Poissons....',texte_mes);
	insert into messages_exp (emsg_msg_cod,emsg_perso_cod)
		values
		(num_mes,58);
	insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
		values
		(num_mes,personnage,'N','N');
		
	return 'OK';
else
	return 'KKKOOOO';
end if;
end;
		
		$function$

