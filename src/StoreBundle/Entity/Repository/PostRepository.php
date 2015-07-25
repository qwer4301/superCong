<?php

namespace StoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 *
 *
 * repository methods below.
 */
class PostRepository extends EntityRepository
{
    /**
     * 获取最新帖子
     * @return array
     */
    public function findPostsLate()
    {
        return $this->_em->createQueryBuilder()
            ->select("p")
            ->from('AppBundle:Post', 'p')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(5)
            ->getQuery()->getResult();
    }

    /**
     * 根据ID获取文章
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findPostById( $id )
    {
        return $this->_em->createQueryBuilder()
            ->select('p')
            ->from('StoreBundle:Post', 'p')
            ->where("p.id = :id")
            ->setParameter( 'id', $id )
            ->getQuery()->getSingleResult();
    }


    /**
     * 根据oldId查询文章
     * @param $id
     * @param $action
     * @return mixed
     */
    public function findPostByOldId( $id, $action = 1 )
    {
        return $this->_em->createQueryBuilder()
            ->select('p')
            ->from('StoreBundle:Post', 'p')
            ->where("p.oldId = :oldId")
            ->andWhere( "p.action = :action" )
            ->setParameter( 'action', $action )
            ->setParameter( 'oldId', $id )
            ->getQuery()->getSingleResult();
    }

    /**
     * 获取最火的几片文章
     *
     * @param int $action
     * @param int $id
     * @return array
     */
    public function findPostsTop( $action = null, $id = null )
    {
        $posts =  $this->_em->createQueryBuilder()
            ->select("p")
            ->from("StoreBundle:Post", "p")
            ->where('p.id IS NOT NULL');
        if( $action )
        {
            $posts->andWhere( "p.action = {$action}" );
        }
        if( $id )
        {
            $posts->andWhere( "p.id != {$id}" );
        }
        return $posts->setMaxResults( 3 )
            ->orderBy("p.readNum", 'DESC')
            ->getQuery()->getResult();
    }

    /**
     * 获取文章列表
     * @param int $action
     * @param int $page
     * @param int $categoryId
     * @return array
     */
    public function findPostsPage( $action, $page = 1, $categoryId = null )
    {
        $post = $this->_em->createQueryBuilder()
            ->select("p")
            ->from("StoreBundle:Post", "p")
            ->where('p.action = :action');
        if( !is_null( $categoryId ) )
        {
            $post->andWhere('p.categoryId = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        return $post->setParameter( 'action', $action )
            ->setMaxResults( 10 )
            ->setFirstResult( ($page - 1) * 10 )
            ->orderBy("p.id", 'DESC')
            ->getQuery()->getResult();
    }

    /**
     * 统计文章数量
     * @param  int $action
     * @param int $categoryId
     * @return mixed
     */
    public function countPosts( $action, $categoryId = null )
    {
        $post = $this->_em->createQueryBuilder()
            ->select("COUNT(p.id)")
            ->from("StoreBundle:post", "p")
            ->where('p.action = :action');
        if( !is_null( $categoryId ) )
        {
            $post->andWhere('p.categoryId = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }
        return $post->setParameter('action', $action)
            ->getQuery()->getSingleScalarResult();
    }
}