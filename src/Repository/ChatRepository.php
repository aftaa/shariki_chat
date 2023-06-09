<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chat>
 *
 * @method Chat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chat[]    findAll()
 * @method Chat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chat::class);
    }

    public function save(Chat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Chat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Session $session
     * @return Chat[]
     */
    public function getChats(Session $session): array
    {
        $qb = $this->createQueryBuilder('c');
        $qb->where('c.session = :session')
            ->orderBy('c.created', 'ASC')
            ->setParameter(':session', $session);
        $query = $qb->getQuery();
        return $query->execute();
    }

    /**
     * @param Session $session
     * @return bool
     * @throws Exception
     */
    public function isNewChat(Session $session): bool
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
            SELECT COUNT(*) AS chat_count FROM chat
            WHERE name = 'Вы' AND session_id='{$session->getId()}'
        ";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $resultSet = $resultSet->fetchAssociative();
        return 1 == $resultSet['chat_count'];
    }
}
