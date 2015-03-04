<?php

namespace eleveque\CasperJsPlugin;

use PHPCI\Plugin;
use PHPCI\Builder;
use PHPCI\Model\Build;

use eleveque\CasperJsPlugin\Util\XUnitParser;

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

    protected $xUnitFilePath = '/tmp/casperOutput.xml';

    protected $testsPath = 'tests/test.js';

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

        $cmd = $phpunit . ' test %s --xunit="%s"';
        $success = $this->phpci->executeCommand($cmd, $this->testsPath, $this->xUnitFilePath);

        chdir($curdir);

        $xUnitString = file_get_contents($this->xUnitFilePath);
        try {
            $xUnitParser = new XUnitParser($xUnitString);
            $output = $xUnitParser->parse();

            $failures = $xUnitParser->getTotalFailures();
        } catch (\Exception $ex) {
            $this->phpci->logFailure($xUnitParser);
            throw $ex;
        }

        $this->build->storeMeta('phpunit-errors', $failures);
        $this->build->storeMeta('phpunit-data', $output);

        $this->phpci->logExecOutput(true);

        return $success;
    }

    /**
     * Build an args string for PHPCS Fixer.
     * @param $options
     */
    public function buildArgs($options)
    {
        if (!empty($options['testsPath'])) {
            $this->testsPath = $options['testsPath'];
        }

        if (!empty($options['xUnitFilePath'])) {
            $this->xUnitFilePath = $options['xUnitFilePath'];
        }

    }
}
