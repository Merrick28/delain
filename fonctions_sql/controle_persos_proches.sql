
--
-- Name: controle_persos_proches(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE or replace FUNCTION controle_persos_proches(integer, integer, integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $_$/*************************************************************/
/* fonction controle_persos_proches                          */
/*   Indique si deux persos sont à moins de X cases, en      */
/*     en compte un niveau d’escaliers                       */
/*   Sortie : Booléen                                        */
/*************************************************************/
/* Créé le 19/02/2013                                        */
/*************************************************************/
declare
	perso_cod1 alias for $1;  -- Premier perso
	perso_cod2 alias for $2;  -- Deuxième perso
	distance_demandee alias for $3; -- Distance par rapport à laquelle vérifier la proximité
	temp integer;             -- Variable de calcul
	pos_x1 integer;           -- Le pos_x du premier perso
	pos_x2 integer;           -- Le pos_x du deuxième perso
	pos_y1 integer;           -- Le pos_y du premier perso
	pos_y2 integer;           -- Le pos_y du deuxième perso
	etage1 integer;           -- L’étage du premier perso
	etage2 integer;           -- L’étage du second perso

begin
	-- Initialisation Perso 1
	select into pos_x1, pos_y1, etage1
		pos_x, pos_y, pos_etage
	from perso_position
	inner join positions on pos_cod = ppos_pos_cod
	where ppos_perso_cod = perso_cod1;

	-- Initialisation Perso 2
	select into pos_x2, pos_y2, etage2
		pos_x, pos_y, pos_etage
	from perso_position
	inner join positions on pos_cod = ppos_pos_cod
	where ppos_perso_cod = perso_cod2;

	-- Si les persos sont au même étage et sont très près les uns des autres
	if etage1 = etage2 and ABS(pos_x1 - pos_x2) < distance_demandee and ABS(pos_y1 - pos_y2) < distance_demandee then
		return true;
	end if;

	-- Si les persos ne sont pas directement près, on vérifie les passages dans le sens Perso1 => Perso2.
	select into temp
		1
	from lieu
	inner join lieu_position on lpos_lieu_cod = lieu_cod
	inner join positions dep on dep.pos_cod = lpos_pos_cod
	inner join positions arr on arr.pos_cod = lieu_dest
	where dep.pos_etage = etage1
		and abs(dep.pos_x - pos_x1) < distance_demandee
		and abs(dep.pos_y - pos_y1) < distance_demandee
		and arr.pos_etage = etage2
		and abs(arr.pos_x - pos_x2) < distance_demandee
		and abs(arr.pos_y - pos_y2) < distance_demandee
	limit 1;
	if found then
		return true;
	end if;

	-- Puis on vérifie les passages dans le sens Perso2 => Perso1.
	select into temp
		1
	from lieu
	inner join lieu_position on lpos_lieu_cod = lieu_cod
	inner join positions dep on dep.pos_cod = lpos_pos_cod
	inner join positions arr on arr.pos_cod = lieu_dest
	where dep.pos_etage = etage2
		and abs(dep.pos_x - pos_x2) < distance_demandee
		and abs(dep.pos_y - pos_y2) < distance_demandee
		and arr.pos_etage = etage1
		and abs(arr.pos_x - pos_x1) < distance_demandee
		and abs(arr.pos_y - pos_y1) < distance_demandee
	limit 1;
	if found then
		return true;
	end if;

	return false;
end;$_$;


ALTER FUNCTION public.controle_persos_proches(integer, integer, integer) OWNER TO delain;

--
-- Name: FUNCTION controle_persos_proches(integer, integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION controle_persos_proches(integer, integer, integer) IS 'Contrôle la proximité de deux personnages';