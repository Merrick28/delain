
--
-- Name: mort_gm(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION mort_gm(integer) RETURNS void
    LANGUAGE plpgsql
    AS $_$/* *********************************************** */
/* Fonction mort_gm                                */
/*                                                 */
/* le perso vient de mourrir, s'il était dans le   */
/*  Garde Manger, on l'en sort pour une mort       */
/*  normale                                        */
/*                                                 */
/* Paramètres :                                    */
/*   $1 = perso_cod                                */
/* *********************************************** */
/* Créé le 08/11/2019 (Marlyza)                    */
/* *********************************************** */

declare
	v_perso_cod		alias for $1;	  -- Numéro de perso
	gm_pos			  integer;	      -- position du sac
	v_back_pos		integer;	      -- Position de retour
	v_pos			    integer;	      -- position actuelle du perso
	texte_evt	    text;		        -- Texte à placer dans les évènements
begin
	-- Initialisation (position du GM)
	gm_pos := 152387;

	-- Récupération des données du sac
	select into v_back_pos pgm_pos_cod from	perso_gmanger where	pgm_perso_cod = v_perso_cod;
	if found then

	  -- Le perso étaitn dans le sac, on l'en sort !
     delete from	perso_gmanger where	pgm_perso_cod = v_perso_cod;

    -- Récupération des données sur le perso (on s'assure qu'il était bien dans le sac (il aurait pu être teleporté par un admin de façon sauvage)
    select into v_pos ppos_pos_cod from	perso_position where	ppos_perso_cod = v_perso_cod;
    if v_pos = gm_pos then
      -- on ressort le perso là où il a été attrapé
      update perso_position set	ppos_pos_cod = v_back_pos where	ppos_perso_cod = v_perso_cod;

      -- MaJ des évènements
      texte_evt := '[perso_cod1] est sorti du sac par son bourreau car il n''est plus très très frais!';
      insert into ligne_evt( levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant)
      values(nextval('seq_levt_cod'), 79, now(), 1, v_perso_cod, texte_evt, 'O', 'O', v_perso_cod );

    end if;

	end if;

end;$_$;

ALTER FUNCTION public.mort_gm(integer) OWNER TO delain;