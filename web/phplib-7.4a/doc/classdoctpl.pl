#!/usr/bin/perl
##
## Copyright (c) 1999-2000 Internet Images Srl
##                    Massimiliano Masserelli
##
## Synopsys: 
##   classdoctpl.pl < classdeclaration.inc
##
## Output:
##   A file for every class defined in classdeclaration.inc, named after
##   each class with ".sgml" extension containing a workable template
##   to start with.
##   A line to stdout for every class method and instance, just to tell
##   you what's going on.
##
## Bugs/Features:
##   Instances which are initialized in declaration with defaults, are 
##   reported as such in output template[s]. This DOES NOT follow
##   phplib documentation style, but may be an useful reminder when
##   editing the template.
##
## WARNING!!
##   This script is a VERY QUICK HACK(tm). If you follow phplib codestyle
##   should work. It works for me(tm), YMMV.
##
## $Id: classdoctpl.pl,v 1.2 2000/07/12 18:22:31 kk Exp $
##

$pcount = 0;
$cclass = "";

while (<>) {
  chop;
  while (?\}?) {
    $pcount --;
  }
  reset;
  if ($pcount == 0) {
    if (/class\s+(\w+)\s+{/) {
      # Beginning of a class
      $cclass = $1;
      push(@classes, $cclass);
      @{$variables{$cclass}} = ();
      @{$methods{$cclass}} = ();
    }
  } elsif ($pcount == 1) {
#    if (/function\s+(\w+)\s*\(([a-zA-Z ,\$=_]*)\)\s*\{/) {
    if (/function\s+(\w+)\s*\((.*)\)\s*\{/) {
      push(@{$methods{$cclass}}, sprintf("%s(%s)", $1, $2));
      print $cclass . "->" . $1 . "(" . $2 . ")\n";
    }
    if (/var\s+(\$\w+)\s*=\s*(\S.*)\s*;/) {
      push(@{$variables{$cclass}}, sprintf("%s = %s", $1, $2));
      print $cclass . "->" . $1 . "=" . $2 . "\n";
    }
    if (/var\s+(\$\w+)\s*;/) {
      push(@{$variables{$cclass}}, sprintf("%s", $1));
      print $cclass . "->" . $1 . "\n";
    }
  }
  while (?\{?) {
    $pcount++;
  }
  reset;
}

for $cl (@classes) {
  open(OUT, "> $cl.sgml") || print "Cannot open $cl.sgml\n", next;
  print OUT '<!-- $Id: classdoctpl.pl,v 1.2 2000/07/12 18:22:31 kk Exp $ -->' . "\n";
  print OUT sprintf("<sect1>%s\n<p>\n\n", tosgml($cl));
  print OUT "\n";
  print OUT "<sect2>Instance variables\n";
  print OUT "<p>\n";
  print OUT "\n";
  print OUT "<table>\n";
  print OUT "<tabular ca=\"\">\n";
  for $vr (@{$variables{$cl}}) {
    print OUT sprintf("%s<colsep>Description<rowsep>\n", tosgml($vr));
  }
  print OUT "</tabular>\n";
  print OUT "<caption>Accessible instance variables.</caption>\n";
  print OUT "</table>\n";
  print OUT "\n";
  print OUT "<table>\n";
  print OUT "<tabular ca=\"\">\n";
  print OUT "</tabular>\n";
  print OUT "<caption>Internal instance variables.</caption>\n";
  print OUT "</table>\n";
  print OUT "\n";

  print OUT "<sect2>Instance methods\n";
  print OUT "<p>\n";
  print OUT "\n";
  print OUT "<sect3>Accessible instance methods\n";
  print OUT "<p>\n";
  print OUT "\n";
  print OUT "<descrip>\n";
  for $mt (@{$methods{$cl}}) {
    print OUT sprintf("<tag>%s</tag>\n", tosgml($mt));
    print OUT "<p>\n";
    print OUT "\n";
  }
  print OUT "</descrip>\n";
  print OUT "\n";
  print OUT "<sect3>Internal instance methods\n";
  print OUT "<p>\n";
  print OUT "<descrip>\n";
  print OUT "</descrip>\n";
  print OUT "\n";

  print OUT "<sect2>Example\n";
  print OUT "<p>\n";
  print OUT "\n";
  print OUT "Use\n";
  print OUT "\n";
  print OUT "<tscreen><code>\n";
  print OUT "\n";
  print OUT "</code></tscreen>\n";
  close(OUT);
}

sub tosgml($) {
  my $string = shift;
  $string =~ s/_/&lowbar;/g;
  $string =~ s/>/&gt;/g;
  $string =~ s/</&lt;/g;
  $string =~ s/\[/&lsqb;/g;
  $string =~ s/\]/&rsqb;/g;

  return $string;
}


