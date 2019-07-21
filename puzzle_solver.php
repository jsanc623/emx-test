<?php

class PuzzleSolver {
    public function __construct() {
    }

    /**
     * Solve for the following query
     *  Please+solve+this+puzzle%3A%0A+ABCD%0AA-%3E--%0AB--%3E-%0AC--%3D-%0AD--%3C-%0A
     *
     *  if cell is - and mirrored cell is empty, turn it to > and mirrored to <
     *  if cell is - but mirrored cell is not empty, turn it to opposite of mirrored
     *
     *  where:         -  0        >  1     Changing the symbols to numbers
     *                 < -1        =  2      helped me to visualize a solution
     *  thus:
     *        A  B  C  D      A  B  C  D
     *      A -  -  >  -   A  =  <  >  >
     *      B >  -  -  -   B  >  =  >  >
     *      C -  -  =  -   C  <  <  =  >
     *      D -  -  <  -   D  <  <  <  =
     *  becomes:
     *      A 0  0  1  0   A  2 -1  1  1
     *      B 1  0  0  0   B  1  2  1  1
     *      C 0  0  2  0   C -1 -1  2  1
     *      D 0  0 -1  0   D -1 -1 -1  2
     *        A  B  C  D      A  B  C  D
     *
     * @param $req_str
     * @return string
     */
    public function abcd($req_str) {
        if (strlen($req_str) == 0) {
            return FALSE;
        }

        //  URL decode, remove whitespace, explode by newline, then filter out any empty elements
        $clean = array_filter(explode(PHP_EOL, str_replace(' ', '', urldecode($req_str))));
        array_shift($clean); # Remove Pleasesolvethispuzzle:
        $header = ' ' . array_shift($clean) . PHP_EOL; # Capture and remove ABCD... header for output

        $acceptable_chars = [
            '-' => ['char' => '-', 'opp' => '>'],
            '=' => ['char' => '=', 'opp' => '='],
            '<' => ['char' => '<', 'opp' => '>'],
            '>' => ['char' => '>', 'opp' => '<'],
            'default' => ['char' => '<', 'opp' => '>'],
            'empty' => ['char' => '-', 'opp' => NULL],
        ];

        // Set up our board
        $board = [];
        $x_count = 0;
        $y_count = 0;
        $keys = [];
        foreach($clean as $line){
            $line = str_split($line);
            $keys[$y_count] = array_shift($line);
            $board[] = $line;
            $x_count = count($line);
            $y_count += 1;
        }

        // Process our board
        $x_equal_location = 0;
        for($y = 0; $y < $y_count; $y++){
            for($x = $x_count; $x >= 0; $x--){ // cache invalidation and naming things...
                // the x position of '=' increases by 1 every time y increases by 1,
                // creating a diagonal which acts as our mirror line
                if($x == $x_equal_location){
                    $board[$y][$x] = '=';
                }

                // we don't care about explicitly checking the lower mirror
                if($x < $x_equal_location){
                    continue;
                }

                // gather our mirror coordinates
                $mirror_x = $x;
                $mirror_y = $y;

                // if both normal and mirror are empty, set normal to '>' and mirror to '<'
                // But first, check if we have a nearby cell to the left with a value
                if($board[$y][$x] == '-' && $board[$mirror_x][$mirror_y] == '-'){
                    $board[$y][$x] = $acceptable_chars['default']['char'];
                    $board[$mirror_x][$mirror_y] = $acceptable_chars['default']['opp'];
                } else if ($board[$y][$x] == '-' && $board[$mirror_x][$mirror_y] != '-'){
                    // if normal is empty, but mirrored is not, set normal to opposite of mirrored
                    $board[$y][$x] = $acceptable_chars[$board[$mirror_x][$mirror_y]]['opp'];
                } else if ($board[$y][$x] != '-' && $board[$mirror_x][$mirror_y] == '-'){
                    // if normal is not empty, but mirrored is, set mirrored to opposite of normal
                    $board[$mirror_x][$mirror_y] = $acceptable_chars[$board[$y][$x]]['opp'];
                }
            }
            $x_equal_location++; # Increase the x position of '=' by 1 for the next line
        }

        // Display our board
        $output = $header;
        foreach($board as $k => $line){
            $output .= $keys[$k];
            foreach($line as $cell){
                $output .= $cell;
            }
            $output .= PHP_EOL;
        }

        return $output;
    }

    /**
     * THIS IS A FAILED FIRST ATTEMPT TRYING TO DISCERN A RULE-BASED SOLUTION
     * Solve for the following query
     *  Please+solve+this+puzzle%3A%0A+ABCD%0AA-%3E--%0AB--%3E-%0AC--%3D-%0AD--%3C-%0A
     * @param $req_str
     * @return string
     */
    public function abcd_first_solution($req_str) {
        // Sanity check
        if(strlen($req_str) == 0){
            return FALSE;
        }
        //  URL decode, remove whitespace, explode by newline, then filter out any empty elements
        $clean = array_filter(
            explode("\n",
                str_replace(' ', '', urldecode($req_str) )
            )
        );

        array_shift($clean); # Remove Pleasesolvethispuzzle:
        $header = ' ' . $clean[0]; # Capture ABCD... header for output
        array_shift($clean); # Remove ABCD... header

        print_r($clean);
        $rules = [
            '->' => '=>',
            '=-' => '=>',
            '<-' => '<='
        ];
        $acceptable_chars = ['-' => '-',
                             '=' => '=',
                             '<' => '<',
                             '>' => '>'];

        $lines = [];
        foreach($clean as $line){
            $parts = str_split($line);
            $parts_count = count($parts);

            for($i = 0; $i <= $parts_count - 2; $i++){
                // Check that our char is in the acceptable characters array. Use isset() rather than in_array because
                // isset is O(1) since it uses a hash search rather than checking each element
                if( isset($acceptable_chars[$parts[$i]]) ){
                    // Build our two char key
                    $dual = $parts[$i] . ($parts[$i + 1] ?: '');

                    // Check if we have a rule match, then build the line accordingly
                    // (pad to the left with "<" and to the right with ">")
                    if( isset($rules[$dual]) ){
                        $match = $rules[$dual];
                        $lpad = str_repeat('<', $i - 1);
                        $rpad = str_repeat('>', ($parts_count - 2 - $i) );
                        $lines[] = $lpad . $match . $rpad;
                    }
                }
            }
        }

        return $header . PHP_EOL . implode(PHP_EOL, $lines);
    }
}