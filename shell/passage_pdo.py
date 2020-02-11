# coding: utf-8

import re, sys, argparse

parser = argparse.ArgumentParser()
parser.add_argument('inputfile', help='Input file')
args = parser.parse_args()




with open (args.inputfile, 'r' ) as f:
    content = f.read()
content_new = re.sub("\$db->query\(\$([a-zA-Z1-9_]*)\)", r'$stmt = $pdo->query($\1)', content, flags = re.M)
content_new = content_new.replace("while($db->next_record())","while($result = $stmt->fetch())")
content_new = content_new.replace("if ($db->next_record())","if($result = $stmt->fetch())")
content_new = content_new.replace("$db->next_record()","$result = $stmt->fetch()")
content_new = content_new.replace("$db->nf()","$stmt->rowCount()")
content_new = re.sub("\$db->f\('([a-zA-Z1-9_]*)'\)", r"$result['\1']", content_new, flags = re.M)
content_new = re.sub("\$db->f\(\"([a-zA-Z1-9_]*)\"\)", r"$result['\1']", content_new, flags = re.M)
# on a fait le "normal" on fait maintenant les autres bases
content_new = re.sub("\$db([a-zA-Z1-9_]*)->query\(\$([a-zA-Z_]*)\)", r'$stmt\1 = $pdo->query($\2)', content_new, flags = re.M)
content_new = re.sub("while(\$db([a-zA-Z1-9_]*)->next_record\(\))", r'while($result\1 = $stmt\1->fetch())', content_new, flags = re.M)
content_new = re.sub("if (\$db([a-zA-Z1-9_]*)->next_record\(\))", r'if($result\1 = $stmt\1->fetch())', content_new, flags = re.M)
content_new = re.sub("\$db([a-zA-Z1-9_]*)->next_record\(\)", r'$result\1 = $stmt\1->fetch()', content_new, flags = re.M)
content_new = re.sub("\$db([a-zA-Z1-9_]*)->nf\(\)", r'$stmt\1->rowCount()', content_new, flags = re.M)
content_new = re.sub("\$db([a-zA-Z1-9_]*)->f\('([a-zA-Z_]*)'\)", r"$result\1['\2']", content_new, flags = re.M)
content_new = re.sub("\$db([a-zA-Z1-9_]*)->f\(\"([a-zA-Z_]*)\"\)", r"$result\1['\2']", content_new, flags = re.M)
content_new = re.sub("\$db([a-zA-Z1-9_ =]*)new base_delain;", r"", content_new, flags = re.M)

f = open (args.inputfile, 'w+' )
f.seek(0)
f.write(content_new)
f.truncate()
f.close


