<?php
/**
 * Migrate the database.
 * @author Yangdong Zhang <27282480@qq.com>
 * @time 2018/10/30 12:57
 */

namespace Swoft\Migration\Migrates;

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\MigrationInterface;
use Phinx\Util\Util;
use Swoft\Console\Input\Input;
use Swoft\Console\Output\Output;
use Swoft\Migration\Migrate;

class Up extends Migrate
{

    /**
     * Migrate the database.
     *
     * @param Input  $input
     * @param Output $output
     * @return integer integer 0 on success, or an error code.
     */
    public function execute(Input $input, Output $output)
    {
        $version = $input->getOpt('t');
        $date    = $input->getOpt('d');

        // run the migrations
        $start = microtime(true);
        if (null !== $date) {
            $this->migrateToDateTime(new \DateTime($date));
        } else {
            $this->migrate($version);
        }
        $end = microtime(true);

        $output->writeln('');
        $output->writeln('<comment>All Done. Took ' . sprintf('%.4fs', $end - $start) . '</comment>');
    }

    public function migrateToDateTime(\DateTime $dateTime)
    {
        $versions   = array_keys($this->getMigrations());
        $dateString = $dateTime->format('YmdHis');

        $outstandingMigrations = array_filter($versions, function ($version) use ($dateString) {
            return $version <= $dateString;
        });

        if (count($outstandingMigrations) > 0) {
            $migration = max($outstandingMigrations);
            $this->output->writeln('Migrating to version ' . $migration);
            $this->migrate($migration);
        }
    }

    protected function migrate($version = null)
    {
        $migrations = $this->getMigrations();
        $versions   = $this->getVersions();
        $current    = $this->getCurrentVersion();

        if (empty($versions) && empty($migrations)) {
            return;
        }

        if (null === $version) {
            $version = max(array_merge($versions, array_keys($migrations)));
        } else {
            if (0 != $version && !isset($migrations[$version])) {
                $this->output->writeln(sprintf('<comment>warning</comment> %s is not a valid version', $version));
                return;
            }
        }

        // are we migrating up or down?
        $direction = $version > $current ? MigrationInterface::UP : MigrationInterface::DOWN;
        /**
         * @var $migration AbstractMigration
         */
        if ($direction === MigrationInterface::DOWN) {
            // run downs first
            krsort($migrations);
            foreach ($migrations as $migration) {
                if ($migration->getVersion() <= $version) {
                    break;
                }

                if (in_array($migration->getVersion(), $versions)) {
                    $this->executeMigration($migration, MigrationInterface::DOWN);
                }
            }
        }

        ksort($migrations);
        foreach ($migrations as $migration) {
            if ($migration->getVersion() > $version) {
                break;
            }

            if (!in_array($migration->getVersion(), $versions)) {
                $this->executeMigration($migration, MigrationInterface::UP);
            }
        }
    }

    protected function getCurrentVersion()
    {
        $versions = $this->getVersions();
        $version  = 0;

        if (!empty($versions)) {
            $version = end($versions);
        }

        return $version;
    }
}
