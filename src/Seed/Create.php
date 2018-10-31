<?php
/**
 * Create the new seed.
 * @author Yangdong Zhang <27282480@qq.com>
 * @time 2018/10/31 18:35
 */

namespace Swoft\Migration\Seed;

use Phinx\Util\Util;
use Swoft\Console\Input\Input;
use Swoft\Console\Output\Output;
use Swoft\Migration\Seed;

class Create extends Seed
{
    /**
     * Create the new seeder.
     *
     * @param Input  $input
     * @param Output $output
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @return void
     */
    public function execute(Input $input, Output $output)
    {
        $path = $this->getPath();

        if (!file_exists($path)) {
            $name = $input->read('Create seeds directory? [y]/n: ');
            if (strtolower($name) == 'y') {
                mkdir($path, 0755, true);
            }else {
                return false;
            }
        }

        $this->verifyMigrationDirectory($path);

        $path = realpath($path);

        $className = $input->getArg('0');

        if (!Util::isValidPhinxClassName($className)) {
            throw new \InvalidArgumentException(sprintf('The seed class name "%s" is invalid. Please use CamelCase format', $className));
        }

        // Compute the file path
        $filePath = $path . DS . $className . '.php';

        if (is_file($filePath)) {
            throw new \InvalidArgumentException(sprintf('The file "%s" already exists', basename($filePath)));
        }
        
        // Load the alternative template if it is defined.
        $contents = file_get_contents($this->getTemplate());

        // inject the class names appropriate to this seeder
        $arr = preg_split('/(?=[A-Z])/', $className);
        unset($arr[0]); // remove the first element ('')
        $classes  = [
            '$className' => $className,
            '$name' => strtolower(implode($arr, '_'))
        ];
        $contents = strtr($contents, $classes);

        if (false === file_put_contents($filePath, $contents)) {
            throw new \RuntimeException(sprintf('The file "%s" could not be written to', $path));
        }

        $output->writeln('<info>created</info> .' . str_replace(getcwd(), '', $filePath));
    }

    protected function getTemplate()
    {
        return __DIR__ . '/../stubs/seed.stub';
    }
}