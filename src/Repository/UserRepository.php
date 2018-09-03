<?php

namespace Repository;

use Doctrine\DBAL\Connection;

class UserRepository
{
    private $_db;

    /**
     * UserRepository constructor.
     * @param $_db
     */
    public function __construct(Connection $_db)
    {
        $this->_db = $_db;
    }

    public function getUserByEmail($email) {
        try {
            $queryBuilder = $this->_db->createQueryBuilder();

            $queryBuilder->select('u.id', 'u.email', 'u.password', 'u.active', 'u.roles')
                ->from('user_tab', 'u')
                ->where('u.email = :email')
                ->setParameter(':email', $email, \PDO::PARAM_STR);
            return $queryBuilder->execute()->fetch();
        } catch (DBALException $exception) {
            return [];
        }
    }

    public function getUserRoles() {

    }

    public function save($user, $app)
    {
        $this->_db->beginTransaction();

        try {
            if (isset($user['id'])) {
                $this->_db->update('user_tab', $user, ['id' => $user['id']]);
            } else {
                $user['password'] = $app['security.encoder.bcrypt']->encodePassword($user['password'], '');
                $user['active'] = 1;
                $user['roles'] = "ROLE_USER";
                $this->_db->insert('user_tab', $user);
            }
            $this->_db->commit();
        } catch (DBALException $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function findAll()
    {
        $queryBuilder = $this->_db->createQueryBuilder();

        $queryBuilder->select('u.id', 'u.email', 'u.active', 'u.roles')
            ->from('user_tab', 'u');

        return $queryBuilder->execute()->fetchAll();
    }

    public function getById($id)
    {
        $queryBuilder = $this->_db->createQueryBuilder();

        $queryBuilder->select('u.id', 'u.email', 'u.active', 'u.roles')
            ->from('user_tab', 'u')
            ->where('u.id = :id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);

        return $queryBuilder->execute()->fetch();
    }

    public function delete($obj, $app)
    {
        $this->_db->beginTransaction();

        try {
            if (isset($obj['id'])) {
                $this->_db->delete('reservation_tab', ['user_id' => $obj['id']]);
                $this->_db->delete('car_tab', ['user_id' => $obj['id']]);
                $this->_db->delete('user_tab', ['id' => $obj['id']]);
            }
            $this->_db->commit();
        } catch (DBALException $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

}

?>
