<?php

namespace Repository;

use Doctrine\DBAL\Connection;

class ParkingRepository
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

        $queryBuilder->select('pl.id', 'pl.name')
            ->from('place_tab', 'pl');

        return $queryBuilder->execute()->fetchAll();
    }

    public function getPlaceById($id)
    {
        $queryBuilder = $this->_db->createQueryBuilder();

        $queryBuilder->select('pl.id', 'pl.name')
            ->from('place_tab', 'pl')
            ->where('pl.id = :id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);

        return $queryBuilder->execute()->fetch();
    }

    public function savePlace($obj, $app)
    {
        $this->_db->beginTransaction();

        try {
            if (isset($obj['id'])) {
                $this->_db->update('place_tab', $obj, ['id' => $obj['id']]);
            } else {
                $this->_db->insert('place_tab', $obj);
            }
            $this->_db->commit();
        } catch (DBALException $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function deletePlace($obj, $app)
    {
        $this->_db->beginTransaction();

        try {
            if (isset($obj['id'])) {
                $this->_db->delete('place_tab', ['id' => $obj['id']]);
            }
            $this->_db->commit();
        } catch (DBALException $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }


}

?>