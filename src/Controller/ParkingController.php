<?php
namespace Controller;

use Repository\ParkingRepository;
use Repository\UserRepository;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Validator\PlaceFormValidator;
use Validator\RegisterFormValidator;

/**
 * Class IndexController
 * @package Controller
 */
class ParkingController implements ControllerProviderInterface
{
    /**
     * Connects urls with actions
     * @param Application $app
     * @return Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $registerController = $app['controllers_factory'];

        $registerController->get("/", array($this, 'parking'))->bind('parking');
        $registerController->post("/save", array($this, 'parking_save'))->bind('parking_save');
        $registerController->get("/delete/{id}", array($this, 'parking_delete'))->bind('parking_delete');

        return $registerController;
    }

    /**
     * @param Application $app
     * @return mixed
     */
    public function parking(Application $app)
    {
        $parkingRepository = new ParkingRepository($app['db']);
        $places = $parkingRepository->findAll();

        return $app['twig']->render('parking/parking.twig', array(
            'places' => $places
        ));
    }

    public function parking_save(Application $app, Request $request)
    {
        $validator = new PlaceFormValidator($request, $app);
        $errors = $validator->validate();

        if (count($errors) > 0) {

            $app['session']->getFlashBag()->add('error', 'message.form_error');

            return $app->redirect($app['url_generator']->generate('parking'), 301);
        }

        $parkingRepository = new ParkingRepository($app['db']);
        $parkingRepository->savePlace($request->request->all(), $app);

        $app['session']->getFlashBag()->add('success', 'message.data_saved');

        return $app->redirect($app['url_generator']->generate('parking'), 301);
    }

    public function parking_delete(Application $app, Request $request)
    {
        $id = $request->attributes->get('id');

        $parkingRepository = new ParkingRepository($app['db']);
        $place = $parkingRepository->getPlaceById($id);

        $parkingRepository->deletePlace($place, $app);

        $app['session']->getFlashBag()->add('success', 'message.data_delete');

        return $app->redirect($app['url_generator']->generate('parking'), 301);
    }

}

