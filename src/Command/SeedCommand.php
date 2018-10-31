<?php
/**
 * seed
 * @author Yangdong Zhang <27282480@qq.com>
 * @time 2018/10/31 18:47
 */

namespace Swoft\Migration\Command;

use Swoft\Console\Bean\Annotation\Command;
use Swoft\Migration\Seed\Create;
use Swoft\Migration\Seed\Run;
use Swoft\Console\Input\Input;
use Swoft\Console\Output\Output;

/**
 * the group command list of database seed.
 *
 * @Command(coroutine=false)
 *
 * @package Swoft\Migration\Command
 */
class SeedCommand
{
    public $input;

    public $output;

    /**
     * SeedCommand constructor.
     */
    public function __construct()
    {
        $this->input  = new Input();
        $this->output = new Output();
    }

    /**
     * Create the new seeder.
     *
     * @Usage
     * <info>seed:create</info>
     *
     * @Options
     * <info>name</info> seeder Name
     *
     * @Example
     * php bin/swoft seed:create SeederName
     */
    public function create()
    {
        (new Create())->execute($this->input, $this->output);
    }

    /**
     * Run database seeders.
     *
     * @Usage
     * <info>seed:run</info>
     *   <info>seed:run -s User</info>
     *
     * @Options
     * <info>-s</info> The seeder number to seed.
     *
     * @Example
     * php bin/swoft seed:run
     */
    public function run()
    {
        (new Run())->execute($this->input, $this->output);
    }
}