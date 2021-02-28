<?php
/**
 * LPS
 *
 * @author Shulman A.V.
 * @version 5 (2012.06.27)
 * @copyright 2008
 */
    namespace LPS;
    /** Platform init **/
    $startProgrammTime = microtime(1);
    require_once (__DIR__.'/Autoload.php'); // LPS Loader
    require_once dirname(__DIR__).'/vendor/autoload.php';   // Composer Loader
    require_once (dirname (__DIR__).'/Config.php'); // Config Loader


    // disable DOMPDF's internal autoloader if you are using Composer
    define('DOMPDF_ENABLE_AUTOLOAD', false);

    if (!class_exists('\LPS\Config')){
        exit ('Config file not found');
    }
    
    //$_SERVER['REQUEST_URI'] = urldecode($_SERVER['REQUEST_URI']);
    
    if(Config::isCLI()){
        Config::cliPrepare();
    } else {
        // Если вход выполнен с PhantomJS, включаем тестовую бд
        if (!empty($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'PhantomJS') !== FALSE){
            Config::setTest(TRUE);
        }
    }
    Autoload::init(Config::getAutoload(), Config::getRealDocumentRoot() . '/' . Config::getParametr('Dir','logs').'/autoload.log');
    /* Error Handler Init */
    if (!class_exists('\exceptionHandler\Controller')){
        exit ('Error handler file not found. Error can\'t be configurated');
    }
    if (!class_exists('\App\Builder')){
        exit('Builder file not found.');
    }
    $builder = \App\Builder::getInstance();
    Components\Benchmark::factory()->log('After load config, builder, autoloaders');
    //event dispatcher
    $dispatcher = $builder->getEventDispatcher();
    //событие на начало действий
    $dispatcher->dispatch(StoreEvents::STORE_START, new \Symfony\Component\EventDispatcher\GenericEvent(null));
    if (Config::isCLI()){
        $router = $builder->getCliRouter();
        /* RUN */
        Components\Benchmark::factory()->log('Start $router->route()');
        $response = $router->route();
        if (Config::BenchmarkAccess()){
            print_r(array_values(\LPS\Components\Benchmark::get()->getLog()));
        }else{
            /* SEND */
            //echo $response;
        }
    }else{
    
        $request = $builder->getRequest();
        new \App\Redirect($request);
        $router = $builder->getWebRouter();

        /* RUN */
        /*  */
        Components\Benchmark::factory()->log('Start $router->route()');
        $response = $router->route();
        //echo $response;
        //событие на конец действий
        $dispatcher->dispatch(StoreEvents::STORE_FINISH, new \Symfony\Component\EventDispatcher\GenericEvent(null));
        Components\Benchmark::factory()->log('FINISH');
        if (Config::BenchmarkAccess()){
            echo '<pre>';
            print_r(array_values(\LPS\Components\Benchmark::get()->getLog()));
            echo '</pre>';
        }else{
            /* SEND */
            $response
                ->prepare($request)
                ->send();
        }
    }