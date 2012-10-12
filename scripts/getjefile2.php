<?php
$year = (isset($_GET['year'])) ? $_GET['year'] : '';
if ( $year == '') { $year="05"; }
$jefile = (isset($_GET['jefile'])) ? $_GET['jefile'] : '';
if ( $jefile ==''  ) { $jefile="11"; }



function Desc($line) {
  $len=strlen($line);
  if ($line[$len-1]==";" && $line[$len-1]=="-") {
	  return "";
  } else {
	  $sod = strpos($line,"-,");
	  if ($sod !== false) {
		  return substr($line, $sod+2);		  
	  } else {
		  return "";
	  }
  }  

}

function AcctAmt($line) {
	
$begpos = strpos($line,",-");
$line = substr($line, $begpos+2);
$lineJson =  "\"acctamt\":[";
for ($aaidx = 0; $line{3}==",";$aaidx++) {
    $eon = strpos($line,";");
    $acct = substr($line, 0, 3);
    $amt = substr($line, 4, $eon-4);
    if ($aaidx != 0) {$lineJson .= ",";};
    $lineJson .=  "{". "\"acct\":"."\"$acct\","."\"amt\":"."\"$amt\"". "}";
    $line = substr($line, $eon+1);

}
$lineJson .=  "]";  // close acctamt array of objects

return $lineJson;
	
	
} // function AcctAmt

/*
function acctamt($line) (
  // 1101,-631,25.00;777,1.50;101-,Description
  // assume perfect syntax (needs to be fixed/improved)
  





   
)
*/


$jefileFileName = "/home/nf/public_html/nf/".$year."/".$jefile;

$jefileLines = file( $jefileFileName );
echo "[\n";
for ($i=0; $i<count($jefileLines); $i++) {
  if($i!=0) echo ",";
  $line = rtrim($jefileLines[$i]);
  $desc = Desc($line);
  $mmdd = substr($line,0,4);
  $cract = substr($line,strpos($line,";")+1,3);
  $acctamt = AcctAmt($line);
  echo "{";
    echo "\"line\":\"$line\",";
	echo "\"mmdd\":\"$mmdd\",";   
	echo "\"acctamt\":{$acctamt}," ;
    echo "\"cract\":\"$cract\",";
    echo "\"desc\":\"$desc\"";;
  echo "}";
  echo "\n";
}

echo "]\n";

?>

