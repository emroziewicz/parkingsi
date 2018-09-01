<?php

use Provider\UserProvider;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\RememberMeServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Translation\Loader\YamlFileLoader;

$app->register(new FormServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new AssetServiceProvider());

$app->register(
    new TwigServiceProvider(),
    array(
        'twig.path' => array(PATH_VIEWS),
    )
);

$app->register(
    new DoctrineServiceProvider(),
    array(
        'db.options' => array(
            'driver'    => 'pdo_mysql',
            'host'      => 'localhost',
            'dbname'    => 'parking',
            'user'      => 'root',
            'password'  => 'M1llennium',
            'charset'   => 'utf8',
            'driverOptions' => array(
                1002 => 'SET NAMES utf8',
            ),
        ),
    )
);

$app->register(new LocaleServiceProvider());
$app->register(
    new TranslationServiceProvider(),
    [
        'locale' => 'pl',
        'locale_fallbacks' => array('pl'),
    ]
);

$app->extend(
    'translator',
    function ($translator, $app) {
        $translator->addLoader('yaml', new YamlFileLoader());

        $translator->addResource('yaml', __DIR__.'/../locales/en.yml', 'en');
        $translator->addResource('yaml', __DIR__.'/../locales/pl.yml', 'pl');

        return $translator;
    }
);

$app->register(
    new SecurityServiceProvider(),
    array(
        'security.firewalls' => array(
            'login' => array(
                'pattern' => '^/login$',
                'anonymous' => true,
            ),
            'register' => array(
                'pattern' => '^/register/.*$',
                'anonymous' => true,
            ),
            'site' => array(
                'pattern' => '^/.*$',
                'form' => array(
                    'login_path' => 'login',
                    'check_path' => 'login_check',
                    'failure_path' => 'login',
                    'username_parameter' => 'email',
                    'password_parameter' => 'password',
                ),
                'anonymous' => false,
                'logout' => array(
                    'logout_path' => 'logout',
                    'invalidate_session' => true,
                ),
                'remember_me' => array(
                    'key' => 'K$@4t35faKFA$%@REfea',
                    'lifetime' => 604800,
                    'path' => '/',
                ),
                'users' => function () use ($app) {
                    return new UserProvider($app['db']);
                },
            ),
        ),
    )
);

$app->register(new RememberMeServiceProvider());

$app['security.default_encoder'] = function ($app) {
    return $app['security.encoder.bcrypt'];
};

$app['security.encoder.bcrypt'] = function ($app) {
    return new BCryptPasswordEncoder(6);
};


//
//$app['user.provider'] = function ($app) {
//    return new UserProvider($app);
//};

//
//$app['security.authentication.failure_handler.site'] = function ($app) {
//    return new Handler\AuthenticationFailureHandler(
//        $app, $app['security.http_utils'],
//        $app['security.firewalls']['site']['form'], null, $app
//    );
//};

// 404 - Page not found
//$app->error(
//    function (\Exception $e, Request $request, $code) use ($app) {
//        switch ($code) {
//            case 404:
//                return $app['twig']->render('/error/404.twig');
//                break;
//            default:
//                return $app['twig']->render('/error/500.twig');
//        }
//    }
//);

require PATH_SRC.'/routing.php';

return $app;