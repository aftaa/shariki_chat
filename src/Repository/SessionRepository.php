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
//        $this->getEntityManager()->getConnection()->executeQuery(
//            'SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,"ONLY_FULL_GROUP_BY",""))',
//        );

        return $this
            ->createQueryBuilder('s')
//            ->select('s.id AS id, s.name AS session, c.created AS last_message')
//            ->join(Chat::class, 'c', Join::ON)
//            ->orderBy('c.created', 'DESC')
//            ->groupBy('s.id')
            ->getQuery()
            ->execute();
    }
}
