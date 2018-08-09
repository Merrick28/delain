CREATE OR REPLACE FUNCTION public.cadeau_pn(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$declare
  code_retour text;
  personnage alias for $1;
  temp integer;
  v_num_mes integer;
  v_texte_mes text;
begin
  temp := cree_objet_perso_nombre(327,personnage,1);
  v_texte_mes := 'Heureux petits enfants bien sages,
Le Père Noël a été très satifsait de vous cette année. Vos petits souliers seront remplis pour vous récompenser de l''aide apportée suite à son ''petit accident''. Saine émulation et mythe méritocratique obligent, les plus gentils d''entre vous recevront un très gros cadeau. Les autres n''auront qu''à les envier pour tenter d''être parmi l''élite la prochaine fois.<br><br> Que l''esprit de Noël vous habite tous';
  v_num_mes := nextval('seq_msg_cod');
  insert into messages (msg_cod,msg_titre,msg_corps)
  values (v_num_mes,'Récompense....',v_texte_mes);
  insert into messages_exp (emsg_msg_cod,emsg_perso_cod) values (v_num_mes,721463);
  insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive) values (v_num_mes,personnage,'N','N');
  return 'OK';
end;


$function$

