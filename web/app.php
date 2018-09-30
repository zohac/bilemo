<?php

use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../vendor/autoload.php';

/**
 * BlackFire extension
 */
// If the header is set
if (isset($_SERVER['HTTP_BLACKFIRETRIGGER'])) {
    $config = new \Blackfire\ClientConfiguration();
    $config->setClientId('852814fc-548f-4216-bc37-03d5aab5662f');
    $config->setClientToken('c54644f37928f624f157280c2e1ec5353a11044c81022b4bfdcee1dc9284cc00');

    // let's create a client
    $blackfire = new \Blackfire\Client($config);
    // then start the probe
    $probe = $blackfire->createProbe();

    // When runtime shuts down, let's finish the profiling session
    register_shutdown_function(function () use ($blackfire, $probe) {
        // See the PHP SDK documentation for using the $profile object
        $profile = $blackfire->endProbe($probe);
    });
}

if (PHP_VERSION_ID < 70000) {
    include_once __DIR__.'/../var/bootstrap.php.cache';
}

$kernel = new AppKernel('prod', true);
if (PHP_VERSION_ID < 70000) {
    $kernel->loadClassCache();
}
$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
error_log($kernel->getLog());
$kernel->terminate($request, $response);
