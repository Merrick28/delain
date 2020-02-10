# coding: utf-8

import re, sys, getopt
fw= open("../web/www/jeu_test/admin_factions_missions.tmp","w+")

with open ('../web/www/jeu_test/admin_factions_missions.php', 'r' ) as f:
    content = f.read()
content_new = re.sub("\$db->query\(\$([a-zA-Z_]*)\)", r'$stmt = $pdo->query($\1)', content, flags = re.M)
fw.write(content_new)
fw.close
