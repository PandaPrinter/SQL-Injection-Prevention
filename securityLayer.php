<?php

$globalVar = "";

function queryCheck($dbh, $query)
{
    $backtrace = debug_backtrace();
    $line = $backtrace[0]['line'];
    //print "This function was called from line $line of test1.php<br />";

    $sourceFile = "test1.php";
    $lines = file($sourceFile);
    $inputArr = [];
    for ($i = $line - 1; $i >= 0; $i--) {
        $pos = strpos($lines[$i], substr($query, 0, 5));
        // contain the content of query string
        if ($pos !== false) {
            // get the content of query string
            $tempQuery = substr($lines[$i], $pos);
            $strlen = strlen($tempQuery);
            for ($j = 0; $j <= $strlen; $j++) {
                $char = substr($tempQuery, $j, 1);
                // get the begin index of a input string
                if ($char === '$') {
            
                    // case $_POST['name']
                    if (substr($tempQuery, $j + 1, 1) === '_') {
                        $pos1 = strpos($tempQuery, ']', $j + 1);
                        $tempInput = substr($tempQuery, $j + 7, $pos1 - $j - 7);
                        $input = str_replace("'", "", $tempInput);
                        $post = $input;
                        $globalVar = $_POST[$post];
                        $inputArr[] = $globalVar;
                    } 
                    // case ${_POST['name']}
                    else if (substr($tempQuery, $j + 1, 6) === '{_POST') {
                        $pos1 = strpos($tempQuery, ']', $j + 1);
                        $tempInput = substr($tempQuery, $j + 8, $pos1 - $j - 8);
                        $input = str_replace("'", "", $tempInput);
                        $post = $input;
                        $globalVar = $_POST[$post];
                        $inputArr[] = $globalVar;
                    } 
                    // case ${u}
                    else if (substr($tempQuery, $j + 1, 1) === '{') {
                        $pos1 = strpos($tempQuery, '}', $j + 1);
                        $tempInput = substr($tempQuery, $j + 1, $pos1 - $j);
                        $find = array("{", "}");
                        $input = str_replace($find, "", $tempInput);
                        global ${$input};
                        $globalVar = ${$input};
                        $inputArr[] = $globalVar;
                    } 
                    // case $u
                    else {
                        $pos1 = strpos($tempQuery, ')', $j + 1);
                        $input = substr($tempQuery, $j + 1, $pos1 - $j - 1);
                        global ${$input};
                        $globalVar = ${$input};
                        $inputArr[] = $globalVar;
                    }
                }
            }
            break;
        } else {
            continue;
        }
    }
    
    $count = count($inputArr);
    $tempArr = array_fill(0, $count, '?');
    $tempPreparedQuery = str_replace($inputArr, $tempArr, $query);

    $preparedQuery = str_replace("'", "", $tempPreparedQuery);
    $stmt = $dbh->prepare($preparedQuery);
    $stmt->execute($inputArr);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);    
    echo "Results: " . "<br/>\n";
    print_r($result);
    echo "<br/>\n";
        
    /* use mysqli
    $stmt = $dbh->prepare($preparedQuery);
    $stmt->bind_param('ss', $a, $b);
    $a = $inputArr[0];
    $b = $inputArr[1];
    $stmt->execute();
    $res = $stmt->get_result();
    var_dump($res->fetch_all());
    */

}

?>
