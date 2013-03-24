<?

class Classloader {
    static protected $debug = false; // temporary for testing. please remove me.

    static protected $classloader_map = array(
        "APIAnswer" =>                  "/Symfony/src/EP/EP/ApiV1_0Bundle/Model/APIAnswer.php",
        "APIAuthenticator" =>           "/Symfony/src/EP/EP/ApiV1_0Bundle/Services/APIAuthenticator.php",
        "APIException" =>               "/Symfony/src/EP/EP/ApiV1_0Bundle/Error/APIException.php",
        "APIExceptionCodes" =>          "/Symfony/src/EP/EP/ApiV1_0Bundle/Error/APIExceptionCodes.php",
        "_" =>                          "/classes/common/utility/_.php",
    );

    /**
     * dynamically load a class
     * @param type $classname
     * @return type 
     */
    static function loadClass($classname) {
        $classloader_map = self::$classloader_map;
    
        if(array_key_exists($classname, $classloader_map)) {
            $resource = $classloader_map[$classname];
            // include the class being requested
            self::log("Found a file that exists for this class $classname, loading $resource");
            include_once( $_SERVER['DOCUMENT_ROOT'] . $resource);
        } else {
            self::log("Did not find a file to load for class $classname");
        }
    }
}

spl_autoload_register( 'Classloader::loadClass' );
if( function_exists('__autoload') ) spl_autoload_register( '__autoload' ); // this basically says, if the old-school autoloader existed, make sure we use it as well as our custom autoloader
?>