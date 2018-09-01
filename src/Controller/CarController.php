<?php

namespace Controller;

use Repository\CarRepository;
use Repository\ParkingRepository;
use Repository\UserRepository;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Validator\CarFormValidator;

/**
 * Class IndexController
 * @package Controller
 */
class CarController implements ControllerProviderInterface
{
    /**
     * Connects urls with actions
     * @param Application $app
     * @return Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $registerController = $app['controllers_factory'];

        $registerController->get("/", array($this, 'car'))->bind('car');
        $registerController->post("/save", array($this, 'car_save'))->bind('car_save');
        $registerController->get("/delete/{id}", array($this, 'car_delete'))->bind('car_delete');

        return $registerController;
    }

    /**
     * @param Application $app
     * @return mixed
     */
    public function car(Application $app)
    {
        $carRepository = new CarRepository($app['db']);
        $cars = $carRepository->findAll();

        return $app['twig']->render(
            'car/car.twig',
            array(
                'cars' => $cars,
            )
        );
    }

    public function car_save(Application $app, Request $request)
    {
        $validator = new CarFormValidator($request, $app);
        $errors = $validator->validate();

        if (count($errors) > 0) {

            $app['session']->getFlashBag()->add('error', 'message.form_error');

            return $app->redirect($app['url_generator']->generate('car'), 301);
        }

        $userRepository = new UserRepository($app['db']);
        $userSession = $app['security.token_storage']->getToken()->getUser();

        $user = $userRepository->getUserByEmail($userSession->getUsername());

        $car = $request->request->all();
        $car['user_id'] = $user['id'];

        $carRepository = new CarRepository($app['db']);
        $carRepository->save($car, $app);

        $app['session']->getFlashBag()->add('success', 'message.data_saved');

        return $app->redirect($app['url_generator']->generate('car'), 301);
    }

    public function car_delete(Application $app, Request $request)
    {
        $id = $request->attributes->get('id');

        $carRepository = new CarRepository($app['db']);
        $car = $carRepository->getById($id);

        $carRepository->delete($car, $app);

        $app['session']->getFlashBag()->add('success', 'message.data_delete');

        return $app->redirect($app['url_generator']->generate('car'), 301);
    }

}

