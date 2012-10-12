#!/usr/bin/perl
print "Content-type: text/html\n\n";
use CGI;
my $q = new CGI;
print "\n\n";

$year = $q->param('year');


open(INPUT, "</home/jkozik/public_html/nf/$year/chart")
   or die "Couldn't open /home/jkozik/public_html/nf/$year/chart: $!\n";

while (<INPUT>) {
  print;
  print "\n<br>";
}
close (INPUT);

