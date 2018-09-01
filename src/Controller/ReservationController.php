<?php
namespace Controller;

use Repository\CarRepository;
use Repository\ParkingRepository;
use Repository\ReservationRepository;
use Repository\UserRepository;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Validator\ReservationFormValidator;

/**
 * Class IndexController
 * @package Controller
 */
class ReservationController implements ControllerProviderInterface
{
    /**
     * Connects urls with actions
     * @param Application $app
     * @return Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $indexController = $app['controllers_factory'];

        $indexController->get("/", array($this, 'reservation'))->bind('reservation');
        $indexController->post("/", array($this, 'reservation_save'))->bind('reservation_save');

        $indexController->get("/delete/{id}", array($this, 'reservation_delete'))->bind('reservation_delete');

        return $indexController;
    }

    public function reservation(Application $app, Request $request)
    {
        $userRepository = new UserRepository($app['db']);
        $userSession = $app['security.token_storage']->getToken()->getUser();

        $user = $userRepository->getUserByEmail($userSession->getUsername());

        $reservation = $request->request->all();
        $reservation['user_id'] = $user['id'];

        $date = date("Y-m-d");

        $reservationRepository = new ReservationRepository($app['db']);
        $reservations = $reservationRepository->findAllByDateAndUser($date, $user['id']);

        $parkingRepository = new ParkingRepository($app['db']);
        $places = $parkingRepository->findAll();

        $carRepository = new CarRepository($app['db']);
        $cars = $carRepository->findAllByUserId($user['id']);

        return $app['twig']->render('reservation/reservation.twig', array(
            'reservations' => $reservations,
            'places' => $places,
            'cars' => $cars,
        ));
    }

    public function reservation_save(Application $app, Request $request)
    {
        $validator = new ReservationFormValidator($request, $app);
        $errors = $validator->validate();

        if (count($errors) > 0) {

            $app['session']->getFlashBag()->add('error', 'message.form_error');

            return $app->redirect($app['url_generator']->generate('index'), 301);
        }

        $userRepository = new UserRepository($app['db']);
        $userSession = $app['security.token_storage']->getToken()->getUser();

        $user = $userRepository->getUserByEmail($userSession->getUsername());

        $reservation = $request->request->all();
        $reservation['user_id'] = $user['id'];

        $reservationRepository = new ReservationRepository($app['db']);
        $reservationRepository->save($reservation, $app);

        $app['session']->getFlashBag()->add('success', 'message.data_saved');

        return $app->redirect($app['url_generator']->generate('index'), 301);
    }

    public function reservation_delete(Application $app, Request $request)
    {
        $id = $request->attributes->get('id');

        $reservationRepository = new ReservationRepository($app['db']);
        $reservation = $reservationRepository->getById($id);

        $reservationRepository->delete($reservation, $app);

        $app['session']->getFlashBag()->add('success', 'message.data_delete');

        return $app->redirect($app['url_generator']->generate('reservation'), 301);
    }


}

