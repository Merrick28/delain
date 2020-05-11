
update fonction_specifique set fonc_force=30 where fonc_effet='DES' and fonc_force='1';
update fonction_specifique set fonc_force=60 where fonc_effet='DES' and fonc_force='2';
update fonction_specifique set fonc_force=100 where fonc_effet='DES' and fonc_force='3';

update bonus set bonus_valeur=30 where bonus_tbonus_libc='DES' and bonus_valeur=1;
update bonus set bonus_valeur=60 where bonus_tbonus_libc='DES' and bonus_valeur=2;
update bonus set bonus_valeur=100 where bonus_tbonus_libc='DES' and bonus_valeur=3;