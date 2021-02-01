DROP FUNCTION IF EXISTS public.trg_modif_perso() CASCADE ;

--
-- Name: trg_modif_perso(); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE FUNCTION public.trg_modif_perso() RETURNS trigger
    LANGUAGE plpgsql
    AS $$/******************************************************/
/* trigger modif_perso : à la modif de perso          */
/******************************************************/
declare
	personnage integer;
begin

	NEW.perso_lower_perso_nom = lower(NEW.perso_nom);

  -- dechevaucher la monture en cas de de changement de status du joureur ou de sa monture
	if NEW.perso_actif<>OLD.perso_actif and OLD.perso_actif='O' then

	    if NEW.perso_type_perso=1 and NEW.perso_monture is not NULL then
	        NEW.perso_monture := NULL ;  -- le perso déchevauche
	    end if;

	    if NEW.perso_type_perso=2 then
	        update perso set perso_monture=NULL where perso_monture=NEW.perso_cod ;	    -- la monture est déchevauchée
	    end if;

	end if;

	return NEW;
end;$$;


ALTER FUNCTION public.trg_modif_perso() OWNER TO delain;


--
-- Name: perso trg_modif_perso; Type: TRIGGER; Schema: public; Owner: delain
--

CREATE TRIGGER trg_modif_perso BEFORE UPDATE ON public.perso FOR EACH ROW EXECUTE PROCEDURE public.trg_modif_perso();

