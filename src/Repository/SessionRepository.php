<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function save(Session $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Session $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getSession(string $sessionKey): ?Session
    {
        return $this
            ->createQueryBuilder('s')
            ->where('s.name = :sessionKey')
            ->setParameter(':sessionKey', $sessionKey)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws Exception
     */
    public function getSessions(): array
    {
        $this->getEntityManager()->getConnection()->executeQuery(
            'SET sql_mode=(SELECT REPLACE(@@sql_mode,"ONLY_FULL_GROUP_BY",""))',
        );

        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT s.id AS id, s.name AS `session`, s.session_started AS started, 
            (SELECT created FROM chat WHERE s.id=session_id ORDER BY created DESC LIMIT 1) AS last_message,
            (SELECT COUNT(*) FROM chat WHERE s.id=session_id) AS message_count
            FROM session s 
            ORDER BY last_message DESC
        ';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }
}
