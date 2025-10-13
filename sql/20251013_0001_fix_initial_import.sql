--
-- Name: perso_objets f_trg_after_delete_perso_objet; Type: TRIGGER; Schema: public; Owner: delain
--

CREATE TRIGGER f_trg_after_delete_perso_objet AFTER DELETE ON public.perso_objets FOR EACH ROW EXECUTE PROCEDURE public.f_trg_after_delete_perso_objet();

--
-- Name: perso_objets f_trg_after_update_perso_objet; Type: TRIGGER; Schema: public; Owner: delain
--

CREATE TRIGGER f_trg_after_update_perso_objet AFTER UPDATE ON public.perso_objets FOR EACH ROW EXECUTE PROCEDURE public.f_trg_after_update_perso_objet();
