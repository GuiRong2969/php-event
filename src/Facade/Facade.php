<?php
// +--------------------------------------------------------------------------------------------------------------------------------------------------------------
// | [Facade Mode Base Class]
// +--------------------------------------------------------------------------------------------------------------------------------------------------------------
// | desc: A facade is a class that provides access to objects in a container. The principle of this mechanism is implemented by the Facade class, which only 
// | needs to implement one method: getFacadeAccessor. It is the getFacadeAccessor method that defines what to parse from the container. Then the Facade base 
// | class uses magic methods__ CallStatic() proxies the invocation of static methods on the facade and hands it over to the service class parsed from the 
// | container defined by the getFacadeAccessor method for execution
// +--------------------------------------------------------------------------------------------------------------------------------------------------------------
// | auth: guirong <15168272969@163.com>
// +--------------------------------------------------------------------------------------------------------------------------------------------------------------

namespace Guirong\Event\Facade;

use Guirong\Event\Container\Container;
use RuntimeException;

class Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return '';
    }

    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();
        if (!$instance) {
            throw new RuntimeException('A facade root has not been set.');
        }
        return $instance->$method(...$args);
    }

    /**
     * Get the facade really instance
     *
     * @return object
     */
    public static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    /**
     * Resolve Real Instances from Containers
     *
     * @param string $name
     * @return object
     */
    protected static function resolveFacadeInstance($name)
    {
        return Container::make($name);
    }
}
