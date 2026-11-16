-- Compétence #105
INSERT INTO public.competences(  comp_typc_cod, comp_libelle, comp_modificateur, comp_connu, comp_nb_util_tour)
    VALUES ( 19, 'Frappe Tellurique', 0, 'N', 2);


INSERT INTO public.type_evt( tevt_libelle, tevt_texte) VALUES (' Frappe Tellurique', '[cible] a touché par la frappe tellurique de [attaquant]');