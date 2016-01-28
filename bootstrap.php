<?php

/**
 * Autoloader needed for phpunit only.
 */
	
 /**
 * An example of a project-specific implementation.
 * 
 * After registering this autoload function with SPL, the following line
 * would cause the function to attempt to load the \Foo\Bar\Baz\Qux class
 * from /path/to/project/src/Baz/Qux.php:
 * 
 *      new \Foo\Bar\Baz\Qux;
 *      
 * @param string $class The fully-qualified class name.
 * @return void
 */

function autoload_init( $class_prefix, $base_dir ) {

	spl_autoload_register(function ($class) use ( $base_dir, $class_prefix ) {

	    // does the class use the namespace prefix?
	    $len = strlen($class_prefix);

	    if (strncmp($class_prefix, $class, $len) !== 0) {
	        // no, move to the next registered autoloader
	        return;
	    }

	    // get the relative class name
	    $relative_class = substr($class, $len);

	    // replace the namespace prefix with the base directory, replace namespace
	    // separators with directory separators in the relative class name, append
	    // with .php
	    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

	    // if the file exists, require it
	    if (file_exists($file)) {
	        require $file;
	    }
	});

}

autoload_init( 'browserfs\\website', __DIR__ . '/src/' );

// during Travis CI build for example, if any composer dependencies are required, 
// a "vendor" folder will be created during testing inside of your root project.
// we require the composer autoloader here.
if ( is_dir( __DIR__ . '/vendor') && file_exists( __DIR__ . '/vendor/autoload.php') ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

// assuming that you are a vendor, and that you use other sub-packages created
// by you, they are located within a level up folder of your project
foreach ( $dependencies = [
		'string',
	] as $submodule ) {

	if ( is_dir( __DIR__ . '/../' . $submodule ) && file_exists( __DIR__ . '/../' . $submodule . '/bootstrap.php' ) ) {
		
		require_once __DIR__ . '/../' . $submodule . '/bootstrap.php';

	}

}

unset( $dependencies );