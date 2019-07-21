<?php
require(__DIR__ . '/helper.php');
require(__DIR__ . '/puzzle_solver.php');

$helper = new Helper();
$solver = new PuzzleSolver();

// Log the raw request
$helper->log($_GET);

// Log each part of the request
foreach($_REQUEST as $key => $value) {
    $helper->log($key, $value);
}

// Normalize our query
$q = str_replace(" ", "_", strtolower(trim($_REQUEST['q'])));

// Load our JSON data from a file. Normally, we'd get this from a DB.
$data = json_decode(file_get_contents('data.json'), true);

// If no data, exit early.
if(is_null($data)){
    exit(1);
}

// Replace keyword in our years with years since
if($q == 'years'){
    $keyword = 'YEARS_SINCE';
    if(strpos($data[$q], $keyword) !== FALSE){
        $year = end(explode(" ", $data[$q]));
        $word = $helper->years_since_word($year);
        if(!$word) {
            $word = "";
        }
        $data[$q] = str_replace($keyword, $word, $data[$q]);
    }
}

// Puzzle solver, otherwise fetch from data
if($q == 'puzzle') {
    $output = $solver->abcd($_REQUEST['d']);
} else {
    $output = $data[$q];
}

echo $output;