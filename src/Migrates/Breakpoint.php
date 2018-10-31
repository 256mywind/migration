<?php
/**
 * Toggle the breakpoint.
 * @author Yangdong Zhang <27282480@qq.com>
 * @time 2018/10/30 16:58
 */

namespace Swoft\Migration\Migrates;

use Swoft\Console\Input\Input;
use Swoft\Console\Output\Output;
use Swoft\Migration\Migrate;

class Breakpoint extends Migrate
{
    /**
     * Toggle the breakpoint.
     *
     * @param Input $input
     * @param Output $output
     */
    public function execute(Input $input, Output $output)
    {
        $version   = $input->getOpt('t');
        $removeAll = $input->getOpt('r');

        if ($version && $removeAll) {
            throw new \InvalidArgumentException('Cannot toggle a breakpoint and remove all breakpoints at the same time.');
        }

        // Remove all breakpoints
        if ($removeAll) {
            $this->removeBreakpoints();
        } else {
            // Toggle the breakpoint.
            $this->toggleBreakpoint($version);
        }
    }

    protected function toggleBreakpoint($version)
    {
        $migrations = $this->getMigrations();
        $versions   = $this->getVersionLog();

        if (empty($versions) || empty($migrations)) {
            return;
        }

        if (null === $version) {
            $lastVersion = end($versions);
            $version     = $lastVersion['version'];
        }

        if (0 != $version && !isset($migrations[$version])) {
            $this->output->writeln(sprintf('<comment>warning</comment> %s is not a valid version', $version));
            return;
        }

        $this->getAdapter()->toggleBreakpoint($migrations[$version]);

        $versions = $this->getVersionLog();

        $this->output->writeln(' Breakpoint ' . ($versions[$version]['breakpoint'] ? 'set' : 'cleared') . ' for <info>' . $version . '</info>' . ' <comment>' . $migrations[$version]->getName() . '</comment>');
    }

    /**
     * Remove all breakpoints
     *
     * @return void
     */
    protected function removeBreakpoints()
    {
        $this->output->writeln(sprintf(' %d breakpoints cleared.', $this->getAdapter()->resetAllBreakpoints()));
    }
}