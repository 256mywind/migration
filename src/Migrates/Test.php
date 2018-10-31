<?php
/**
 * just test
 * @author Yangdong Zhang <27282480@qq.com>
 * @time 2018/10/29 17:59
 */

namespace Swoft\Migration\Migrates;

use Swoft\Console\Input\Input;
use Swoft\Console\Output\Output;
use Swoft\Migration\Migrate;

class Test extends Migrate
{
    /**
     * just test
     *
     * @param Input $input
     * @param Output $output
     */
    public function execute(Input $input, Output $output)
    {
        $output->writeln('<info>success!</info>');
    }
}