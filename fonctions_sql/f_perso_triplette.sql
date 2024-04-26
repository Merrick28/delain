
--
-- Name: f_perso_triplette(integer, boolean); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_perso_triplette(integer, boolean default true) RETURNS int[]
    LANGUAGE plpgsql
    AS $$/*************************************************/
/* fonction f_perso_triplette                      */
/* retourne la liste des persos de la triplette  */
/*************************************************/
declare
  v_perso_cod alias for $1;
  v_fam_inclus alias for $2;

  triplette int[];
  v_perso record ;

begin

	triplette := array[]::int[] ;

	for v_perso in
		select perso.perso_cod, perso.perso_type_perso from perso_compte c
		inner join perso_compte ct on ct.pcompt_compt_cod=c.pcompt_compt_cod
		inner join perso on perso_cod=ct.pcompt_perso_cod and perso_type_perso=1
		where c.pcompt_perso_cod=v_perso_cod

		union

		select familier.perso_cod, familier.perso_type_perso from perso_compte c
		inner join perso_compte ct on ct.pcompt_compt_cod=c.pcompt_compt_cod
		inner join perso on perso_cod=ct.pcompt_perso_cod and perso_type_perso=1
		inner join perso_familier on pfam_perso_cod=perso_cod
		inner join perso as familier on familier.perso_cod=pfam_familier_cod and familier.perso_type_perso=3 and familier.perso_actif='O'
		where c.pcompt_perso_cod=v_perso_cod
	loop
	  if v_fam_inclus or v_perso.perso_type_perso = 1 then
		    triplette := triplette || array[v_perso.perso_cod] ;
    end if;
	end loop;

	RETURN triplette ;

end;$$;


ALTER FUNCTION public.f_perso_triplette(integer, boolean) OWNER TO delain;
COMMENT ON FUNCTION f_perso_triplette(integer, boolean) IS 'Retourne la liste des persos de la triplette.';



