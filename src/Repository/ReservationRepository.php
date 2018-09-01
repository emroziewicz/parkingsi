<?php

namespace Repository;

use Doctrine\DBAL\Connection;

class ReservationRepository
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

        $queryBuilder->select('r.id', 'r.car_id', 'r.place_id', 'r.date')
            ->from('reservation_tab', 'r');

        return $queryBuilder->execute()->fetchAll();
    }

    public function findAllByDate($date)
    {
        $queryBuilder = $this->_db->createQueryBuilder();

        $queryBuilder->select('r.id', 'r.car_id', 'r.place_id', 'r.date', 'r.user_id')
            ->from('reservation_tab', 'r')
            ->where('r.date = :date')
            ->setParameter(':date', $date);

        return $queryBuilder->execute()->fetchAll();
    }

    public function findAllByDateAndUser($date, $user_id)
    {
        $queryBuilder = $this->_db->createQueryBuilder();

        $queryBuilder->select('r.id', 'r.car_id', 'r.place_id', 'r.date', 'r.user_id')
            ->from('reservation_tab', 'r')
            ->where('r.date >= :date')
            ->where('r.user_id = :user_id')
            ->setParameter(':date', $date)
        ->setParameter(":user_id", $user_id);

        return $queryBuilder->execute()->fetchAll();
    }

    public function getById($id)
    {
        $queryBuilder = $this->_db->createQueryBuilder();

        $queryBuilder->select('r.id', 'r.car_id', 'r.place_id', 'r.date')
            ->from('reservation_tab', 'r')
            ->where('r.id = :id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);

        return $queryBuilder->execute()->fetch();
    }

    public function save($obj, $app)
    {
        $this->_db->beginTransaction();

        try {
            if (isset($obj['id'])) {
                $this->_db->update('reservation_tab', $obj, ['id' => $obj['id']]);
            } else {
                $this->_db->insert('reservation_tab', $obj);
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
                $this->_db->delete('reservation_tab', ['id' => $obj['id']]);
            }
            $this->_db->commit();
        } catch (DBALException $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }


}

?>