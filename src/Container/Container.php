<?php

namespace Guirong\Event\Container;

/**
 * Container class, which is used to implement automatic dependency injection
 * @auth:Rong Gui
 * @date:2023/04/03
 */

class Container
{
    /**
     * Action scope
     * 
     * @var string
     */
    private static $scope = 'event';

    /**
     * Set action scope
     *
     * @param [type] $value
     * @return void
     */
    public static function setScope($value)
    {
        self::$scope = $value;
    }

    /**
     * Register Service Collection
     * 
     * @var array
     */
    protected static $services = [];

    /**
     * Register an instance
     * 
     * @param $alias
     * @param $generator
     */
    public static function register($alias, $generator, $constructParams = [])
    {
        if ($generator instanceof \Closure) {
            self::$services[self::$scope][$alias] = $generator;
        } else {
            self::$services[self::$scope][$alias] = self::build($generator, $constructParams);
        }
        return self::$services[self::$scope][$alias];
    }

    /**
     * Destroy an instance
     * 
     * @param string $class
     * @return boolean
     */
    public static function destory($class)
    {
        if (isset(self::$services[self::$scope][$class])) {
            unset(self::$services[self::$scope][$class]);
        }
        return true;
    }

    /**
     * Build service implementation through reflection
     * 
     * @param string $className
     * @param array $constructParams
     * @return object|null
     */
    protected static function build($className, $constructParams = [])
    {
        $methodParams = $constructParams + self::getMethodParams($className);
        return (new \ReflectionClass($className))->newInstanceArgs($methodParams);
    }

    /**
     * Obtain object instances of the class
     * 
     * @param string $className
     * @param array $constructParams
     * @return object|null
     */
    public static function make($className, $constructParams = [])
    {
        if (isset(self::$services[self::$scope][$className])) {
            $instance = self::$services[self::$scope][$className];
        } else {
            $instance = self::register($className, $className, $constructParams);
        }
        return $instance;
    }

    /**
     * Method for executing classes
     * 
     * @param string $class [Class name/registered alias]
     * @param string $methodName    [Method Name]
     * @param array $params   [Additional parameters]
     * @param array $constructParams    [Parameters of the constructor]
     */
    public static function call($class, $methodName, $params = [], $constructParams = [])
    {
        // Get an instance of a class
        $instance = self::make($class, $constructParams);
        $className = get_class($instance);
        // Obtain the parameters required for dependency injection for this method
        $paramsArr = self::getMethodParams($className, $methodName);
        $params = $params ?: $paramsArr;
        // Method for executing classes
        try {
            $method = new \ReflectionMethod($className, $methodName);
            if ($method->isPublic()) {
                /**
                 * Two other methods for executing classes
                 * 1. $instance->{$methodName}(...$params);
                 * 2. return call_user_func_array([$instance, $methodName], $params);
                 */
                return $method->invokeArgs($instance, $params);
            } else {
                throw new \ReflectionException("method $className->$methodName() is not public!");
            }
        } catch (\ReflectionException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Obtaining method parameters for a class, only obtaining parameters with types
     * 
     * @param string $className  
     * @param string $methodsName 
     * @return array
     */
    public static function getMethodParams($className, $methodsName = '__construct')
    {
        // Obtain this class through reflection
        $class = new \ReflectionClass($className);
        $paramArr = []; // Collect parameters and parameter types
        // Determine if the class has a constructor
        if ($class->hasMethod($methodsName)) {
            // Get Constructor
            $construct = $class->getMethod($methodsName);
            // Determine if the constructor has parameters
            $params = $construct->getParameters();
            if (count($params) > 0) {
                // Determine parameter type
                foreach ($params as $key => $param) {
                    if ($paramClass = $param->getClass()) {
                        // Obtain parameter type name
                        $paramClassName = $paramClass->getName();
                        if (self::isResident($paramClassName)) {
                            $paramArr[] = self::getResidentInstance($paramClassName);
                        } else {
                            // Obtain parameter types
                            $args = self::getMethodParams($paramClassName);
                            $paramArr[] = (new \ReflectionClass($paramClass->getName()))->newInstanceArgs($args);
                        }
                    } else if ($param->isDefaultValueAvailable()) {
                        $paramArr[] = $param->getDefaultValue();
                    } else {
                        $paramArr[] = null;
                    }
                }
            }
        }
        return $paramArr;
    }

    /**
     * The resident instances
     *
     * @var array
     */
    protected static $resident = [];

    /**
     * Set up resident instances
     *
     * @param object $instance
     * @return boolean
     */
    public static function setResidentInstance(object $instance)
    {
        $name = get_class($instance);
        self::$resident[] = $name;
        self::$services[self::$scope][$name] = $instance;
        return true;
    }

    /**
     * Resident instance or not
     *
     * @param string $name
     * @return boolean
     */
    protected static function isResident($name)
    {
        return in_array($name, self::$resident);
    }

    /**
     * Obtain resident instances
     *
     * @param string $name
     * @return object
     */
    protected static function getResidentInstance($name)
    {
        return self::$services[self::$scope][$name];
    }
}
