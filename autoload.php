<?php
class Autoloader {


    /**
     * @var Autoloader $instance
     */
    private static $instance;


    /**
     * @var array
     */
    protected static $psr4 = [];


    /**
     * @var array
     */
    protected static $psr0 = [];


    /**
     * Create and store new instance 
     * @return Autoloader
     */
    public static function Instance() {

        if(null === static :: $instance) {
            
            $class = __CLASS__;
            static :: $instance = new $class();
        }

        return static :: $instance;
    }


    /**
     * Register loader with SPL autoloader stack.
     */
    public static function register() {
        spl_autoload_register(['Autoloader', 'loadClass']);
    }


    /**
     * Adds a base directory for a namespace prefix using the PSR-4 standard.
     * @param string $prefix
     * @param string $base_dir
     * @param bool $prepend
     */
    public static function addPsr4($prefix, $base_dir, $prepend = false) {

        //Normalize namespace prefix
        $prefix = trim($prefix, '\\') . '\\';

        //Normalize the base directory with a trailing separator
        $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . '/';

        //Initialize the namespace prefix array
        if(false === isset(static :: $psr4[$prefix])) {
            static :: $psr4[$prefix] = [];
        }

        //Retain the base directory for the namespace prefix
        if(true === $prepend) {
            array_unshift(static :: $psr4[$prefix], $base_dir);
        } else {
            array_push(static :: $psr4[$prefix], $base_dir);
        }
    }


    /**
     * Adds a base directory for a namespace prefix using the PSR-0 standard.
     * @param string $prefix
     * @param string $base_dir
     * @param bool $prepend
     */
    public static function add($prefix, $base_dir, $prepend = false) {

        //Normalize namespace prefix
        $prefix = trim($prefix, '\\') . '\\';

        //Normalize the base directory with a trailing separator
        $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . '/';

        //Initialize the namespace prefix array
        if(false === isset(static :: $psr0[$prefix])) {
            static :: $psr0[$prefix] = [];
        }

        //Retain the base directory for the namespace prefix
        if(true === $prepend) {
            array_unshift(static :: $psr0[$prefix], $base_dir);
        } else {
            array_push(static :: $psr0[$prefix], $base_dir);
        }
    }


    /**
     * Loads the class file for a given class name.
     * @param string $class
     * @return mixed
     */
    public static function loadClass($class) {
    	
        //The current namespace prefix
        $prefix = $class;

        //Work backwards through the namespace names of the fully-qualified class name to find a mapped file name
        while(false !== $pos = strrpos($prefix, '\\')) {

            //Retain the trailing namespace separator in the prefix
            $prefix = substr($class, 0, $pos + 1);

            //The rest is the relative class name
            $relative_class = substr($class, $pos + 1);

            //Try to load a mapped file for the prefix and relative class
            $mapped_file = static :: loadMappedFile($prefix, $relative_class);

            if(false !== $mapped_file) {
                return $mapped_file;
            }

            //Remove the trailing namespace separator for the next iteration of strrpos()
            $prefix = rtrim($prefix, '\\');
        }

        //Never found a mapped file
        return false;
    }


    /**
     * Load the mapped file for a namespace prefix and relative class.
     * @param string $prefix 
     * @param string $relative_class
     * @return mixed
     */
    protected static function loadMappedFile($prefix, $relative_class) {

        if(true === isset(static :: $psr4[$prefix])) {

            //Look through base directories for this namespace prefix
            foreach(static :: $psr4[$prefix] as $base_dir) {

                //Replace the namespace prefix with the base directory, replace namespace separators with directory separators in the relative class name, append with .php
                $file = $base_dir . str_replace('\\', DIRECTORY_SEPARATOR, $relative_class) . '.php';

                //If the mapped file exists, require it
                if(true === static :: requireFile($file)) {
                    return $file;
                }
            }
        }

        if(true === isset(static :: $psr0[$prefix])) {

            //Look through base directories for this namespace prefix
            foreach(static :: $psr0[$prefix] as $base_dir) {

                //Replace the namespace prefix with the base directory, replace namespace separators with directory separators in the relative class name, append with .php
                $file = $base_dir . str_replace('\\', DIRECTORY_SEPARATOR, $prefix) . str_replace('\\', DIRECTORY_SEPARATOR, $relative_class) . '.php';

                //If the mapped file exists, require it
                if(true === static :: requireFile($file)) {
                    return $file;
                }
            }
        }

        //Never found it
        return false;
    }


    /**
     * If a file exists, require it from the file system.
     * @param string $file
     * @return boolean
     */
    protected static function requireFile($file) {

        if(true === file_exists($file)) {
            
            require_once($file);
            return true;
        }

        return false;
    }
}

return Autoloader :: Instance();
?>