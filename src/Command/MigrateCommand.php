<?php
/**
 * base
 * @author Yangdong Zhang <27282480@qq.com>
 * @time 2018/10/29 9:57
 */
namespace Swoft\Migration\Command;

use Swoft\Console\Bean\Annotation\Command;
use Swoft\Migration\Migrates\Breakpoint;
use Swoft\Migration\Migrates\Create;
use Swoft\Migration\Migrates\Down;
use Swoft\Migration\Migrates\Status;
use Swoft\Migration\Migrates\Test;
use Swoft\Migration\Migrates\Up;


/**
 * the group command list of database migration
 *
 * @Command(coroutine=false)
 * @package Swoft\Migration\Command
 */
class MigrateCommand
{
    /**
     * Create the new migration.
     *
     * @Usage
     * <info>migrate:create</info>
     *
     * @Options
     * <info>name</info> Migration Table Name
     *
     * @Example
     * php bin/swoft migrate:create TableName
     */
    public function create()
    {
        (new Create())->execute(\input(), \output());
    }

    /**
     * Migrate the database.
     *
     * @Usage
     * <info>migrate:up</info>
     *   <info>migrate:up -t 20110103081132</info>
     *   <info>migrate:up -d 20110103</info>
     *
     * @Options
     * <info>-t</info> The version number to migrate to.
     *   <info>-d</info> The date to migrate to.
     *
     * @Example
     * php bin/swoft migrate:up
     */
    public function up()
    {
        (new Up())->execute(\input(), \output());
    }

    /**
     * Rollback the last or to a specific migration.
     *
     * @Usage
     * <info>migrate:down</info>
     *   <info>migrate:down -t 20181030044142</info>
     *   <info>migrate:down -d 20181030</info>
     *   <info>migrate:down -f</info>
     *
     * @Options
     * <info>-t</info> The version number to rollback to
     *   <info>-d</info> The date to rollback to
     *   <info>-f</info> Force rollback to ignore breakpoints
     * @Example
     * php bin/swoft migrate:down
     */
    public function down()
    {
        (new Down())->execute(\input(), \output());
    }

    /**
     * Show prints a list of all migrations, along with their current status.
     *
     * @Usage
     * <info>migrate:status</info>
     *   <info>migrate:status -f json</info>
     *
     * @Options
     * <info>-f json</info> return data formatter.
     *
     * @Example
     * php bin/swoft migrate:status
     */
    public function status()
    {
        (new Status())->execute(\input(), \output());
    }

    /**
     * break point
     *
     * @Usage
     * <info>migrate:breakpoint</info>
     *   <info>migrate:breakpoint -t 20181030044142</info>
     *   <info>migrate:breakpoint -r</info>
     *
     * @Options
     * The <info>breakpoint</info> command allows you to set or clear a breakpoint against a specific target to inhibit rollbacks beyond a certain target.
     *   If no target is supplied then the most recent migration will be used.
     *   You cannot specify un-migrated targets
     *
     * @Example
     * php bin/swoft migrate:breakpoint -t 20181030044142
     */
    public function breakpoint()
    {
        (new Breakpoint())->execute(\input(), \output());
    }

    /**
     * just test
     */
    public function test()
    {
        (new Test())->execute(\input(), \output());
    }
}
