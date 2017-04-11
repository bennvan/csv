<?php

require_once dirname(__FILE__).'/../syntax.php';

/**
 * @group plugin_csv
 * @group plugins
 */
class syntax_plugin_csv_test extends DokuWikiTest {

    private $delimiters = array(
        'c' => ',',
        's' => ';',
        't' => "\t"
    );

    private $enclosings = array(
        'q' => '"',
        's' => "'",
    );

    private $escapes = array(
        'q' => '"',
        'b' => '\\'
    );

    function test_files(){
        // run through all the test files
        $files = glob(__DIR__.'/csv/*.csv');
        foreach($files as $file){
            // load test csv and json files
            $csv  = file_get_contents($file);
            $file = basename($file, '.csv');
            $json = file_get_contents(__DIR__.'/json/'.$file.'.json');

            // get delimiter configs form file name
            list($delim, $enc, $esc) =  explode('-', $file);
            $delim = $this->delimiters[$delim];
            $enc = $this->enclosings[$enc];
            $esc = $this->escapes[$esc];

            // test
            $this->assertEquals(json_decode($json, true), $this->csvparse($csv, $delim, $enc, $esc), $file);
        }
    }

    /**
     * Calls the CSV line parser of our plugin and returns the whole array
     *
     * @param string $csvdata
     * @param string $delim
     * @param string $enc
     * @param string $esc
     * @return array
     */
    function csvparse($csvdata, $delim, $enc, $esc){

        $data = array();

        while($csvdata != '') {
            $line = helper_plugin_csv::csv_explode_row($csvdata, $delim, '"', '"');
            if($line !== false) array_push($data, $line);
        }

        return $data;
    }

    /**
     * check general content loading
     */
    function test_content() {
        $contents = file_get_contents(__DIR__ . '/avengers.csv');

        $opt = array(
            'hdr_rows' => 1,
            'hdr_cols' => 0,
            'span_empty_cols' => 0,
            'maxlines' => 0,
            'offset' => 0,
            'file' => '',
            'delim' => ',',
            'enclosure' => '"',
            'escape' => '"',
            'content' => ''
        );

        $data = helper_plugin_csv::prepareData($contents, $opt);
        $this->assertSame(174, count($data), 'number of rows');
        $this->assertSame(21, count($data[0]), 'number of columns');
    }
}
