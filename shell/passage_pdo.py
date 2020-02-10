# coding: utf-8

import re, sys, argparse

parser = argparse.ArgumentParser()
parser.add_argument('inputfile', help='Input file')
args = parser.parse_args()




with open (args.inputfile, 'r' ) as f:
    content = f.read()
content_new = re.sub("\$db->query\(\$([a-zA-Z_]*)\)", r'$stmt = $pdo->query($\1)', content, flags = re.M)
content_new = content_new.replace("while($db->next_record())","while($result = $stmt->fetch())")
content_new = content_new.replace("if ($db->next_record())","if($result = $stmt->fetch())")
content_new = content_new.replace("$db->next_record()","$result = $stmt->fetch()")
content_new = re.sub("\$db->f\('([a-zA-Z_]*)'\)", r"$result['\1']", content_new, flags = re.M)
content_new = re.sub("\$db->f\(\"([a-zA-Z_]*)\"\)", r"$result['\1']", content_new, flags = re.M)
f = open (args.inputfile, 'w+' )
f.seek(0)
f.write(content_new)
f.truncate()
f.close


