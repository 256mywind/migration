<?php
/**
 * Run seed.
 * @author Yangdong Zhang <27282480@qq.com>
 * @time 2018/10/31 18:45
 */
namespace Swoft\Migration\Seed;

use Phinx\Seed\SeedInterface;
use Swoft\Migration\Seed;
use Swoft\Console\Input\Input;
use Swoft\Console\Output\Output;

class Run extends Seed
{
    /**
     * Run database seeders.
     *
     * @param Input  $input
     * @param Output $output
     * @return void
     */
    public function execute(Input $input, Output $output)
    {
        $seed = $input->getOpt('s');

        // run the seed(ers)
        $start = microtime(true);
        $this->seed($seed);
        $end = microtime(true);

        $output->writeln('');
        $output->writeln('<comment>All Done. Took ' . sprintf('%.4fs', $end - $start) . '</comment>');
    }

    public function seed($seed = null)
    {
        $seeds = $this->getSeeds();

        if (null === $seed) {
            // run all seeders
            foreach ($seeds as $seeder) {
                if (array_key_exists($seeder->getName(), $seeds)) {
                    $this->executeSeed($seeder);
                }
            }
        } else {
            // run only one seeder
            if (array_key_exists($seed, $seeds)) {
                $this->executeSeed($seeds[$seed]);
            } else {
                throw new \InvalidArgumentException(sprintf('The seed class "%s" does not exist', $seed));
            }
        }
    }

    protected function executeSeed(SeedInterface $seed)
    {
        $this->output->writeln('');
        $this->output->writeln(' ==' . ' <info>' . $seed->getName() . ':</info>' . ' <comment>seeding</comment>');

        // Execute the seeder and log the time elapsed.
        $start = microtime(true);
        $seed->setAdapter($this->getAdapter());

        // begin the transaction if the adapter supports it
        if ($this->getAdapter()->hasTransactions()) {
            $this->getAdapter()->beginTransaction();
        }

        // Run the seeder
        if (method_exists($seed, SeedInterface::RUN)) {
            $seed->run();
        }

        // commit the transaction if the adapter supports it
        if ($this->getAdapter()->hasTransactions()) {
            $this->getAdapter()->commitTransaction();
        }
        $end = microtime(true);

        $this->output->writeln(' ==' . ' <info>' . $seed->getName() . ':</info>' . ' <comment>seeded' . ' ' . sprintf('%.4fs', $end - $start) . '</comment>');
    }
}