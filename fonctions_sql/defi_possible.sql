CREATE OR REPLACE FUNCTION public.defi_possible(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction defi_possible                                    */
/*   Vérifie qu’un défi est possible entre deux protagonistes*/
/*   on passe en paramètres :                                */
/*   $1 = v_lanceur : le perso qui lance le défi             */
/*   $2 = v_cible : le perso qui reçoit le défi              */
/* Sortie de la forme n#message                              */
/*   n == statut : -1 (erreur) ou 0 (avertissement)          */
/*                 ou 1 (ok)                                 */
/*   message == message d’erreur si nécessaire               */
/*************************************************************/
/* Créé le 09/01/2014                                        */
/*************************************************************/
declare
	v_lanceur alias for $1;   -- Le code de l’instance de mission
	v_cible alias for $2;     -- Le perso qui relève la mission

	v_actif text;             -- Perso actif ou non
	v_perso_type integer;     -- Le type de perso
	v_compte_l integer;       -- Le compte du lanceur
	v_compte_c integer;       -- Le compte de la cible
	v_resultat text;

begin
	-- Par défaut, tout est OK.
	v_resultat := '1#';

	-- Vérification : les personnages existent et sont actifs (pour le lanceur, c’est très probable, mais bon...)
	select into v_actif, v_perso_type, v_compte_l perso_actif, perso_type_perso, pcompt_compt_cod
	from perso
	inner join perso_compte on pcompt_perso_cod = perso_cod
	where perso_cod = v_lanceur;
	if not found or v_actif <> 'O' or v_perso_type = 3 then
		return '-1#Le lanceur du défi est en hibernation, ou n’existe pas, ou est un familier.';
	end if;

	-- Pour la cible, on est un peu plus précis, car l’erreur a une réelle valeur ajoutée pour le lanceur.
	select into v_actif, v_perso_type, v_compte_c perso_actif, perso_type_perso, pcompt_compt_cod
	from perso
	inner join perso_compte on pcompt_perso_cod = perso_cod
	where perso_cod = v_cible;
	if not found then
		return '-1#La cible du défi n’existe pas.';
	end if;
	if v_actif = 'N' then
		return '-1#La cible du défi est définitivement décédée.';
	end if;
	if v_actif <> 'O' then
		return '-1#La cible du défi est en hibernation.';
	end if;
	if v_perso_type = 3 then
		return '-1#Un défi ne peut pas cible un familier.';
	end if;
	if v_compte_c = v_compte_l then
		return '-1#Un défi ne peut pas opposer deux personnages d’un même compte.';
	end if;

	-- Vérification de la présence d’une des deux parties dans un autre défi
	if exists(select 1 from defi where v_lanceur IN (defi_lanceur_cod, defi_cible_cod) AND defi_statut < 2) then
		return '-1#Vous devez d’abord répondre aux défis dans lesquels vous êtes impliqués.';
	end if;
	if exists(select 1 from defi where v_cible IN (defi_lanceur_cod, defi_cible_cod) AND defi_statut < 2) then
		return '-1#Votre adversaire est actuellement impliqué dans un défi.';
	end if;

	-- Vérification qu’on n’est pas en train de harceler une victime
	if exists (select 1 from defi where defi_lanceur_cod = v_lanceur and defi_cible_cod = v_cible and (now() - defi_date_debut) < '5 days'::interval)
		and not exists (select 1 from defi where defi_lanceur_cod = v_cible and defi_cible_cod = v_lanceur and (now() - defi_date_debut) < '5 days'::interval)
	then
		return '-1#Vous avez déjà défié votre adversaire dans les 5 derniers jours. Laissez-le souffler !';
	end if;

	-- Vérification des ripostes (légitimes défenses)
	if exists(select 1 from riposte where v_lanceur IN (riposte_attaquant, riposte_cible) AND riposte_nb_tours < 2) then
		return '-1#Vous êtes engagé en combat, et ne pouvez pas vous en soustraire pour participer à un défi.';
	end if;
	if exists(select 1 from riposte where v_cible IN (riposte_attaquant, riposte_cible) AND riposte_nb_tours < 2) then
		v_resultat := '0#Votre cible est engagée en combat. ';
		v_resultat := v_resultat || 'Elle pourra donc ne pas être en mesure de répondre à votre défi dans le temps imparti. ';
	end if;

	-- Et on termine;
	return v_resultat;
end;$function$

