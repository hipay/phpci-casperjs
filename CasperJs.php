<?php

namespace Demorose\PHPCI\Plugin;

use PHPCI\Plugin;
use PHPCI\Builder;
use PHPCI\Model\Build;

use Demorose\PHPCI\Plugin\Util\XUnitParser;

/**
* CasperJs - Allows CasperJS testing.
* @author       Emmanuel LEVEQUE <eleveque@hipay.com>
*/
class CasperJs implements Plugin
{
    /**
     * @var \PHPCI\Builder
     */
    protected $phpci;

    /**
     * @var \PHPCI\Model\Build
     */
    protected $build;

    protected $x_unit_file_path = '/tmp/casperOutput.xml';

    protected $tests_path = 'tests/test.js';

    /**
     * Standard Constructor
     */
    public function __construct(Builder $phpci, Build $build, array $options = array())
    {
        $this->phpci = $phpci;
        $this->build = $build;

        $this->buildArgs($options);
    }

    /**
     * Run CasperJS tests.
     * @return bool
     */
    public function execute()
    {
        $this->phpci->logExecOutput(false);

        $casperJs = $this->phpci->findBinary('casperjs');
        if (!$casperJs) {
            $this->phpci->logFailure(Lang::get('could_not_find', 'casperjs'));
            return false;
        }

        $curdir = getcwd();
        chdir($this->phpci->buildPath);

        $cmd = $casperJs . " test $this->tests_path --xunit=$this->x_unit_file_path";
        $success = $this->phpci->executeCommand($cmd);

        chdir($curdir);

        $xUnitString = file_get_contents($this->x_unit_file_path);
        try {
            $xUnitParser = new XUnitParser($xUnitString);
            $output = $xUnitParser->parse();

            $failures = $xUnitParser->getTotalFailures();
        } catch (\Exception $ex) {
            $this->phpci->logFailure($xUnitParser);
            throw $ex;
        }

        $this->build->storeMeta('casperJs-errors', $failures);
        $this->build->storeMeta('casperJs-data', $output);

        $this->phpci->logExecOutput(true);

        return $success;
    }

    /**
     * Build an args string for PHPCS Fixer.
     * @param $options
     */
    public function buildArgs($options)
    {
        if (!empty($options['tests_path'])) {
            $this->tests_path = $options['tests_path'];
        }

        if (!empty($options['x_unit_file_path'])) {
            $this->x_unit_file_path = $options['x_unit_file_path'];
        }

    }
}
