<?php
namespace Controller;

use Repository\CarRepository;
use Repository\ParkingRepository;
use Repository\ReservationRepository;
use Repository\UserRepository;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class IndexController
 * @package Controller
 */
class IndexController implements ControllerProviderInterface
{
    /**
     * Connects urls with actions
     * @param Application $app
     * @return Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $indexController = $app['controllers_factory'];

        $indexController->get("/", array($this, 'index'))->bind('index');

        $indexController->get("/login", array($this, 'login'))->bind('login');
        $indexController->post("/login_check", array($this, 'login_check'))->bind('login_check');

        $indexController->get("/login", array($this, 'login'))->bind('login');

        return $indexController;
    }

    /**
     * @param Application $app
     * @return mixed
     */
    public function index(Application $app, Request $request)
    {
        $date = $request->attributes->get('date');

        if (!$date) {
            $date = date("Y-m-d");
        }

        $parkingRepository = new ParkingRepository($app['db']);
        $places = $parkingRepository->findAll();

        $reservationRepository = new ReservationRepository($app['db']);
        $reservations = $reservationRepository->findAllByDate($date);

        $userRepository = new UserRepository($app['db']);
        $userSession = $app['security.token_storage']->getToken()->getUser();

        $user = $userRepository->getUserByEmail($userSession->getUsername());

        $carRepository = new CarRepository($app['db']);
        $cars = $carRepository->findAllByUserId($user['id']);

        return $app['twig']->render('index/index.twig', array(
            'places' => $places,
            'reservations' => $reservations,
            'cars' => $cars,
            'date' => $date,
        ));
    }

    public function login(Application $app) {
        return $app['twig']->render('index/login.twig', array());
    }

    public function login_check(Application $app) {

    }
}

