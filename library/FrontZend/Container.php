<?php

require_once 'FrontZend/Module/Container.php';

class FrontZend_Container
{
    protected static $registry = array();

    public static function getRegistry()
    {
        return self::$registry;
    }

    public static function clearAll()
    {
        self::$registry = array();
    }

    /**
     * Adiciona um novo resolvedor para a matriz registro.
     *
     * @param string $name
     * @param string $moduleContainer
     * @return void
     */
    public static function set($name, $moduleContainer)
    {
        if (self::has($name)) {
            throw new FrontZend_Exception("'{$name}' is already in use");
        }

        self::$registry[$name] = $moduleContainer;
    }

    /**
     * Cria a instância
     * @param string $name
     * @return mixed
     */
    public static function get($name)
    {
        if (self::has($name)) {
            return self::getCached($name);
        }

        throw new FrontZend_Exception('Nothing registered with the name: ' . $name);
    }

    /**
     * Verifica se o ID existe no registro
     *
     * @param string $name
     * @return bool
     */
    public static function has($name)
    {
        return array_key_exists($name, self::$registry);
    }
    
    public static function getCached($name)
    {
        $moduleContainer = self::$registry[$name];
        $moduleName = strtolower(str_replace('_Model_Container', '', 
                      $moduleContainer));
        $modelContainer = new $moduleContainer();
        $model = $modelContainer->get($name);

//        return $model; // TODO Temp...

        $frontendOptions = array(
            'cached_entity'  => $model
        );
        $backendOptions = array(
            'cache_dir' => APPLICATION_PATH . "/data/cache/file/{$moduleName}",
            'file_name_prefix' => $name
        );

        if (!is_dir($backendOptions['cache_dir'])) {
            mkdir($backendOptions['cache_dir'], 0777, true);
        }

        $cache = Zend_Cache::factory(
            new FrontZend_Module_Model_DbTable_Cache($frontendOptions),
            new Zend_Cache_Backend_File($backendOptions)
        );

        return $cache;
    }
    
}