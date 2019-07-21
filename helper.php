<?php

class Helper extends Log {
    /**
     * Variable printer
     * @param null $msg
     */
    public function p($msg = NULL) {
        if (is_array($msg)) {
            print_r($msg);
        } else {
            echo $msg;
        }
        echo PHP_EOL;
    }

    /**
     * Determine how many years since a given year to the current year and returns
     * the word representation of that number in AD
     * @param $year int
     * @return string
     */
    function years_since_word($year) {
        $year = (int)$year;
        if($year <= 0){
            return FALSE;
        }

        $number_formatter = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $word = $number_formatter->format(date("Y") - $year);
        if($word !== FALSE) {
            return ucfirst($word);
        }
        return FALSE;
    }
}

/**
 * Class Helpers
 * A collection of helper functions for the EMX test
 */
class Log {
    private $log_handler = NULL;

    /**
     * Helpers constructor
     * @param null $log_handler
     */
    public function __construct($log_handler = NULL) {
        if(is_null($log_handler)){
            $log_handler = fopen('/tmp/emx.log', 'a+');
        }
        $this->log_handler = $log_handler;
    }

    /**
     * @param $msg
     * @return bool
     */
    private function write_to_log($msg){
        $log_line = date('[Y-m-d h:i:s]') . " " . $msg . PHP_EOL;
        $ok = fwrite($this->log_handler, $log_line);
        if (!$ok) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * @param $vars string|float|int|array
     * @param $spec_val NULL If non-null, $vars must be a key string
     * @return bool|float|int
     */
    public function log($vars, $spec_val = NULL) {
        if(!is_null($spec_val) && !is_array($vars)){
            return $this->write_to_log($vars . ' => ' . $spec_val);
        } else {
            if (is_array($vars)) {
                foreach ($vars as $v) {
                    return $this->log($v);
                }
            }
        }
        return TRUE;
    }
}