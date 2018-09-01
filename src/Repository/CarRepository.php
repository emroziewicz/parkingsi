<?php

namespace Repository;

use Doctrine\DBAL\Connection;

class CarRepository
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

    public function findAll()
    {
        $queryBuilder = $this->_db->createQueryBuilder();

        $queryBuilder->select('c.id', 'c.name', 'c.vrm', 'c.user_id')
            ->from('car_tab', 'c');

        return $queryBuilder->execute()->fetchAll();
    }

    public function findAllByUserId($user_id)
    {
        $queryBuilder = $this->_db->createQueryBuilder();

        $queryBuilder->select('c.id', 'c.name', 'c.vrm', 'c.user_id')
            ->from('car_tab', 'c')
            ->where('c.user_id = :user_id')
            ->setParameter(':user_id', $user_id, \PDO::PARAM_INT);

        return $queryBuilder->execute()->fetchAll();
    }

    public function getById($id)
    {
        $queryBuilder = $this->_db->createQueryBuilder();

        $queryBuilder->select('c.id', 'c.name', 'c.vrm', 'c.user_id')
            ->from('car_tab', 'c')
            ->where('c.id = :id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);

        return $queryBuilder->execute()->fetch();
    }

    public function save($obj, $app)
    {
        $this->_db->beginTransaction();

        try {
            if (isset($obj['id'])) {
                $this->_db->update('car_tab', $obj, ['id' => $obj['id']]);
            } else {
                $this->_db->insert('car_tab', $obj);
            }
            $this->_db->commit();
        } catch (DBALException $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function delete($obj, $app)
    {
        $this->_db->beginTransaction();

        try {
            if (isset($obj['id'])) {
                $this->_db->delete('car_tab', ['id' => $obj['id']]);
            }
            $this->_db->commit();
        } catch (DBALException $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }


}

?>