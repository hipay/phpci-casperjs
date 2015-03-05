<?php

namespace Demorose\PHPCI\Plugin\Util;

use PHPCI\Helper\Lang;

/**
 * Processes XUnit format strings into usable test result data.
 */
class XUnitParser
{
    /**
     * @var string
     */
    protected $xUnitString;
    protected $failures = 0;

    /**
     * Create a new XUnit parser for a given string.
     * @param string $xunitString The XUnit format string to be parsed.
     */
    public function __construct($xUnitString)
    {
        $this->xUnitString = trim($xUnitString);
    }

    /**
     * Parse a given XUnit format string and return an array of tests and their status.
     */
    public function parse()
    {
        $xml=simplexml_load_string($this->xUnitString) or die("Error: Cannot create object");

        $rtn = array();
        $testNumber = 0;
        $failures = 0;

        if (!$xml->testsuite) {
        }
        foreach ($xml->testsuite as $testsuite) {
            foreach ($testsuite->testcase as $testcase) {
                $item = array(
                    'pass' => true,
                    'suite' => $testNumber,
                    'test' => $testcase->attributes()->name,
                );
                if ($testcase->failure) {
                    $item['pass'] = false;
                    $this->failures++;
                }

                $rtn[] = $item;
                $testNumber ++;
            }
        }

        return $rtn;
    }

    /**
     * Get the total number of failures from the current XUnit file.
     * @return int
     */
    public function getTotalFailures()
    {
        return $this->failures;
    }
}

