<?php
/**
 * Create the new migration.
 * @author Yangdong Zhang <27282480@qq.com>
 * @time 2018/10/29 17:59
 */

namespace Swoft\Migration\Migrates;

use Phinx\Util\Util;
use Swoft\Console\Input\Input;
use Swoft\Console\Output\Output;
use Swoft\Migration\Migrate;


class Create extends Migrate
{
    /**
     * Create the new migration.
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @return void
     */
    public function execute(Input $input, Output $output)
    {
        $path = $this->getPath();

        if (!file_exists($path)) {
            $name = $input->read('Create migrations directory? [y]/n: ');
            if (strtolower($name) == 'y') {
                mkdir($path, 0755, true);
            }else {
                return false;
            }
        }

        $this->verifyMigrationDirectory($path);

        $path      = realpath($path);

        $className = $input->getArg('0');

        if (!Util::isValidPhinxClassName($className)) {
            throw new \InvalidArgumentException(sprintf('The migration class name "%s" is invalid. Please use CamelCase format.', $className));
        }

        if (!Util::isUniqueMigrationClassName($className, $path)) {
            throw new \InvalidArgumentException(sprintf('The migration class name "%s" already exists', $className));
        }

        // Compute the file path
        $fileName = Util::mapClassNameToFileName($className);
        $filePath = $path . DS . $fileName;

        if (is_file($filePath)) {
            throw new \InvalidArgumentException(sprintf('The file "%s" already exists', $filePath));
        }

        // Verify that the template creation class (or the aliased class) exists and that it implements the required interface.
        $aliasedClassName = null;

        // Load the alternative template if it is defined.
        $contents = file_get_contents($this->getTemplate());

        // inject the class names appropriate to this migration
        $arr = preg_split('/(?=[A-Z])/', $className);
        unset($arr[0]); // remove the first element ('')
        $contents = strtr($contents, [
            '$className' => $className,
            '$name' => strtolower(implode($arr, '_'))
        ]);

        if (false === file_put_contents($filePath, $contents)) {
            throw new \RuntimeException(sprintf('The file "%s" could not be written to', $path));
        }

        $output->writeln('<info>created success</info> .' . str_replace(getcwd(), '', $filePath));
    }

    /**
     * Get template file
     * @return string
     */
    protected function getTemplate()
    {
        return __DIR__ . '/../stubs/migrate.stub';
    }
}