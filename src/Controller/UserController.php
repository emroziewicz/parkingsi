<?php
namespace Controller;

use Repository\ParkingRepository;
use Repository\UserRepository;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\User;
use Validator\PlaceFormValidator;
use Validator\RegisterFormValidator;

/**
 * Class IndexController
 * @package Controller
 */
class UserController implements ControllerProviderInterface
{
    /**
     * Connects urls with actions
     * @param Application $app
     * @return Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $registerController = $app['controllers_factory'];

        $registerController->get("/", array($this, 'user'))->bind('user');
        $registerController->get("/delete/{id}", array($this, 'user_delete'))->bind('user_delete');
        $registerController->get("/active/{id}", array($this, 'user_active'))->bind('user_active');
        $registerController->get("/admin/{id}", array($this, 'user_admin'))->bind('user_admin');

        $registerController->get("/edit", array($this, 'edit'))->bind('edit');
        $registerController->post("/edit", array($this, 'edit_save'))->bind('edit_save');

        return $registerController;
    }

    /**
     * @param Application $app
     * @return mixed
     */
    public function user(Application $app)
    {
        $userRepository = new UserRepository($app['db']);
        $users = $userRepository->findAll();

        return $app['twig']->render('user/user.twig', array(
            'users' => $users
        ));
    }

     public function user_delete(Application $app, Request $request)
    {
        $id = $request->attributes->get('id');

        $userRepository = new UserRepository($app['db']);
        $user = $userRepository->getById($id);

        $userRepository->delete($user, $app);

        $app['session']->getFlashBag()->add('success', 'message.data_delete');

        return $app->redirect($app['url_generator']->generate('user'), 301);
    }

    public function user_active(Application $app, Request $request)
    {
        $id = $request->attributes->get('id');

        $userRepository = new UserRepository($app['db']);
        $user = $userRepository->getById($id);

        if ($user['active'] == 1) {
            $user['active'] = 0;
        } else {
            $user['active'] = 1;
        }

        $userRepository->save($user, $app);

        $app['session']->getFlashBag()->add('success', 'message.data_saved');

        return $app->redirect($app['url_generator']->generate('user'), 301);
    }

    public function user_admin(Application $app, Request $request)
    {
        $id = $request->attributes->get('id');

        $userRepository = new UserRepository($app['db']);
        $user = $userRepository->getById($id);

        if (strpos($user['roles'], 'ROLE_ADMIN')) {
            $roles = $this->modifyUserAdminRoleString($user['roles'], false);
        } else {
            $roles = $this->modifyUserAdminRoleString($user['roles'], true);
        }

        $user['roles'] = $roles;

        $userRepository->save($user, $app);

        $app['session']->getFlashBag()->add('success', 'message.data_saved');

        return $app->redirect($app['url_generator']->generate('user'), 301);
    }

    private function modifyUserAdminRoleString($roles, $add) {
        if ($add) {
            if (!strpos($roles, 'ROLE_ADMIN')) {
                $roles .= ',ROLE_ADMIN';
            } else {
                return $roles;
            }
        } else {
            $roles = str_replace(",ROLE_ADMIN", "", $roles);
            $roles = str_replace("ROLE_ADMIN", "", $roles);
        }

        return $roles;
    }

}

