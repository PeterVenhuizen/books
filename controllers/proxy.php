<?php
    // myproxy.php
    header('Content-type: application/json');
    $q = (!empty($_GET['q'])) ? $_GET['q'] : 'I, Robot';
    $offset = (!empty($_GET['offset'])) ? $_GET['offset'] : '0';
    $limit = (!empty($_GET['limit'])) ? $_GET['limit'] : '10';
    
    // possibly include offset
    $results = file_get_contents("https://api.bol.com/catalog/v4/search?q={$q}&dataoutput=products&ids=8299&apikey=4B1BD213D64A410189A40D14E7577007&format=json&limit=${limit}&offset=${offset}&includeattributes=true");
    echo $results;
?>
