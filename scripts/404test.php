<?php
$year="05"; 
$jefile="12";


$jefileFileName = "/home/nf/public_html/nf/".$year."/".$jefile;


if( ! file_exists($jefileFileName)) {
	header("HTTP/1.0 404 Not Found",true,404);
	echo "does not exist\n";
};


$line = "1101,-631,25.00;777,1.50;101-,Description";
$begpos = strpos($line,",-");
$line = substr($line, $begpos+2);
echo "\"acctamt\":[";
$lineJson =  "\"acctamt\":[";
for ($aaidx = 0; $line{3}==",";$aaidx++) {
    $eon = strpos($line,";");
    $acct = substr($line, 0, 3);
    $amt = substr($line, 4, $eon-4);
    if ($aaidx != 0) {echo ","; $lineJson .= ",";};
    echo "{";
        echo "\"acct\":";
        echo "\"$acct\",";
        echo "\"amt\":";
        echo "\"$amt\"";
    echo "}";
    $lineJson .=  "{". "\"acct\":"."\"$acct\","."\"amt\":"."\"$amt\"". "}";
    $line = substr($line, $eon+1);

}
echo "]\n";  // close acctamt array of objects
$lineJson .=  "]";
echo "$lineJson\n";


$ret = strpos($line,"x");
echo "\nret=$ret\n";

if ($ret == false) {
	echo "ret=false\n";
}

?>
