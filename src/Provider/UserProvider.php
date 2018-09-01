<?php

namespace Provider;

use Doctrine\DBAL\Connection;
use Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{

    private $_db;

    /**
     * UserProvider constructor.
     * @param $_db
     */
    public function __construct(Connection $_db)
    {
        $this->_db = $_db;
    }

    public function refreshUser(UserInterface $user)
    {

        if (!$user instanceof User) {
            throw new UnsupportedUserException("Unable to refresh user");
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function loadUserByUsername($username)
    {

        $userRepository = new UserRepository($this->_db);
        $user = $userRepository->getUserByEmail($username);

        if (sizeof($user) == 0) {
            throw new UsernameNotFoundException(sprintf("Unable to find user: %s", $username));
        }

        return new User($user['email'], $user['password'], explode(",", $user['roles']), $this->isActive($user['active']), true, true, true);
    }

    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }

    private function isActive($input)
    {
        if ($input == 1) {
            return true;
        }

        return false;
    }


}

?>

