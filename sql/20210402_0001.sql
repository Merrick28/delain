ALTER TABLE public.terrain
   ADD COLUMN ter_msg_inaccessible character varying(256);
COMMENT ON COLUMN public.terrain.ter_msg_inaccessible
  IS 'Message pour le joueur lors qu''il se retouve sur un terrain normalement inacessible (en général à la mort de sa monture).';


update terrain set ter_msg_inaccessible='[cible] s’enfonce dans la boue profonde qui le submerge et l’asphyxie.' where ter_nom='boue';
update terrain set ter_msg_inaccessible='[cible] s’ecorche à sang dans ses cailloux saillants.' where ter_nom='caillou';
update terrain set ter_msg_inaccessible='[cible] tombe en chute libre et perd connaissance.' where ter_nom='vent';
update terrain set ter_msg_inaccessible='Le froid engourdi les membres gelés de [cible].' where ter_nom='glace';
update terrain set ter_msg_inaccessible='Des plantes dangereuses se cachent dans l’herbe, en marchant dessus [cible] se retouve empoisonné.' where ter_nom='herbe';
update terrain set ter_msg_inaccessible='La forêt abrite de nombreux animaux parasites qui s’attaquent à [cible].' where ter_nom='foret';
update terrain set ter_msg_inaccessible='[cible] s’ecorche sur cette clotûre particulièrement dangeureuse.'  where ter_nom='clôture';
update terrain set ter_msg_inaccessible='L’eau est profonde, [cible] perd pied et s’y noit.' where ter_nom='eau';
update terrain set ter_msg_inaccessible='Les émanations qui s’échappent du marrais empoisonnent [cible].' where ter_nom='marais';
update terrain set ter_msg_inaccessible='[cible] meure de soif dans ce désert.' where ter_nom='désert';
update terrain set ter_nom='obscur', ter_desc='obscur' where ter_nom='obscur ';
update terrain set ter_msg_inaccessible='L’obscurité flétrie l’ame de [cible].' where ter_nom='obscur';
update terrain set ter_msg_inaccessible='[cible] s’écorche sur cette barrière particulièrement dangeureuse.' where ter_nom='barrière';
update terrain set ter_msg_inaccessible='Le feu inflige de lourdes brûlure à [cible].' where ter_nom='feu';
update terrain set ter_msg_inaccessible='Le terrain cache de nombreux pièges mortels, [cible] tombe dans l’un d’eux.' where ter_nom='terre';



update public.type_ia set ia_nom='Monture docile', ia_fonction='ia_monture([perso], 0)' where ia_type=17 ;
update public.type_ia set ia_nom='Monture à ordre', ia_fonction='ia_monture([perso], 1)' where ia_type=18 ;
update public.type_ia set ia_nom='Monture mixte', ia_fonction='ia_monture([perso], 2)' where ia_type=19 ;
delete from public.type_ia where ia_type in (20, 21) ;

INSERT INTO public.competences(  comp_typc_cod, comp_libelle, comp_modificateur, comp_connu) VALmUES ( 2, 'Equitation', 0, 'N');
