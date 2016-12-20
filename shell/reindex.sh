#!/bin/bash
source `dirname $0`/env
$psql << EOF
reindex table ligne_evt;
reindex table perso;
reindex table perso_competences;
truncate table perso_type_competences;
reindex table perso_type_competences;
reindex table riposte;
EOF
