<?php

//use Respect\Validation\Validator as v;

#require_once (__DIR__ . '/controllers/StaticPagesController.php');
#require_once (__DIR__ . '/controllers/ApiController.php');

// Get the container

$container = $app->getContainer();

// monolog
$container['logger'] = function ($c) {
	$settings = $c->get('settings')['logger'];
	if (!file_exists($settings['path'])){
		$F = fopen($settings['path'], 'w') or die("Cannot create log file at ".$settings['path'].". Please, check the write permissions");
		fclose($F);
	}
	$logger = new Monolog\Logger($settings['name']);
	$logger->pushProcessor(new Monolog\Processor\UidProcessor());
	$logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
	return $logger;
};

// DB dependency
$container['db'] = function ($c) {

	$db = $c->get('settings')['db'];

	/*$mng =  new MongoDB\Client("mongodb://".$db['username'].":".$db['password']."@".$db['host']."/".$db['database'].'?authSource='.$db['authSource'],
            array(),
        	array('typeMap' => array (	'root'     => 'array',
                                        'document' => 'array',
                                        'array'    => 'array')
           )
 	);*/
	
	$mng = new \MongoDB\Driver\Manager("mongodb://".$db['username'].":".$db['password']."@".$db['host']."/".$db['database'].'?authSource='.$db['authSource']);

	return new \App\Models\DB($mng, $db['database']);
};

// DB dependency: permissions_db
$container['permissions_db'] = function ($c) {

	$permissions_db = $c->get('settings')['permissions_db'];
	
	$mng = new \MongoDB\Driver\Manager("mongodb://".$permissions_db['username'].":".$permissions_db['password']."@".$permissions_db['host']."/".$permissions_db['database'].'?authSource='.$permissions_db['authSource']);

	return new \App\Models\DB($mng, $permissions_db['database']);
};


// CONTROLLERS
$container['staticPages'] = function($c) {
	return new \App\Controllers\StaticPagesController($c);
};

$container['apiController'] = function($c) {
	return new \App\Controllers\ApiController($c);
};


//GLOBALS
$container['global'] = function($c) {
	return $c->get('globals');
};


//HANDLERS
$container['errorHandler'] = function ($c) {
    return new \App\Handlers\Error($c['logger']);
};


//MODELS 
$container['utils'] = function($c) {

	return new \App\Models\Utilities($c);

};

$container['oauth2'] = function($c) {

	return new \App\Models\Oauth2($c);

};

$container['vrefile'] = function($c) {

	return new \App\Models\VREfile($c);

};

$container['permissions'] = function($c) {

	return new \App\Models\Permissions($c);

};

$container['user'] = function($c) {

	return new \App\Models\User($c);

};
