<?php
/**
 * seed
 * @author Yangdong Zhang <27282480@qq.com>
 * @time 2018/10/31 18:37
 */
namespace Swoft\Migration;

use Phinx\Seed\AbstractSeed;
use Phinx\Util\Util;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class Seed extends Command
{
    /**
     * @var array
     */
    protected $seeds;

    public function __construct()
    {
        $this->input = new ArgvInput();
        $this->output = new ConsoleOutput();
    }


    protected function getPath()
    {
        $rootPath = \Swoft::getAlias('@root');
        return $rootPath . '/database' . DS . 'seeds' . ($this->config !== 'database' ? DS . $this->config : '');
    }

    public function getSeeds()
    {
        if (null === $this->seeds) {
            $phpFiles = glob($this->getPath() . DS . '*.php', defined('GLOB_BRACE') ? GLOB_BRACE : 0);

            // filter the files to only get the ones that match our naming scheme
            $fileNames = [];
            /** @var Seeder[] $seeds */
            $seeds = [];

            foreach ($phpFiles as $filePath) {
                if (Util::isValidSeedFileName(basename($filePath))) {
                    // convert the filename to a class name
                    $class             = pathinfo($filePath, PATHINFO_FILENAME);
                    $fileNames[$class] = basename($filePath);

                    // load the seed file
                    /** @noinspection PhpIncludeInspection */
                    require_once $filePath;
                    if (!class_exists($class)) {
                        throw new \InvalidArgumentException(sprintf('Could not find class "%s" in file "%s"', $class, $filePath));
                    }

                    // instantiate it
                    $seed = new $class($this->input, $this->output);

                    if (!($seed instanceof AbstractSeed)) {
                        throw new \InvalidArgumentException(sprintf('The class "%s" in file "%s" must extend \Phinx\Seed\AbstractSeed', $class, $filePath));
                    }

                    $seeds[$class] = $seed;
                }
            }

            ksort($seeds);
            $this->seeds = $seeds;
        }

        return $this->seeds;
    }
}