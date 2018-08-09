CREATE OR REPLACE FUNCTION public.load_base_test(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* alimente une base vierge en compte et perso */
/*****************************************************************/
declare
	code_retour text;
	race_perso integer;
	c_admin integer;
	c_monstre integer;
	c_test0 integer;
	c_test1 integer;
	c_test2 integer;
	c_test3 integer;
	c_test4 integer;
	c_test5 integer;
	c_test6 integer;
	c_test7 integer;
	c_test8 integer;
	c_test9 integer;
	c_test10 integer;
	c_test11 integer;
	c_test12 integer;
	c_test13 integer;
	c_test14 integer;
	c_test15 integer;
	p_test0_archer integer;
	p_test0_mage integer;
	p_test0_cac integer;
	p_test1_archer integer;
	p_test1_mage integer;
	p_test1_cac integer;
	p_test2_archer integer;
	p_test2_mage integer;
	p_test2_cac integer;
	p_test3_archer integer;
	p_test3_mage integer;
	p_test3_cac integer;
	p_test4_archer integer;
	p_test4_mage integer;
	p_test4_cac integer;
	p_test5_archer integer;
	p_test5_mage integer;
	p_test5_cac integer;
	p_test6_archer integer;
	p_test6_mage integer;
	p_test6_cac integer;
	p_test7_archer integer;
	p_test7_mage integer;
	p_test7_cac integer;
	p_test8_archer integer;
	p_test8_mage integer;
	p_test8_cac integer;
	p_test9_archer integer;
	p_test9_mage integer;
	p_test9_cac integer;
	p_test10_archer integer;
	p_test10_mage integer;
	p_test10_cac integer;
	p_test11_archer integer;
	p_test11_mage integer;
	p_test11_cac integer;
	p_test12_archer integer;
	p_test12_mage integer;
	p_test12_cac integer;
	p_test13_archer integer;
	p_test13_mage integer;
	p_test13_cac integer;
	p_test14_archer integer;
	p_test14_mage integer;
	p_test14_cac integer;
	p_test15_archer integer;
	p_test15_mage integer;
	p_test15_cac integer;
	compt_comp perso_competences%rowtype;
	ligne_competences record;
	personnage record;
	modif_type integer;
	modif_competences competences.comp_modificateur%type;
        coterie_0 integer;
        coterie_1 integer;
        coterie_2 integer;
        coterie_3 integer;
        coterie_4 integer;
        coterie_5 integer;
        coterie_6 integer;
        coterie_7 integer;
        coterie_8 integer;
        coterie_9 integer;
        coterie_10 integer;
        coterie_11 integer;
        coterie_12 integer;
        coterie_13 integer;
        coterie_14 integer;
        coterie_15 integer;

begin
	/**************************************/
	/* Etape 1                            */
	/* Delete                             */
	/**************************************/
delete from compt_droit;
delete from perso_compte;
delete from compte;
delete from perso_position;
delete from perso_competences;
delete from perso_sorts;
delete from perso_nb_sorts;
delete from perso_nb_sorts_total;
delete from perso_titre;
delete from etage_visite;
delete from perso;

	/**************************************/
	/* Etape 2                            */
	/* compte                 */
	/**************************************/
INSERT INTO compte VALUES (nextval('seq_compt_cod'), 'admin', '', 'a.a@a.com', 774, 'O', 0, '2017-12-07 20:11:03.374896+00', '2017-12-07 20:11:03.374896+00', '78.248.5.156', NULL, NULL, 'N', 'N', 'O', NULL, NULL, 'O', 'O', NULL, 'N', 0, 0, 1, 0, 1, NULL, NULL, 'O', NULL, NULL, NULL, NULL, 'O', 5, '2017-12-07 20:11:03.374896', 2, NULL, NULL, 0, NULL, NULL, '$1$8KzpRGnE$Oy3TJRmY/oH5VXkiUNgJj.');
INSERT INTO compte VALUES (nextval('seq_compt_cod'), 'monstre', '', 'a.a@a.com', 774, 'O', 0, '2017-12-07 20:11:03.374896+00', '2017-12-07 20:11:03.374896+00', '78.248.5.156', NULL, NULL, 'O', 'N', 'N', NULL, NULL, 'O', 'O', NULL, 'N', 0, 0, 1, 0, 1, NULL, NULL, 'O', NULL, NULL, NULL, NULL, 'O', 5, '2017-12-07 20:11:03.374896', 2, NULL, NULL, 0, NULL, NULL, '$1$8KzpRGnE$Oy3TJRmY/oH5VXkiUNgJj.');
INSERT INTO compte VALUES (nextval('seq_compt_cod'), 'test0', '', 'a.a@a.com', 774, 'O', 0, '2017-12-07 20:11:03.374896+00', '2017-12-07 20:11:03.374896+00', '78.248.5.156', NULL, NULL, 'N', 'O', 'N', NULL, NULL, 'O', 'O', NULL, 'N', 0, 0, 1, 0, 1, NULL, NULL, 'O', NULL, NULL, NULL, NULL, 'O', 5, '2017-12-07 20:11:03.374896', 2, NULL, NULL, 0, NULL, NULL, '$1$8KzpRGnE$Oy3TJRmY/oH5VXkiUNgJj.');
INSERT INTO compte VALUES (nextval('seq_compt_cod'), 'test1', '', 'a.a@a.com', 774, 'O', 0, '2017-12-07 20:11:03.374896+00', '2017-12-07 20:11:03.374896+00', '78.248.5.156', NULL, NULL, 'N', 'O', 'N', NULL, NULL, 'O', 'O', NULL, 'N', 0, 0, 1, 0, 1, NULL, NULL, 'O', NULL, NULL, NULL, NULL, 'O', 5, '2017-12-07 20:11:03.374896', 2, NULL, NULL, 0, NULL, NULL, '$1$8KzpRGnE$Oy3TJRmY/oH5VXkiUNgJj.');
INSERT INTO compte VALUES (nextval('seq_compt_cod'), 'test2', '', 'a.a@a.com', 774, 'O', 0, '2017-12-07 20:11:03.374896+00', '2017-12-07 20:11:03.374896+00', '78.248.5.156', NULL, NULL, 'N', 'O', 'N', NULL, NULL, 'O', 'O', NULL, 'N', 0, 0, 1, 0, 1, NULL, NULL, 'O', NULL, NULL, NULL, NULL, 'O', 5, '2017-12-07 20:11:03.374896', 2, NULL, NULL, 0, NULL, NULL, '$1$8KzpRGnE$Oy3TJRmY/oH5VXkiUNgJj.');
INSERT INTO compte VALUES (nextval('seq_compt_cod'), 'test3', '', 'a.a@a.com', 774, 'O', 0, '2017-12-07 20:11:03.374896+00', '2017-12-07 20:11:03.374896+00', '78.248.5.156', NULL, NULL, 'N', 'O', 'N', NULL, NULL, 'O', 'O', NULL, 'N', 0, 0, 1, 0, 1, NULL, NULL, 'O', NULL, NULL, NULL, NULL, 'O', 5, '2017-12-07 20:11:03.374896', 2, NULL, NULL, 0, NULL, NULL, '$1$8KzpRGnE$Oy3TJRmY/oH5VXkiUNgJj.');
INSERT INTO compte VALUES (nextval('seq_compt_cod'), 'test4', '', 'a.a@a.com', 774, 'O', 0, '2017-12-07 20:11:03.374896+00', '2017-12-07 20:11:03.374896+00', '78.248.5.156', NULL, NULL, 'N', 'O', 'N', NULL, NULL, 'O', 'O', NULL, 'N', 0, 0, 1, 0, 1, NULL, NULL, 'O', NULL, NULL, NULL, NULL, 'O', 5, '2017-12-07 20:11:03.374896', 2, NULL, NULL, 0, NULL, NULL, '$1$8KzpRGnE$Oy3TJRmY/oH5VXkiUNgJj.');
INSERT INTO compte VALUES (nextval('seq_compt_cod'), 'test5', '', 'a.a@a.com', 774, 'O', 0, '2017-12-07 20:11:03.374896+00', '2017-12-07 20:11:03.374896+00', '78.248.5.156', NULL, NULL, 'N', 'O', 'N', NULL, NULL, 'O', 'O', NULL, 'N', 0, 0, 1, 0, 1, NULL, NULL, 'O', NULL, NULL, NULL, NULL, 'O', 5, '2017-12-07 20:11:03.374896', 2, NULL, NULL, 0, NULL, NULL, '$1$8KzpRGnE$Oy3TJRmY/oH5VXkiUNgJj.');
INSERT INTO compte VALUES (nextval('seq_compt_cod'), 'test6', '', 'a.a@a.com', 774, 'O', 0, '2017-12-07 20:11:03.374896+00', '2017-12-07 20:11:03.374896+00', '78.248.5.156', NULL, NULL, 'N', 'O', 'N', NULL, NULL, 'O', 'O', NULL, 'N', 0, 0, 1, 0, 1, NULL, NULL, 'O', NULL, NULL, NULL, NULL, 'O', 5, '2017-12-07 20:11:03.374896', 2, NULL, NULL, 0, NULL, NULL, '$1$8KzpRGnE$Oy3TJRmY/oH5VXkiUNgJj.');
INSERT INTO compte VALUES (nextval('seq_compt_cod'), 'test7', '', 'a.a@a.com', 774, 'O', 0, '2017-12-07 20:11:03.374896+00', '2017-12-07 20:11:03.374896+00', '78.248.5.156', NULL, NULL, 'N', 'O', 'N', NULL, NULL, 'O', 'O', NULL, 'N', 0, 0, 1, 0, 1, NULL, NULL, 'O', NULL, NULL, NULL, NULL, 'O', 5, '2017-12-07 20:11:03.374896', 2, NULL, NULL, 0, NULL, NULL, '$1$8KzpRGnE$Oy3TJRmY/oH5VXkiUNgJj.');
INSERT INTO compte VALUES (nextval('seq_compt_cod'), 'test8', '', 'a.a@a.com', 774, 'O', 0, '2017-12-07 20:11:03.374896+00', '2017-12-07 20:11:03.374896+00', '78.248.5.156', NULL, NULL, 'N', 'O', 'N', NULL, NULL, 'O', 'O', NULL, 'N', 0, 0, 1, 0, 1, NULL, NULL, 'O', NULL, NULL, NULL, NULL, 'O', 5, '2017-12-07 20:11:03.374896', 2, NULL, NULL, 0, NULL, NULL, '$1$8KzpRGnE$Oy3TJRmY/oH5VXkiUNgJj.');
INSERT INTO compte VALUES (nextval('seq_compt_cod'), 'test9', '', 'a.a@a.com', 774, 'O', 0, '2017-12-07 20:11:03.374896+00', '2017-12-07 20:11:03.374896+00', '78.248.5.156', NULL, NULL, 'N', 'O', 'N', NULL, NULL, 'O', 'O', NULL, 'N', 0, 0, 1, 0, 1, NULL, NULL, 'O', NULL, NULL, NULL, NULL, 'O', 5, '2017-12-07 20:11:03.374896', 2, NULL, NULL, 0, NULL, NULL, '$1$8KzpRGnE$Oy3TJRmY/oH5VXkiUNgJj.');
INSERT INTO compte VALUES (nextval('seq_compt_cod'), 'test10', '', 'a.a@a.com', 774, 'O', 0, '2017-12-07 20:11:03.374896+00', '2017-12-07 20:11:03.374896+00', '78.248.5.156', NULL, NULL, 'N', 'O', 'N', NULL, NULL, 'O', 'O', NULL, 'N', 0, 0, 1, 0, 1, NULL, NULL, 'O', NULL, NULL, NULL, NULL, 'O', 5, '2017-12-07 20:11:03.374896', 2, NULL, NULL, 0, NULL, NULL, '$1$8KzpRGnE$Oy3TJRmY/oH5VXkiUNgJj.');
INSERT INTO compte VALUES (nextval('seq_compt_cod'), 'test11', '', 'a.a@a.com', 774, 'O', 0, '2017-12-07 20:11:03.374896+00', '2017-12-07 20:11:03.374896+00', '78.248.5.156', NULL, NULL, 'N', 'O', 'N', NULL, NULL, 'O', 'O', NULL, 'N', 0, 0, 1, 0, 1, NULL, NULL, 'O', NULL, NULL, NULL, NULL, 'O', 5, '2017-12-07 20:11:03.374896', 2, NULL, NULL, 0, NULL, NULL, '$1$8KzpRGnE$Oy3TJRmY/oH5VXkiUNgJj.');
INSERT INTO compte VALUES (nextval('seq_compt_cod'), 'test12', '', 'a.a@a.com', 774, 'O', 0, '2017-12-07 20:11:03.374896+00', '2017-12-07 20:11:03.374896+00', '78.248.5.156', NULL, NULL, 'N', 'O', 'N', NULL, NULL, 'O', 'O', NULL, 'N', 0, 0, 1, 0, 1, NULL, NULL, 'O', NULL, NULL, NULL, NULL, 'O', 5, '2017-12-07 20:11:03.374896', 2, NULL, NULL, 0, NULL, NULL, '$1$8KzpRGnE$Oy3TJRmY/oH5VXkiUNgJj.');
INSERT INTO compte VALUES (nextval('seq_compt_cod'), 'test13', '', 'a.a@a.com', 774, 'O', 0, '2017-12-07 20:11:03.374896+00', '2017-12-07 20:11:03.374896+00', '78.248.5.156', NULL, NULL, 'N', 'O', 'N', NULL, NULL, 'O', 'O', NULL, 'N', 0, 0, 1, 0, 1, NULL, NULL, 'O', NULL, NULL, NULL, NULL, 'O', 5, '2017-12-07 20:11:03.374896', 2, NULL, NULL, 0, NULL, NULL, '$1$8KzpRGnE$Oy3TJRmY/oH5VXkiUNgJj.');
INSERT INTO compte VALUES (nextval('seq_compt_cod'), 'test14', '', 'a.a@a.com', 774, 'O', 0, '2017-12-07 20:11:03.374896+00', '2017-12-07 20:11:03.374896+00', '78.248.5.156', NULL, NULL, 'N', 'O', 'N', NULL, NULL, 'O', 'O', NULL, 'N', 0, 0, 1, 0, 1, NULL, NULL, 'O', NULL, NULL, NULL, NULL, 'O', 5, '2017-12-07 20:11:03.374896', 2, NULL, NULL, 0, NULL, NULL, '$1$8KzpRGnE$Oy3TJRmY/oH5VXkiUNgJj.');
INSERT INTO compte VALUES (nextval('seq_compt_cod'), 'test15', '', 'a.a@a.com', 774, 'O', 0, '2017-12-07 20:11:03.374896+00', '2017-12-07 20:11:03.374896+00', '78.248.5.156', NULL, NULL, 'N', 'O', 'N', NULL, NULL, 'O', 'O', NULL, 'N', 0, 0, 1, 0, 1, NULL, NULL, 'O', NULL, NULL, NULL, NULL, 'O', 5, '2017-12-07 20:11:03.374896', 2, NULL, NULL, 0, NULL, NULL, '$1$8KzpRGnE$Oy3TJRmY/oH5VXkiUNgJj.');

INSERT INTO news VALUES (nextval('seq_news'),'Bienvenue','Texte de bienvenue', now(),'Auteur du bienvenu', NULL);

select into c_admin compt_cod from compte where compt_nom='admin';
select into c_monstre compt_cod from compte where compt_nom='monstre';
select into c_test0 compt_cod from compte where compt_nom='test0';
select into c_test1 compt_cod from compte where compt_nom='test1';
select into c_test2 compt_cod from compte where compt_nom='test2';
select into c_test3 compt_cod from compte where compt_nom='test3';
select into c_test4 compt_cod from compte where compt_nom='test4';
select into c_test5 compt_cod from compte where compt_nom='test5';
select into c_test6 compt_cod from compte where compt_nom='test6';
select into c_test7 compt_cod from compte where compt_nom='test7';
select into c_test8 compt_cod from compte where compt_nom='test8';
select into c_test9 compt_cod from compte where compt_nom='test9';
select into c_test10 compt_cod from compte where compt_nom='test10';
select into c_test11 compt_cod from compte where compt_nom='test11';
select into c_test12 compt_cod from compte where compt_nom='test12';
select into c_test13 compt_cod from compte where compt_nom='test13';
select into c_test14 compt_cod from compte where compt_nom='test14';
select into c_test15 compt_cod from compte where compt_nom='test15';

	/**************************************/
	/* Etape 2                            */
	/* Perso                 */
	/**************************************/

INSERT INTO perso VALUES (nextval('seq_perso'), 7, 16, 6, 16, 7, 16, 6, 16, 'M', 2, 32, 32, '2017-12-13 10:27:00.504942+00', 720, NULL, '2017-12-12 22:27:00.504942+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 1, 1, 0, 0, 0, 0, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test0_archer', NULL, 0, NULL, 'test0_archer', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, 'O', 0, 0, 0, 0, NULL, 1, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 7, 16, 6, 16, 7, 16, 6, 16, 'M', 2, 62, 62, '2017-12-13 10:27:00.504942+00', 660, NULL, '2017-12-12 22:27:00.504942+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 10, 1, 2, 0, 0, 2, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 4, 'test1_archer', NULL, 0, NULL, 'test1_archer', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 0, 0, 0, 0, NULL, 460, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 7, 16, 6, 16, 7, 16, 6, 16, 'M', 2, 92, 92, '2017-12-13 10:27:00.504942+00', 565, NULL, '2017-12-12 22:27:00.504942+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 20, 1, 2, 0, 0, 3, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 9, 'test2_archer', NULL, 0, NULL, 'test2_archer', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 0, 0, 0, 0, NULL, 1900, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 7, 16, 6, 16, 7, 16, 6, 16, 'M', 2, 132, 132, '2017-12-13 10:27:00.504942+00', 525, NULL, '2017-12-12 22:27:00.504942+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 30, 1, 5, 0, 0, 3, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 11, 'test3_archer', NULL, 0, NULL, 'test3_archer', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 3, 0, 0, 0, NULL, 4350, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 7, 16, 6, 16, 7, 16, 6, 16, 'M', 2, 162, 162, '2017-12-13 10:27:00.504942+00', 470, NULL, '2017-12-12 22:27:00.504942+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 40, 1, 5, 0, 0, 3, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 17, 'test4_archer', NULL, 0, NULL, 'test4_archer', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 3, 0, 0, 0, NULL, 7800, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 7, 21, 6, 16, 7, 16, 6, 16, 'M', 2, 192, 192, '2017-12-13 10:27:00.504942+00', 470, NULL, '2017-12-12 22:27:00.504942+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 50, 1, 5, 0, 0, 3, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 22, 'test5_archer', NULL, 0, NULL, 'test5_archer', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 3, 0, 0, 0, NULL, 12250, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 7, 21, 6, 16, 7, 16, 6, 16, 'M', 2, 222, 222, '2017-12-13 10:27:00.504942+00', 470, NULL, '2017-12-12 22:27:00.504942+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 60, 1, 5, 0, 0, 3, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 29, 'test6_archer', NULL, 0, NULL, 'test6_archer', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 6, 0, 0, 0, NULL, 17700, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 7, 21, 6, 16, 7, 16, 6, 16, 'M', 2, 252, 252, '2017-12-13 10:27:00.504942+00', 435, NULL, '2017-12-12 22:27:00.504942+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 70, 1, 5, 0, 0, 3, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 34, 'test7_archer', NULL, 0, NULL, 'test7_archer', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 6, 0, 0, 0, NULL, 24150, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 7, 21, 6, 16, 7, 16, 6, 16, 'M', 2, 282, 282, '2017-12-13 10:27:00.504942+00', 410, NULL, '2017-12-12 22:27:00.504942+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 80, 1, 5, 0, 0, 3, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 39, 'test8_archer', NULL, 0, NULL, 'test8_archer', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 6, 0, 0, 0, NULL, 31600, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 7, 25, 6, 16, 7, 16, 6, 16, 'M', 2, 312, 312, '2017-12-13 10:27:00.504942+00', 410, NULL, '2017-12-12 22:27:00.504942+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 90, 1, 5, 0, 0, 3, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 45, 'test9_archer', NULL, 0, NULL, 'test9_archer', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 6, 0, 0, 0, NULL, 40050, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);

INSERT INTO perso VALUES (nextval('seq_perso'), 7, 30, 6, 16, 7, 16, 6, 16, 'M', 2, 342, 342, '2017-12-13 10:27:00.504942+00', 410, NULL, '2017-12-12 22:27:00.504942+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 100, 1, 5, 0, 0, 3, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 50, 'test10_archer', NULL, 0, NULL, 'test10_archer', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 6, 0, 0, 0, NULL, 49500, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 7, 35, 6, 16, 7, 16, 6, 16, 'M', 2, 372, 372, '2017-12-13 10:27:00.504942+00', 410, NULL, '2017-12-12 22:27:00.504942+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 110, 1, 5, 0, 0, 3, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 55, 'test11_archer', NULL, 0, NULL, 'test11_archer', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 6, 0, 0, 0, NULL, 59950, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 7, 40, 6, 16, 7, 16, 6, 16, 'M', 2, 402, 402, '2017-12-13 10:27:00.504942+00', 410, NULL, '2017-12-12 22:27:00.504942+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 120, 1, 5, 0, 0, 3, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 60, 'test12_archer', NULL, 0, NULL, 'test12_archer', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 6, 0, 0, 0, NULL, 71400, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 7, 40, 6, 16, 7, 16, 6, 16, 'M', 2, 432, 432, '2017-12-13 10:27:00.504942+00', 385, NULL, '2017-12-12 22:27:00.504942+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 130, 1, 5, 0, 0, 3, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 65, 'test13_archer', NULL, 0, NULL, 'test13_archer', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 6, 0, 0, 0, NULL, 83850, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 7, 40, 6, 16, 7, 16, 6, 16, 'M', 2, 472, 472, '2017-12-13 10:27:00.504942+00', 360, NULL, '2017-12-12 22:27:00.504942+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 140, 1, 5, 0, 0, 3, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 70, 'test14_archer', NULL, 0, NULL, 'test14_archer', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 6, 0, 0, 0, NULL, 97300, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 12, 40, 6, 16, 7, 16, 6, 16, 'M', 2, 502, 502, '2017-12-13 10:27:00.504942+00', 360, NULL, '2017-12-12 22:27:00.504942+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 150, 1, 5, 0, 0, 3, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 75, 'test15_archer', NULL, 0, NULL, 'test15_archer', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 6, 0, 0, 0, NULL, 111750, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);

INSERT INTO perso VALUES (nextval('seq_perso'), 7, 6, 16, 16, 7, 6, 16, 16, 'M', 2, 32, 32, '2017-12-13 10:27:00.560585+00', 720, NULL, '2017-12-12 22:27:00.560585+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 1, 1, 0, 0, 0, 0, 1, 3, NULL, 21, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test0_mage', NULL, 0, NULL, 'test0_mage', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, 'O', 0, 0, 0, 0, NULL, 0, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 7, 6, 16, 16, 7, 6, 16, 16, 'M', 2, 62, 62, '2017-12-13 10:27:00.560585+00', 585, NULL, '2017-12-12 22:27:00.560585+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 10, 1, 0, 0, 0, 5, 1, 3, NULL, 21, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test1_mage', NULL, 0, NULL, 'test1_mage', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 0, 0, 0, 0, NULL, 460, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 7, 6, 16, 16, 7, 6, 16, 16, 'M', 2, 92, 92, '2017-12-13 10:27:00.560585+00', 495, NULL, '2017-12-12 22:27:00.560585+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 20, 1, 0, 0, 0, 10, 1, 3, NULL, 21, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test2_mage', NULL, 0, NULL, 'test2_mage', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 0, 0, 0, 0, NULL, 1900, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 7, 6, 16, 16, 7, 6, 16, 16, 'M', 2, 122, 122, '2017-12-13 10:27:00.560585+00', 445, NULL, '2017-12-12 22:27:00.560585+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 30, 1, 0, 0, 0, 12, 1, 3, NULL, 21, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test3_mage', NULL, 0, NULL, 'test3_mage', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 0, 3, 0, 0, NULL, 4350, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 7, 6, 20, 16, 7, 6, 16, 16, 'M', 2, 152, 152, '2017-12-13 10:27:00.560585+00', 420, NULL, '2017-12-12 22:27:00.560585+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 40, 1, 0, 0, 0, 12, 1, 3, NULL, 21, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test4_mage', NULL, 0, NULL, 'test4_mage', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 0, 4, 0, 0, NULL, 7800, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 7, 6, 25, 16, 7, 6, 16, 16, 'M', 2, 182, 182, '2017-12-13 10:27:00.560585+00', 405, NULL, '2017-12-12 22:27:00.560585+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 50, 1, 0, 0, 0, 12, 1, 3, NULL, 21, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test5_mage', NULL, 0, NULL, 'test5_mage', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 0, 6, 0, 0, NULL, 12250, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 7, 6, 30, 16, 7, 6, 16, 16, 'M', 2, 212, 212, '2017-12-13 10:27:00.560585+00', 390, NULL, '2017-12-12 22:27:00.560585+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 60, 1, 0, 0, 0, 12, 1, 3, NULL, 21, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test6_mage', NULL, 0, NULL, 'test6_mage', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 0, 8, 0, 0, NULL, 17700, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 7, 6, 35, 16, 7, 6, 16, 16, 'M', 2, 242, 242, '2017-12-13 10:27:00.560585+00', 370, NULL, '2017-12-12 22:27:00.560585+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 70, 1, 0, 0, 0, 12, 1, 3, NULL, 21, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test7_mage', NULL, 0, NULL, 'test7_mage', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 0, 9, 0, 0, NULL, 24150, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 9, 6, 40, 16, 7, 6, 16, 16, 'M', 2, 272, 272, '2017-12-13 10:27:00.560585+00', 360, NULL, '2017-12-12 22:27:00.560585+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 80, 1, 0, 0, 0, 12, 1, 3, NULL, 27, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test8_mage', NULL, 0, NULL, 'test8_mage', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 0, 10, 0, 0, NULL, 31600, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 12, 6, 45, 16, 7, 6, 16, 16, 'M', 2, 302, 302, '2017-12-13 10:27:00.560585+00', 360, NULL, '2017-12-12 22:27:00.560585+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 90, 1, 0, 0, 0, 12, 1, 3, NULL, 27, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test9_mage', NULL, 0, NULL, 'test9_mage', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 0, 12, 0, 0, NULL, 40050, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);

INSERT INTO perso VALUES (nextval('seq_perso'), 12, 6, 50, 20, 7, 6, 16, 16, 'M', 2, 352, 352, '2017-12-13 10:27:00.560585+00', 360, NULL, '2017-12-12 22:27:00.560585+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 100, 1, 0, 0, 0, 12, 1, 3, NULL, 27, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test10_mage', NULL, 0, NULL, 'test10_mage', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 0, 13, 0, 0, NULL, 49500, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 12, 6, 55, 24, 7, 6, 16, 16, 'M', 2, 412, 412, '2017-12-13 10:27:00.560585+00', 360, NULL, '2017-12-12 22:27:00.560585+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 110, 1, 0, 0, 0, 12, 1, 3, NULL, 27, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test11_mage', NULL, 0, NULL, 'test11_mage', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 0, 14, 0, 0, NULL, 59950, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 12, 6, 60, 28, 7, 6, 16, 16, 'M', 2, 482, 482, '2017-12-13 10:27:00.560585+00', 360, NULL, '2017-12-12 22:27:00.560585+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 120, 1, 0, 0, 0, 12, 1, 3, NULL, 27, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test12_mage', NULL, 0, NULL, 'test12_mage', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 0, 15, 0, 0, NULL, 71400, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 12, 6, 65, 32, 7, 6, 16, 16, 'M', 2, 532, 532, '2017-12-13 10:27:00.560585+00', 360, NULL, '2017-12-12 22:27:00.560585+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 130, 1, 0, 0, 0, 12, 1, 3, NULL, 27, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test13_mage', NULL, 0, NULL, 'test13_mage', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 0, 16, 0, 0, NULL, 83850, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 12, 6, 70, 36, 7, 6, 16, 16, 'M', 2, 632, 632, '2017-12-13 10:27:00.560585+00', 360, NULL, '2017-12-12 22:27:00.560585+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 140, 1, 0, 0, 0, 12, 1, 3, NULL, 27, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test14_mage', NULL, 0, NULL, 'test14_mage', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 0, 17, 0, 0, NULL, 97300, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 12, 6, 75, 40, 7, 6, 16, 16, 'M', 2, 782, 782, '2017-12-13 10:27:00.560585+00', 360, NULL, '2017-12-12 22:27:00.560585+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 150, 1, 0, 0, 0, 12, 1, 3, NULL, 27, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test15_mage', NULL, 0, NULL, 'test15_mage', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 0, 18, 0, 0, NULL, 111750, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);

INSERT INTO perso VALUES (nextval('seq_perso'), 16, 7, 6, 16, 16, 7, 6, 16, 'M', 2, 32, 32, '2017-12-13 10:27:00.61643+00', 720, NULL, '2017-12-12 22:27:00.61643+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 1, 1, 0, 0, 0, 0, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test0_cac', NULL, 0, NULL, 'test0_cac', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, 'O', 0, 0, 0, 0, NULL, 0, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 16, 7, 6, 16, 16, 7, 6, 16, 'M', 2, 62, 62, '2017-12-13 10:27:00.61643+00', 720, NULL, '2017-12-12 22:27:00.61643+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 10, 1, 0, 0, 5, 5, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test1_cac', NULL, 0, NULL, 'test1_cac', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 0, 0, 0, 0, NULL, 450, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 16, 7, 6, 16, 16, 7, 6, 16, 'M', 2, 92, 92, '2017-12-13 10:27:00.61643+00', 720, NULL, '2017-12-12 22:27:00.61643+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 20, 1, 0, 0, 10, 10, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test2_cac', NULL, 0, NULL, 'test2_cac', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 0, 0, 0, 0, NULL, 1900, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 16, 7, 6, 16, 16, 7, 6, 16, 'M', 2, 122, 122, '2017-12-13 10:27:00.61643+00', 585, NULL, '2017-12-12 22:27:00.61643+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 30, 1, 0, 0, 12, 10, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test3_cac', NULL, 0, NULL, 'test3_cac', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 3, 0, 0, 0, NULL, 4350, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 16, 7, 6, 16, 16, 7, 6, 16, 'M', 2, 152, 152, '2017-12-13 10:27:00.61643+00', 525, NULL, '2017-12-12 22:27:00.61643+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 40, 1, 0, 0, 17, 10, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test4_cac', NULL, 0, NULL, 'test4_cac', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 5, 0, 0, 0, NULL, 7800, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 16, 7, 6, 16, 16, 7, 6, 16, 'M', 2, 182, 182, '2017-12-13 10:27:00.61643+00', 480, NULL, '2017-12-12 22:27:00.61643+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 50, 1, 0, 0, 22, 10, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test5_cac', NULL, 0, NULL, 'test5_cac', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 7, 0, 0, 0, NULL, 12250, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 16, 7, 6, 16, 16, 7, 6, 16, 'M', 2, 212, 212, '2017-12-13 10:27:00.61643+00', 480, NULL, '2017-12-12 22:27:00.61643+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 60, 1, 0, 0, 30, 10, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test6_cac', NULL, 0, NULL, 'test6_cac', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 9, 0, 0, 0, NULL, 17750, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 16, 12, 6, 16, 16, 7, 6, 16, 'M', 2, 242, 242, '2017-12-13 10:27:00.61643+00', 480, NULL, '2017-12-12 22:27:00.61643+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 70, 1, 0, 0, 35, 10, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test7_cac', NULL, 0, NULL, 'test7_cac', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 9, 0, 0, 0, NULL, 24150, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 16, 17, 6, 16, 16, 7, 6, 16, 'M', 2, 272, 272, '2017-12-13 10:27:00.61643+00', 480, NULL, '2017-12-12 22:27:00.61643+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 80, 1, 0, 0, 40, 10, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test8_cac', NULL, 0, NULL, 'test8_cac', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 9, 0, 0, 0, NULL, 31600, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 16, 22, 6, 16, 16, 7, 6, 16, 'M', 2, 302, 302, '2017-12-13 10:27:00.61643+00', 480, NULL, '2017-12-12 22:27:00.61643+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 90, 1, 0, 0, 45, 10, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test9_cac', NULL, 0, NULL, 'test9_cac', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 9, 0, 0, 0, NULL, 40050, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);

INSERT INTO perso VALUES (nextval('seq_perso'), 16, 22, 6, 16, 16, 7, 6, 16, 'M', 2, 332, 332, '2017-12-13 10:27:00.61643+00', 440, NULL, '2017-12-12 22:27:00.61643+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 100, 1, 0, 0, 50, 10, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test10_cac', NULL, 0, NULL, 'test10_cac', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 9, 0, 0, 0, NULL, 49500, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 16, 22, 6, 16, 16, 7, 6, 16, 'M', 2, 372, 372, '2017-12-13 10:27:00.61643+00', 415, NULL, '2017-12-12 22:27:00.61643+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 110, 1, 0, 0, 55, 10, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test11_cac', NULL, 0, NULL, 'test11_cac', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 9, 0, 0, 0, NULL, 59950, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 16, 22, 6, 16, 16, 7, 6, 16, 'M', 2, 412, 412, '2017-12-13 10:27:00.61643+00', 390, NULL, '2017-12-12 22:27:00.61643+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 120, 1, 0, 0, 60, 10, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test12_cac', NULL, 0, NULL, 'test12_cac', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 9, 0, 0, 0, NULL, 71400, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 16, 22, 6, 16, 16, 7, 6, 16, 'M', 2, 452, 452, '2017-12-13 10:27:00.61643+00', 365, NULL, '2017-12-12 22:27:00.61643+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 130, 1, 0, 0, 65, 10, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test13_cac', NULL, 0, NULL, 'test13_cac', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 9, 0, 0, 0, NULL, 83850, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 19, 23, 6, 16, 16, 7, 6, 16, 'M', 2, 492, 492, '2017-12-13 10:27:00.61643+00', 360, NULL, '2017-12-12 22:27:00.61643+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 140, 1, 0, 0, 70, 10, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test14_cac', NULL, 0, NULL, 'test14_cac', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 9, 0, 0, 0, NULL, 97300, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);
INSERT INTO perso VALUES (nextval('seq_perso'), 22, 25, 6, 16, 16, 7, 6, 16, 'M', 2, 542, 542, '2017-12-13 10:27:00.61643+00', 360, NULL, '2017-12-12 22:27:00.61643+00', NULL, 'O', 12, '2016-12-17 14:37:52.023567+00', 1, 3, 3, 150, 0, 150, 1, 0, 0, 75, 10, 1, 3, NULL, 48, NULL, 0, 0, 0, 0, NULL, 0, 0, 'test15_cac', NULL, 0, NULL, 'test15_cac', NULL, NULL, 1, 'O', 0, 96, 0, 0, 0, 0, 0, ' ', 9, 0, 0, 0, NULL, 111750, 3, 'N', 0, 0, 'N', 0, 0, 0, 1, '', 0, 0, 0, '2016-12-17 14:37:52.023567+00', 0, 0, 0, 'O', 1, 0, '2016-12-17 14:37:52.023567+00', 1, '', false, 0, 0, '', 0, 0, NULL, 0, 0, NULL, NULL, NULL, 0);


select into p_test0_archer perso_cod from perso where perso_nom='test0_archer';
select into p_test0_mage perso_cod from perso where perso_nom='test0_mage';
select into p_test0_cac perso_cod from perso where perso_nom='test0_cac';
select into p_test1_archer perso_cod from perso where perso_nom='test1_archer';
select into p_test1_mage perso_cod from perso where perso_nom='test1_mage';
select into p_test1_cac perso_cod from perso where perso_nom='test1_cac';
select into p_test2_archer perso_cod from perso where perso_nom='test2_archer';
select into p_test2_mage perso_cod from perso where perso_nom='test2_mage';
select into p_test2_cac perso_cod from perso where perso_nom='test2_cac';
select into p_test3_archer perso_cod from perso where perso_nom='test3_archer';
select into p_test3_mage perso_cod from perso where perso_nom='test3_mage';
select into p_test3_cac perso_cod from perso where perso_nom='test3_cac';
select into p_test4_archer perso_cod from perso where perso_nom='test4_archer';
select into p_test4_mage perso_cod from perso where perso_nom='test4_mage';
select into p_test4_cac perso_cod from perso where perso_nom='test4_cac';
select into p_test5_archer perso_cod from perso where perso_nom='test5_archer';
select into p_test5_mage perso_cod from perso where perso_nom='test5_mage';
select into p_test5_cac perso_cod from perso where perso_nom='test5_cac';
select into p_test6_archer perso_cod from perso where perso_nom='test6_archer';
select into p_test6_mage perso_cod from perso where perso_nom='test6_mage';
select into p_test6_cac perso_cod from perso where perso_nom='test6_cac';
select into p_test7_archer perso_cod from perso where perso_nom='test7_archer';
select into p_test7_mage perso_cod from perso where perso_nom='test7_mage';
select into p_test7_cac perso_cod from perso where perso_nom='test7_cac';
select into p_test8_archer perso_cod from perso where perso_nom='test8_archer';
select into p_test8_mage perso_cod from perso where perso_nom='test8_mage';
select into p_test8_cac perso_cod from perso where perso_nom='test8_cac';
select into p_test9_archer perso_cod from perso where perso_nom='test9_archer';
select into p_test9_mage perso_cod from perso where perso_nom='test9_mage';
select into p_test9_cac perso_cod from perso where perso_nom='test9_cac';
select into p_test10_archer perso_cod from perso where perso_nom='test10_archer';
select into p_test10_mage perso_cod from perso where perso_nom='test10_mage';
select into p_test10_cac perso_cod from perso where perso_nom='test10_cac';
select into p_test11_archer perso_cod from perso where perso_nom='test11_archer';
select into p_test11_mage perso_cod from perso where perso_nom='test11_mage';
select into p_test11_cac perso_cod from perso where perso_nom='test11_cac';
select into p_test12_archer perso_cod from perso where perso_nom='test12_archer';
select into p_test12_mage perso_cod from perso where perso_nom='test12_mage';
select into p_test12_cac perso_cod from perso where perso_nom='test12_cac';
select into p_test13_archer perso_cod from perso where perso_nom='test13_archer';
select into p_test13_mage perso_cod from perso where perso_nom='test13_mage';
select into p_test13_cac perso_cod from perso where perso_nom='test13_cac';
select into p_test14_archer perso_cod from perso where perso_nom='test14_archer';
select into p_test14_mage perso_cod from perso where perso_nom='test14_mage';
select into p_test14_cac perso_cod from perso where perso_nom='test14_cac';
select into p_test15_archer perso_cod from perso where perso_nom='test15_archer';
select into p_test15_mage perso_cod from perso where perso_nom='test15_mage';
select into p_test15_cac perso_cod from perso where perso_nom='test15_cac';

	/**************************************/
	/* Etape 2                            */
	/* compt_droit                 */
	/**************************************/

INSERT INTO compt_droit VALUES (c_admin, 'O', 'O', 'O', 'O', 'N', 'A', 'O', 'O', 'O', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O');
INSERT INTO compt_droit VALUES (c_monstre, 'N', 'O', 'O', 'N', 'O', 'A', 'N', 'N', 'N', 'O', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N');
INSERT INTO compt_droit VALUES (c_test0, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N');
INSERT INTO compt_droit VALUES (c_test1, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N');
INSERT INTO compt_droit VALUES (c_test2, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N');
INSERT INTO compt_droit VALUES (c_test3, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N');
INSERT INTO compt_droit VALUES (c_test4, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N');
INSERT INTO compt_droit VALUES (c_test5, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N');
INSERT INTO compt_droit VALUES (c_test6, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N');
INSERT INTO compt_droit VALUES (c_test7, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N');
INSERT INTO compt_droit VALUES (c_test8, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N');
INSERT INTO compt_droit VALUES (c_test9, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N');
INSERT INTO compt_droit VALUES (c_test10, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N');
INSERT INTO compt_droit VALUES (c_test11, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N');
INSERT INTO compt_droit VALUES (c_test12, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N');
INSERT INTO compt_droit VALUES (c_test13, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N');
INSERT INTO compt_droit VALUES (c_test14, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N');
INSERT INTO compt_droit VALUES (c_test15, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N');

	/**************************************/
	/* Etape 2                            */
	/* perso compt                 */
	/**************************************/


insert into perso_compte values (nextval('seq_pcompt_cod'),c_test0,p_test0_archer);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test0,p_test0_mage);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test0,p_test0_cac);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test1,p_test1_archer);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test1,p_test1_mage);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test1,p_test1_cac);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test2,p_test2_archer);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test2,p_test2_mage);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test2,p_test2_cac);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test3,p_test3_archer);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test3,p_test3_mage);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test3,p_test3_cac);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test4,p_test4_archer);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test4,p_test4_mage);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test4,p_test4_cac);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test5,p_test5_archer);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test5,p_test5_mage);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test5,p_test5_cac);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test6,p_test6_archer);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test6,p_test6_mage);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test6,p_test6_cac);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test7,p_test7_archer);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test7,p_test7_mage);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test7,p_test7_cac);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test8,p_test8_archer);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test8,p_test8_mage);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test8,p_test8_cac);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test9,p_test9_archer);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test9,p_test9_mage);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test9,p_test9_cac);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test10,p_test10_archer);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test10,p_test10_mage);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test10,p_test10_cac);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test11,p_test11_archer);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test11,p_test11_mage);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test11,p_test11_cac);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test12,p_test12_archer);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test12,p_test12_mage);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test12,p_test12_cac);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test13,p_test13_archer);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test13,p_test13_mage);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test13,p_test13_cac);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test14,p_test14_archer);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test14,p_test14_mage);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test14,p_test14_cac);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test15,p_test15_archer);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test15,p_test15_mage);
insert into perso_compte values (nextval('seq_pcompt_cod'),c_test15,p_test15_cac);

	/**************************************/
	/* Etape 2                            */
	/* perso position                 */
	/**************************************/


insert into perso_position values(nextval('seq_ppos_cod'),924,p_test0_archer);
insert into perso_position values(nextval('seq_ppos_cod'),924,p_test0_mage);
insert into perso_position values(nextval('seq_ppos_cod'),924,p_test0_cac);
insert into perso_position values(nextval('seq_ppos_cod'),2689,p_test1_archer);
insert into perso_position values(nextval('seq_ppos_cod'),2689,p_test1_mage);
insert into perso_position values(nextval('seq_ppos_cod'),2689,p_test1_cac);
insert into perso_position values(nextval('seq_ppos_cod'),4538,p_test2_archer);
insert into perso_position values(nextval('seq_ppos_cod'),4538,p_test2_mage);
insert into perso_position values(nextval('seq_ppos_cod'),4538,p_test2_cac);
insert into perso_position values(nextval('seq_ppos_cod'),7447,p_test3_archer);
insert into perso_position values(nextval('seq_ppos_cod'),7447,p_test3_mage);
insert into perso_position values(nextval('seq_ppos_cod'),7447,p_test3_cac);
insert into perso_position values(nextval('seq_ppos_cod'),10355,p_test4_archer);
insert into perso_position values(nextval('seq_ppos_cod'),10355,p_test4_mage);
insert into perso_position values(nextval('seq_ppos_cod'),10355,p_test4_cac);
insert into perso_position values(nextval('seq_ppos_cod'),14967,p_test5_archer);
insert into perso_position values(nextval('seq_ppos_cod'),14967,p_test5_mage);
insert into perso_position values(nextval('seq_ppos_cod'),14967,p_test5_cac);
insert into perso_position values(nextval('seq_ppos_cod'),48733,p_test6_archer);
insert into perso_position values(nextval('seq_ppos_cod'),48733,p_test6_mage);
insert into perso_position values(nextval('seq_ppos_cod'),48733,p_test6_cac);
insert into perso_position values(nextval('seq_ppos_cod'),54400,p_test7_archer);
insert into perso_position values(nextval('seq_ppos_cod'),54400,p_test7_mage);
insert into perso_position values(nextval('seq_ppos_cod'),54400,p_test7_cac);
insert into perso_position values(nextval('seq_ppos_cod'),79018,p_test8_archer);
insert into perso_position values(nextval('seq_ppos_cod'),79018,p_test8_mage);
insert into perso_position values(nextval('seq_ppos_cod'),79018,p_test8_cac);
insert into perso_position values(nextval('seq_ppos_cod'),93709,p_test9_archer);
insert into perso_position values(nextval('seq_ppos_cod'),93709,p_test9_mage);
insert into perso_position values(nextval('seq_ppos_cod'),93709,p_test9_cac);
insert into perso_position values(nextval('seq_ppos_cod'),93709,p_test10_archer);
insert into perso_position values(nextval('seq_ppos_cod'),93709,p_test10_mage);
insert into perso_position values(nextval('seq_ppos_cod'),93709,p_test10_cac);
insert into perso_position values(nextval('seq_ppos_cod'),93709,p_test11_archer);
insert into perso_position values(nextval('seq_ppos_cod'),93709,p_test11_mage);
insert into perso_position values(nextval('seq_ppos_cod'),93709,p_test11_cac);
insert into perso_position values(nextval('seq_ppos_cod'),93709,p_test12_archer);
insert into perso_position values(nextval('seq_ppos_cod'),93709,p_test12_mage);
insert into perso_position values(nextval('seq_ppos_cod'),93709,p_test12_cac);
insert into perso_position values(nextval('seq_ppos_cod'),93709,p_test13_archer);
insert into perso_position values(nextval('seq_ppos_cod'),93709,p_test13_mage);
insert into perso_position values(nextval('seq_ppos_cod'),93709,p_test13_cac);
insert into perso_position values(nextval('seq_ppos_cod'),93709,p_test14_archer);
insert into perso_position values(nextval('seq_ppos_cod'),93709,p_test14_mage);
insert into perso_position values(nextval('seq_ppos_cod'),93709,p_test14_cac);
insert into perso_position values(nextval('seq_ppos_cod'),93709,p_test15_archer);
insert into perso_position values(nextval('seq_ppos_cod'),93709,p_test15_mage);
insert into perso_position values(nextval('seq_ppos_cod'),93709,p_test15_cac);
	/**************************************/
	/* Etape 2                            */
	/* finir creation perso */
	/**************************************/

for personnage in select perso_cod, perso_niveau from perso where perso_nom like 'test%' loop
	for ligne_competences in select * from competences where comp_connu = 'O' loop
		insert into perso_competences values (nextval('seq_pcomp'),personnage.perso_cod,ligne_competences.comp_cod,valeur_comp_init(personnage.perso_cod,ligne_competences.comp_cod));
	end loop;
	/* Competences de race */
	select into race_perso perso_race_cod from perso where perso_cod = personnage.perso_cod;
	for ligne_competences in select * from race_comp where racecomp_race_cod = race_perso loop
		insert into perso_competences values (nextval('seq_pcomp'),personnage.perso_cod,ligne_competences.racecomp_comp_cod,valeur_comp_init(personnage.perso_cod,ligne_competences.racecomp_comp_cod));				
	end loop;
	if race_perso = 1 then
		insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (personnage.perso_cod,6);
	end if;
	if race_perso = 3 then
		insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (personnage.perso_cod,5);
	end if;
	
end loop;

for personnage in select perso_cod, perso_niveau from perso where perso_nom like 'test%cac' loop
	update perso_competences set pcomp_modificateur=pcomp_modificateur+personnage.perso_niveau where pcomp_perso_cod=personnage.perso_cod;
	update perso_competences set pcomp_modificateur=min(100,30+personnage.perso_niveau) where pcomp_perso_cod=personnage.perso_cod and pcomp_pcomp_cod in (50,51);	
end loop;

for personnage in select perso_cod, perso_niveau from perso where perso_nom like 'test%archer' loop
	update perso_competences set pcomp_modificateur=pcomp_modificateur+personnage.perso_niveau where pcomp_perso_cod=personnage.perso_cod;
	update perso_competences set pcomp_modificateur=min(100,30+personnage.perso_niveau) where pcomp_perso_cod=personnage.perso_cod and pcomp_pcomp_cod in (50,51);
end loop;

for personnage in select perso_cod, perso_niveau from perso where perso_nom like 'test%mage' loop
	update perso_competences set pcomp_modificateur=min(100,pcomp_modificateur+personnage.perso_niveau) where pcomp_perso_cod=personnage.perso_cod;
	update perso_competences set pcomp_modificateur=min(150,50+3*personnage.perso_niveau/2) where pcomp_perso_cod=personnage.perso_cod and pcomp_pcomp_cod in (50,51);	
end loop;

update perso_competences set pcomp_modificateur=90 where pcomp_perso_cod=p_test1_archer and pcomp_pcomp_cod in (15,16,52);
update perso_competences set pcomp_modificateur=100 where pcomp_perso_cod=p_test2_archer and pcomp_pcomp_cod in (15,16,52);
update perso_competences set pcomp_modificateur=110 where pcomp_perso_cod=p_test3_archer and pcomp_pcomp_cod in (15,16,52);
update perso_competences set pcomp_modificateur=120 where pcomp_perso_cod=p_test4_archer and pcomp_pcomp_cod in (15,16,52);
update perso_competences set pcomp_modificateur=128 where pcomp_perso_cod=p_test5_archer and pcomp_pcomp_cod in (15,16,52);
update perso_competences set pcomp_modificateur=136 where pcomp_perso_cod=p_test6_archer and pcomp_pcomp_cod in (15,16,52);
update perso_competences set pcomp_modificateur=144 where pcomp_perso_cod=p_test7_archer and pcomp_pcomp_cod in (15,16,52);
update perso_competences set pcomp_modificateur=152 where pcomp_perso_cod=p_test8_archer and pcomp_pcomp_cod in (15,16,52);
update perso_competences set pcomp_modificateur=160 where pcomp_perso_cod=p_test9_archer and pcomp_pcomp_cod in (15,16,52);
update perso_competences set pcomp_modificateur=168 where pcomp_perso_cod=p_test10_archer and pcomp_pcomp_cod in (15,16,52);
update perso_competences set pcomp_modificateur=176 where pcomp_perso_cod=p_test11_archer and pcomp_pcomp_cod in (15,16,52);
update perso_competences set pcomp_modificateur=184 where pcomp_perso_cod=p_test12_archer and pcomp_pcomp_cod in (15,16,52);
update perso_competences set pcomp_modificateur=1900 where pcomp_perso_cod=p_test13_archer and pcomp_pcomp_cod in (15,16,52);
update perso_competences set pcomp_modificateur=196 where pcomp_perso_cod=p_test14_archer and pcomp_pcomp_cod in (15,16,52);
update perso_competences set pcomp_modificateur=204 where pcomp_perso_cod=p_test15_archer and pcomp_pcomp_cod in (15,16,52);

/* Competences */

insert into perso_competences values (nextval('seq_pcomp'),p_test3_cac,62,80);
insert into perso_competences values (nextval('seq_pcomp'),p_test4_cac,62,95);
insert into perso_competences values (nextval('seq_pcomp'),p_test5_cac,62,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test6_cac,62,113);
insert into perso_competences values (nextval('seq_pcomp'),p_test7_cac,62,120);
insert into perso_competences values (nextval('seq_pcomp'),p_test8_cac,62,126);
insert into perso_competences values (nextval('seq_pcomp'),p_test9_cac,62,132);
insert into perso_competences values (nextval('seq_pcomp'),p_test10_cac,62,138);
insert into perso_competences values (nextval('seq_pcomp'),p_test11_cac,62,144);
insert into perso_competences values (nextval('seq_pcomp'),p_test12_cac,62,150);
insert into perso_competences values (nextval('seq_pcomp'),p_test13_cac,62,156);
insert into perso_competences values (nextval('seq_pcomp'),p_test14_cac,62,162);
insert into perso_competences values (nextval('seq_pcomp'),p_test15_cac,62,168);

insert into perso_competences values (nextval('seq_pcomp'),p_test4_cac,65,80);
insert into perso_competences values (nextval('seq_pcomp'),p_test5_cac,65,95);
insert into perso_competences values (nextval('seq_pcomp'),p_test6_cac,65,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test7_cac,65,113);
insert into perso_competences values (nextval('seq_pcomp'),p_test8_cac,65,120);
insert into perso_competences values (nextval('seq_pcomp'),p_test9_cac,65,126);
insert into perso_competences values (nextval('seq_pcomp'),p_test10_cac,65,132);
insert into perso_competences values (nextval('seq_pcomp'),p_test11_cac,65,138);
insert into perso_competences values (nextval('seq_pcomp'),p_test12_cac,65,144);
insert into perso_competences values (nextval('seq_pcomp'),p_test13_cac,65,150);
insert into perso_competences values (nextval('seq_pcomp'),p_test14_cac,65,156);
insert into perso_competences values (nextval('seq_pcomp'),p_test15_cac,65,162);

insert into perso_competences values (nextval('seq_pcomp'),p_test6_cac,68,80);
insert into perso_competences values (nextval('seq_pcomp'),p_test7_cac,68,95);
insert into perso_competences values (nextval('seq_pcomp'),p_test8_cac,68,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test9_cac,68,113);
insert into perso_competences values (nextval('seq_pcomp'),p_test10_cac,68,121);
insert into perso_competences values (nextval('seq_pcomp'),p_test11_cac,68,129);
insert into perso_competences values (nextval('seq_pcomp'),p_test12_cac,68,137);
insert into perso_competences values (nextval('seq_pcomp'),p_test13_cac,68,143);
insert into perso_competences values (nextval('seq_pcomp'),p_test14_cac,68,151);
insert into perso_competences values (nextval('seq_pcomp'),p_test15_cac,68,159);


insert into perso_competences values (nextval('seq_pcomp'),p_test3_archer,77,80);
insert into perso_competences values (nextval('seq_pcomp'),p_test4_archer,77,95);
insert into perso_competences values (nextval('seq_pcomp'),p_test5_archer,77,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test6_archer,77,113);
insert into perso_competences values (nextval('seq_pcomp'),p_test7_archer,77,120);
insert into perso_competences values (nextval('seq_pcomp'),p_test8_archer,77,126);
insert into perso_competences values (nextval('seq_pcomp'),p_test9_archer,77,132);
insert into perso_competences values (nextval('seq_pcomp'),p_test10_archer,77,138);
insert into perso_competences values (nextval('seq_pcomp'),p_test11_archer,77,144);
insert into perso_competences values (nextval('seq_pcomp'),p_test12_archer,77,152);
insert into perso_competences values (nextval('seq_pcomp'),p_test13_archer,77,160);
insert into perso_competences values (nextval('seq_pcomp'),p_test14_archer,77,166);
insert into perso_competences values (nextval('seq_pcomp'),p_test15_archer,77,172);

insert into perso_competences values (nextval('seq_pcomp'),p_test6_archer,74,80);
insert into perso_competences values (nextval('seq_pcomp'),p_test7_archer,74,95);
insert into perso_competences values (nextval('seq_pcomp'),p_test8_archer,74,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test9_archer,74,113);
insert into perso_competences values (nextval('seq_pcomp'),p_test10_archer,74,121);
insert into perso_competences values (nextval('seq_pcomp'),p_test11_archer,74,128);
insert into perso_competences values (nextval('seq_pcomp'),p_test12_archer,74,134);
insert into perso_competences values (nextval('seq_pcomp'),p_test13_archer,74,140);
insert into perso_competences values (nextval('seq_pcomp'),p_test14_archer,74,145);
insert into perso_competences values (nextval('seq_pcomp'),p_test15_archer,74,150);

insert into perso_competences values (nextval('seq_pcomp'),p_test3_archer,91,50);
insert into perso_competences values (nextval('seq_pcomp'),p_test3_mage,91,50);
insert into perso_competences values (nextval('seq_pcomp'),p_test3_cac,91,50);
insert into perso_competences values (nextval('seq_pcomp'),p_test4_archer,91,70);
insert into perso_competences values (nextval('seq_pcomp'),p_test4_mage,91,70);
insert into perso_competences values (nextval('seq_pcomp'),p_test4_cac,91,70);
insert into perso_competences values (nextval('seq_pcomp'),p_test5_archer,91,90);
insert into perso_competences values (nextval('seq_pcomp'),p_test5_mage,91,90);
insert into perso_competences values (nextval('seq_pcomp'),p_test5_cac,91,90);
insert into perso_competences values (nextval('seq_pcomp'),p_test6_archer,92,95);
insert into perso_competences values (nextval('seq_pcomp'),p_test6_mage,92,95);
insert into perso_competences values (nextval('seq_pcomp'),p_test6_cac,92,95);
insert into perso_competences values (nextval('seq_pcomp'),p_test7_archer,93,100);
insert into perso_competences values (nextval('seq_pcomp'),p_test7_mage,93,100);
insert into perso_competences values (nextval('seq_pcomp'),p_test7_cac,93,100);
insert into perso_competences values (nextval('seq_pcomp'),p_test8_archer,93,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test8_mage,93,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test8_cac,93,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test9_archer,93,110);
insert into perso_competences values (nextval('seq_pcomp'),p_test9_mage,93,110);
insert into perso_competences values (nextval('seq_pcomp'),p_test9_cac,93,110);
insert into perso_competences values (nextval('seq_pcomp'),p_test10_archer,93,115);
insert into perso_competences values (nextval('seq_pcomp'),p_test10_mage,93,115);
insert into perso_competences values (nextval('seq_pcomp'),p_test10_cac,93,115);
insert into perso_competences values (nextval('seq_pcomp'),p_test11_archer,93,119);
insert into perso_competences values (nextval('seq_pcomp'),p_test11_mage,93,119);
insert into perso_competences values (nextval('seq_pcomp'),p_test11_cac,93,119);
insert into perso_competences values (nextval('seq_pcomp'),p_test12_archer,93,123);
insert into perso_competences values (nextval('seq_pcomp'),p_test12_mage,93,123);
insert into perso_competences values (nextval('seq_pcomp'),p_test12_cac,93,123);
insert into perso_competences values (nextval('seq_pcomp'),p_test13_archer,93,127);
insert into perso_competences values (nextval('seq_pcomp'),p_test13_mage,93,127);
insert into perso_competences values (nextval('seq_pcomp'),p_test13_cac,93,127);
insert into perso_competences values (nextval('seq_pcomp'),p_test14_archer,93,130);
insert into perso_competences values (nextval('seq_pcomp'),p_test14_mage,93,130);
insert into perso_competences values (nextval('seq_pcomp'),p_test14_cac,93,130);
insert into perso_competences values (nextval('seq_pcomp'),p_test15_archer,93,133);
insert into perso_competences values (nextval('seq_pcomp'),p_test15_mage,93,133);
insert into perso_competences values (nextval('seq_pcomp'),p_test15_cac,93,133);

insert into perso_competences values (nextval('seq_pcomp'),p_test3_archer,97,50);
insert into perso_competences values (nextval('seq_pcomp'),p_test3_mage,97,50);
insert into perso_competences values (nextval('seq_pcomp'),p_test3_cac,97,50);
insert into perso_competences values (nextval('seq_pcomp'),p_test4_archer,97,65);
insert into perso_competences values (nextval('seq_pcomp'),p_test4_mage,97,65);
insert into perso_competences values (nextval('seq_pcomp'),p_test4_cac,97,65);
insert into perso_competences values (nextval('seq_pcomp'),p_test5_archer,97,80);
insert into perso_competences values (nextval('seq_pcomp'),p_test5_mage,97,80);
insert into perso_competences values (nextval('seq_pcomp'),p_test5_cac,97,80);
insert into perso_competences values (nextval('seq_pcomp'),p_test6_archer,97,90);
insert into perso_competences values (nextval('seq_pcomp'),p_test6_mage,97,90);
insert into perso_competences values (nextval('seq_pcomp'),p_test6_cac,97,90);
insert into perso_competences values (nextval('seq_pcomp'),p_test7_archer,100,95);
insert into perso_competences values (nextval('seq_pcomp'),p_test7_mage,100,95);
insert into perso_competences values (nextval('seq_pcomp'),p_test7_cac,100,95);
insert into perso_competences values (nextval('seq_pcomp'),p_test8_archer,101,100);
insert into perso_competences values (nextval('seq_pcomp'),p_test8_mage,101,100);
insert into perso_competences values (nextval('seq_pcomp'),p_test8_cac,101,100);
insert into perso_competences values (nextval('seq_pcomp'),p_test9_archer,101,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test9_mage,101,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test9_cac,101,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test10_archer,101,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test10_mage,101,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test10_cac,101,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test11_archer,101,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test11_mage,101,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test11_cac,101,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test12_archer,101,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test12_mage,101,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test12_cac,101,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test13_archer,101,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test13_mage,101,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test13_cac,101,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test14_archer,101,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test14_mage,101,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test14_cac,101,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test15_archer,101,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test15_mage,101,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test15_cac,101,105);

insert into perso_competences values (nextval('seq_pcomp'),p_test3_archer,88,50);
insert into perso_competences values (nextval('seq_pcomp'),p_test3_mage,88,50);
insert into perso_competences values (nextval('seq_pcomp'),p_test3_cac,88,50);
insert into perso_competences values (nextval('seq_pcomp'),p_test4_archer,88,65);
insert into perso_competences values (nextval('seq_pcomp'),p_test4_mage,88,65);
insert into perso_competences values (nextval('seq_pcomp'),p_test4_cac,88,65);
insert into perso_competences values (nextval('seq_pcomp'),p_test5_archer,88,80);
insert into perso_competences values (nextval('seq_pcomp'),p_test5_mage,88,80);
insert into perso_competences values (nextval('seq_pcomp'),p_test5_cac,88,80);
insert into perso_competences values (nextval('seq_pcomp'),p_test6_archer,88,90);
insert into perso_competences values (nextval('seq_pcomp'),p_test6_mage,88,90);
insert into perso_competences values (nextval('seq_pcomp'),p_test6_cac,88,90);
insert into perso_competences values (nextval('seq_pcomp'),p_test7_archer,102,95);
insert into perso_competences values (nextval('seq_pcomp'),p_test7_mage,102,95);
insert into perso_competences values (nextval('seq_pcomp'),p_test7_cac,102,95);
insert into perso_competences values (nextval('seq_pcomp'),p_test8_archer,103,100);
insert into perso_competences values (nextval('seq_pcomp'),p_test8_mage,103,100);
insert into perso_competences values (nextval('seq_pcomp'),p_test8_cac,103,100);
insert into perso_competences values (nextval('seq_pcomp'),p_test9_archer,103,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test9_mage,103,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test9_cac,103,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test10_archer,103,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test10_mage,103,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test10_cac,103,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test11_archer,103,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test11_mage,103,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test11_cac,103,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test12_archer,103,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test12_mage,103,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test12_cac,103,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test13_archer,103,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test13_mage,103,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test13_cac,103,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test14_archer,103,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test14_mage,103,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test14_cac,103,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test15_archer,103,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test15_mage,103,105);
insert into perso_competences values (nextval('seq_pcomp'),p_test15_cac,103,105);

for personnage in select perso_cod, perso_niveau from perso where perso_nom like 'test%mage' loop
	if personnage.perso_niveau >= 10 then 
		insert into perso_sorts values(nextval('seq_psort_cod'),2,personnage.perso_cod);
		insert into perso_sorts values(nextval('seq_psort_cod'),3,personnage.perso_cod);
		insert into perso_sorts values(nextval('seq_psort_cod'),5,personnage.perso_cod);
	end if;
	if personnage.perso_niveau >= 20 then 
		insert into perso_sorts values(nextval('seq_psort_cod'),10,personnage.perso_cod);
		insert into perso_sorts values(nextval('seq_psort_cod'),13,personnage.perso_cod);
		insert into perso_sorts values(nextval('seq_psort_cod'),17,personnage.perso_cod);
	end if;
	if personnage.perso_niveau >= 30 then 
		insert into perso_sorts values(nextval('seq_psort_cod'),28,personnage.perso_cod);
		insert into perso_sorts values(nextval('seq_psort_cod'),32,personnage.perso_cod);
	end if;
	if personnage.perso_niveau >= 40 then 
		insert into perso_sorts values(nextval('seq_psort_cod'),30,personnage.perso_cod);
		insert into perso_sorts values(nextval('seq_psort_cod'),38,personnage.perso_cod);
		insert into perso_sorts values(nextval('seq_psort_cod'),56,personnage.perso_cod);
	end if;
	if personnage.perso_niveau >= 50 then 
		insert into perso_sorts values(nextval('seq_psort_cod'),60,personnage.perso_cod);
		insert into perso_sorts values(nextval('seq_psort_cod'),65,personnage.perso_cod);
		insert into perso_sorts values(nextval('seq_psort_cod'),66,personnage.perso_cod);
	end if;
	if personnage.perso_niveau >= 60 then 
		insert into perso_sorts values(nextval('seq_psort_cod'),128,personnage.perso_cod);
		insert into perso_sorts values(nextval('seq_psort_cod'),129,personnage.perso_cod);
		insert into perso_sorts values(nextval('seq_psort_cod'),130,personnage.perso_cod);
		insert into perso_sorts values(nextval('seq_psort_cod'),137,personnage.perso_cod);
	end if;
	if personnage.perso_niveau >= 70 then 
		insert into perso_sorts values(nextval('seq_psort_cod'),147,personnage.perso_cod);
		insert into perso_sorts values(nextval('seq_psort_cod'),148,personnage.perso_cod);
		insert into perso_sorts values(nextval('seq_psort_cod'),144,personnage.perso_cod);
		insert into perso_sorts values(nextval('seq_psort_cod'),154,personnage.perso_cod);
	end if;

end loop;

insert into groupe values (nextval('seq_groupe_cod'),'coterie_0', p_test0_archer, 'Info', now(), now());
insert into groupe values (nextval('seq_groupe_cod'),'coterie_1', p_test1_archer, 'Info', now(), now());
insert into groupe values (nextval('seq_groupe_cod'),'coterie_2', p_test2_archer, 'Info', now(), now());
insert into groupe values (nextval('seq_groupe_cod'),'coterie_3', p_test3_archer, 'Info', now(), now());
insert into groupe values (nextval('seq_groupe_cod'),'coterie_4', p_test4_archer, 'Info', now(), now());
insert into groupe values (nextval('seq_groupe_cod'),'coterie_5', p_test5_archer, 'Info', now(), now());
insert into groupe values (nextval('seq_groupe_cod'),'coterie_6', p_test6_archer, 'Info', now(), now());
insert into groupe values (nextval('seq_groupe_cod'),'coterie_7', p_test7_archer, 'Info', now(), now());
insert into groupe values (nextval('seq_groupe_cod'),'coterie_8', p_test8_archer, 'Info', now(), now());
insert into groupe values (nextval('seq_groupe_cod'),'coterie_9', p_test9_archer, 'Info', now(), now());
insert into groupe values (nextval('seq_groupe_cod'),'coterie_10', p_test10_archer, 'Info', now(), now());
insert into groupe values (nextval('seq_groupe_cod'),'coterie_11', p_test11_archer, 'Info', now(), now());
insert into groupe values (nextval('seq_groupe_cod'),'coterie_12', p_test12_archer, 'Info', now(), now());
insert into groupe values (nextval('seq_groupe_cod'),'coterie_13', p_test13_archer, 'Info', now(), now());
insert into groupe values (nextval('seq_groupe_cod'),'coterie_14', p_test14_archer, 'Info', now(), now());
insert into groupe values (nextval('seq_groupe_cod'),'coterie_15', p_test15_archer, 'Info', now(), now());

SELECT INTO coterie_0 groupe_cod from groupe where groupe_nom='coterie_0';
SELECT INTO coterie_1 groupe_cod from groupe where groupe_nom='coterie_1';
SELECT INTO coterie_2 groupe_cod from groupe where groupe_nom='coterie_2';
SELECT INTO coterie_3 groupe_cod from groupe where groupe_nom='coterie_3';
SELECT INTO coterie_4 groupe_cod from groupe where groupe_nom='coterie_4';
SELECT INTO coterie_5 groupe_cod from groupe where groupe_nom='coterie_5';
SELECT INTO coterie_6 groupe_cod from groupe where groupe_nom='coterie_6';
SELECT INTO coterie_7 groupe_cod from groupe where groupe_nom='coterie_7';
SELECT INTO coterie_8 groupe_cod from groupe where groupe_nom='coterie_8';
SELECT INTO coterie_9 groupe_cod from groupe where groupe_nom='coterie_9';
SELECT INTO coterie_10 groupe_cod from groupe where groupe_nom='coterie_10';
SELECT INTO coterie_11 groupe_cod from groupe where groupe_nom='coterie_11';
SELECT INTO coterie_12 groupe_cod from groupe where groupe_nom='coterie_12';
SELECT INTO coterie_13 groupe_cod from groupe where groupe_nom='coterie_13';
SELECT INTO coterie_14 groupe_cod from groupe where groupe_nom='coterie_14';
SELECT INTO coterie_15 groupe_cod from groupe where groupe_nom='coterie_15';

INSERT INTO GROUPE_PERSO VALUES (p_test0_archer, coterie_0, 1, 1, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test1_archer, coterie_1, 1, 1, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test2_archer, coterie_2, 1, 1, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test3_archer, coterie_3, 1, 1, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test4_archer, coterie_4, 1, 1, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test5_archer, coterie_5, 1, 1, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test6_archer, coterie_6, 1, 1, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test7_archer, coterie_7, 1, 1, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test8_archer, coterie_8, 1, 1, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test9_archer, coterie_9, 1, 1, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test10_archer, coterie_10, 1, 1, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test11_archer, coterie_11, 1, 1, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test12_archer, coterie_12, 1, 1, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test13_archer, coterie_13, 1, 1, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test14_archer, coterie_14, 1, 1, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test15_archer, coterie_15, 1, 1, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);

INSERT INTO GROUPE_PERSO VALUES (p_test0_mage, coterie_0, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test1_mage, coterie_1, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test2_mage, coterie_2, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test3_mage, coterie_3, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test4_mage, coterie_4, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test5_mage, coterie_5, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test6_mage, coterie_6, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test7_mage, coterie_7, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test8_mage, coterie_8, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test9_mage, coterie_9, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test10_mage, coterie_10, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test11_mage, coterie_11, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test12_mage, coterie_12, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test13_mage, coterie_13, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test14_mage, coterie_14, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test15_mage, coterie_15, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);

INSERT INTO GROUPE_PERSO VALUES (p_test0_cac, coterie_0, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test1_cac, coterie_1, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test2_cac, coterie_2, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test3_cac, coterie_3, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test4_cac, coterie_4, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test5_cac, coterie_5, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test6_cac, coterie_6, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test7_cac, coterie_7, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test8_cac, coterie_8, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test9_cac, coterie_9, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test10_cac, coterie_10, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test11_cac, coterie_11, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test12_cac, coterie_12, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test13_cac, coterie_13, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test14_cac, coterie_14, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);
INSERT INTO GROUPE_PERSO VALUES (p_test15_cac, coterie_15, 1, 0, 1, 1, 1, 1, 1, 1, '', now(), 1, 1, 0);

return code_retour;
end;$function$

