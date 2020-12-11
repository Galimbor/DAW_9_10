<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }


    //Retrieves posts from the respective users along with some user information.
    public function get_posts()
    {
        $qb = $this->getEntityManager()->createQuery('SELECT u.name,u.id AS userid,p.content,p.created_at,p.updated_at,p.id
        FROM App\Entity\Post p JOIN App\Entity\User u
        WHERE p.user = u.id ORDER BY  p.updated_at DESC ');
        return $qb->getResult();
    }


    public function get_post_by_user($blog_id, $user_id)
    {
        $qb = $this->getEntityManager()->createQuery('SELECT p.content FROM App\Entity\Post p
        WHERE ?1 = p.user AND p.id = ?2 ');
        $qb->setParameter(1,$user_id);
        $qb->setParameter(2,$blog_id);
        return $qb->getResult();
    }

    public function update_post($blog_id, $content)
    {
        $qb = $this->getEntityManager()->createQuery('UPDATE App\Entity\Post p SET p.content = ?1, p.updated_at =?3
        WHERE p.id = ?2');
        $qb->setParameter(1,$content);
        $qb->setParameter(2, $blog_id);
        $qb->setParameter(3, date("Y-m-d H:i:s"));
        return $qb->getResult();
    }




    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
