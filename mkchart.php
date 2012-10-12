<?php

if ( getenv("YEAR")!="" ) {
    $year = getenv("YEAR");
} else {
    $year = "05";
}

if ( getenv("F")=="nf" ) {
    $NFROOT = "c:/nf/";
} else if ( getenv("F")!="" ) {
    $NFROOT = getenv("F");
} else {
    // $NFROOT = "c:/nf/"; 
    $NFROOT = "/home/nf/public_html/nf/";
}
echo "year-$year NFROOT-$NFROOT\n";



$mm = '';
//$year = '05';
$months = array ("00", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");

echo "mkchart: \n";
echo "Make chart of accounts file for new year, based on current year's file.\n";
echo "Using root=$NFROOT, year=$year\n";
echo "1. Verify the chart file for the current year exists.\n";
echo "2. Verify the directory for the new year exists.  If not create it.\n";
echo "3. Verify that the chart file for the new year does not exist.\n";
echo "4. Make chart file for new year.\n";
echo "The chart file has a field that indicates when an Account is new, or the account\n";
echo "number changed.  This program keeps these fields up to date.  Used to be done manually.\n";

/*
** mkchart: Create chart of accounts file for new year
** Input:  /nf/YY/chart
** - a,115,Hawthorn Credit Union,115    <- most typical.  2nd 115 means prior year value
** - a,20f,Audi A8L                     <- new entry, no prior year.  For next year, this 
**                                         wll have a ,20f added
** - a,261,CMA Equities,261,s           <- The s mean Summary account.  The Analdtl report uses
**                                         this as a special totaling flag.
** Output: /nf/YY+1/chart
** - creates directory, if does not exist
** - will not over write
** - fills in lastyearacct field
*/


/* Current chart file must exist */
$chartfilename = $NFROOT.$year."/chart";
if( file_exists( $chartfilename ) === FALSE ) {
    echo "$chartfilename does not exist.  Exiting.\n";
    exit;
}

/* Try to open current year chart file */
$chartfilelines = file( $chartfilename );
if ( $chartfilelines == FALSE ) {
    echo "Unable to open $chartfilename. Exiting.\n";
    exit;
}

/* Create next year's $NFROOT folder.  E.g. $year-05, next year is 06 */
$yearpp = sprintf("%02d",$year+1);
echo "yearpp=$yearpp\n";
$nextyeardir = $NFROOT.$yearpp;
if ( !is_dir( $nextyeardir ) ) {
    echo "Creating $nextyeardir\n";
    $ret = mkdir($nextyeardir);
    if ($ret == FALSE) {
        echo "Unable to create directory.  Exiting.\n";
        exit;
    }
}

/* Verify that in next years directory no chart file exisits */
$nextyearchartfilename = $nextyeardir."/chart";
if ( file_exists ($nextyearchartfilename) ) {
    echo "$yearpp chart already exists.  Exiting.\n";
    exit;
}
echo "Creating file: $nextyearchartfilename\n";

/* loop through chart file...
** Input:  $chartfilelines
** Output: $nextyearchartfilename
** Processing:
** - verify basic formatting
** - parse out fields:
**   $tag, $acct, $desc, $lastyearacct, $subacct
** - format chart for next year:
**   a,$acct,$desc,$acct(,s)
** Note:  for next year, the lastyearacct field equals acct
*/
$sacnt = 0;
$lyacnt = 0;
for ($i=0; $i<count($chartfilelines); $i++) {
    /*
    ** Parse each line of chartfile into PHP varables
    ** a,101,Checking,101,s
    ** $tag,$acct,$desc,$lastyearacct,$subacct
    */
    $chartline = trim($chartfilelines[$i]);
    $line = $chartline;
    /* Basic check of charfile line */
    if( !preg_match("/^a,(\d|\d\d|\d\d[0-9a-z]),[^,?]*$|(,(\d|\d\d|\d\d[0-9a-z])?)(,s$)?/",$line) ) {
        echo $line." <--?\n";
    }

    /* $subacct */
    $subacct = false;
    if ( substr($line, -2) == ",s" ) {
        $line = substr($line, 0, strlen($line)-2);
        $subacct = true;
    }
    
    /* $lastyearacct */
    /* Pulling lastyearacct off the end of the line, this lets
    ** me allow commas in the description.  
    ** (Shouldnt be there, but allow anyway)
    */
    $lastyearacct="";
    if(preg_match("/,\d\d[0-9a-z]$|,\d\d$|,\d$/",$line)) {
        $ridx = strrpos($line,",");
        $lastyearacct = substr($line, $ridx+1);
        $line = substr($line, 0, $ridx);
    }

    /* $tag, $acct, $desc */
    list( $tag, $acct, $desc ) = explode(",", $line);
    //echo $tag." ".$acct." ".$desc." ".$lastyearacct." ".($subacct ? "TRUE" : "FALSE")."\n";

    /* Create new record */
    $nextyearchartfilelines[$i] = sprintf("a,%s,%s,%s",$acct,$desc,$acct);
    if( $subacct ) {
        $sacnt++;
        $nextyearchartfilelines[$i] = $nextyearchartfilelines[$i].",s";
    }
    $nextyearchartfilelines[$i] = $nextyearchartfilelines[$i].PHP_EOL;
    if ($lastyearacct == "") { 
        $lyacnt++;
    }
    //echo $chartline."\n".$nextyearchartfilelines[$i]."\n";
    
}

/*
** Write file (should work)
*/
$ret = file_put_contents($nextyearchartfilename, $nextyearchartfilelines);
if ($ret !== FALSE) {
    echo "chart written.\n";
    echo "- record count-".count($nextyearchartfilelines)."\n";
    echo "- new accounts-$lyacnt\n";
    echo "- sub account tags-$sacnt\n";
} else {
    echo "Unable to write chart\n";
}

exit;


?>
