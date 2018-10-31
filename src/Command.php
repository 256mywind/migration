<?php
/**
 * Base command
 * @author Yangdong Zhang <27282480@qq.com>
 * @time 2018/10/29 19:05
 */

namespace Swoft\Migration;

use InvalidArgumentException;
use Phinx\Db\Adapter\AdapterFactory;

abstract class Command
{
    protected $config = 'database';

    public function getAdapter()
    {
        if (isset($this->adapter)) {
            return $this->adapter;
        }

        $options = $this->getDbConfig();

        $adapter = AdapterFactory::instance()->getAdapter($options['adapter'], $options);

        if ($adapter->hasOption('table_prefix') || $adapter->hasOption('table_suffix')) {
            $adapter = AdapterFactory::instance()->getWrapper('prefix', $adapter);
        }

        $this->adapter = $adapter;

        return $adapter;
    }

    /**
     * 获取数据库配置
     * @return array
     */
    protected function getDbConfig()
    {
        $config = config('db.master.uri');
        $config = array_shift($config);

        $config = explode('?', $config);

        $dbConfig = [];
        $host = explode('/', $config['0']);
        $dbConfig = [
            'host' => explode(':',$host['0'])['0'],
            'port' => explode(':',$host['0'])['1'],
            'name' => $host['1'],
        ];
        foreach (explode('&', $config['1']) as $value) {
            $tmp = explode('=', $value);
            $dbConfig = array_merge($dbConfig, [$tmp['0'] => $tmp['1']]);
        }

        $dbConfig = array_merge(config('db.master'), $dbConfig);
        $dbConfig['pass'] = $dbConfig['password'];

        $dbConfig['version_order'] = 'creation';
        $dbConfig['adapter'] = 'mysql';
        $dbConfig['table_prefix'] = $dbConfig['table_prefix'] ?? 'sw_';
        $dbConfig['default_migration_table'] = $dbConfig['table_prefix'] . 'migrations';

        return $dbConfig;
    }

    protected function verifyMigrationDirectory($path)
    {
        if (!is_dir($path)) {
            throw new InvalidArgumentException(sprintf('Migration directory "%s" does not exist', $path));
        }

        if (!is_writable($path)) {
            throw new InvalidArgumentException(sprintf('Migration directory "%s" is not writable', $path));
        }
    }
}
