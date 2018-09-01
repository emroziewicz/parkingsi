<?php
namespace Controller;

use Repository\UserRepository;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Validator\RegisterFormValidator;

/**
 * Class IndexController
 * @package Controller
 */
class RegisterController implements ControllerProviderInterface
{
    /**
     * Connects urls with actions
     * @param Application $app
     * @return Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $registerController = $app['controllers_factory'];

        $registerController->get("/", array($this, 'register'))->bind('register');
        $registerController->post("/save", array($this, 'register_save'))->bind('register_save');

        return $registerController;
    }

    /**
     * @param Application $app
     * @return mixed
     */
    public function register(Application $app)
    {
        return $app['twig']->render('register/register.twig', array());
    }

    public function register_save(Application $app, Request $request)
    {
        $validator = new RegisterFormValidator($request, $app);
        $errors = $validator->validate();

        if (count($errors) > 0) {

            $app['session']->getFlashBag()->add('error', 'message.form_error');

            return $app->redirect($app['url_generator']->generate('register'), 301);
        }

        $userRepository = new UserRepository($app['db']);
        $userRepository->save($request->request->all(), $app);

        $app['session']->getFlashBag()->add('success', 'message.account_created');

        return $app->redirect($app['url_generator']->generate('login'), 301);


    }

}

