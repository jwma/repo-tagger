<?php

namespace AppBundle\Repository;

/**
 * RepoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RepoRepository extends \Doctrine\ORM\EntityRepository
{
    public function all()
    {
        $qb = $this->createQueryBuilder('repo');
        $qb
            ->select('repo.id', 'repo.name', 'repo.fullName', 'repo.htmlUrl', 'repo.description',
                'repo.tags', 'repo.remark')
            ->orderBy('repo.id', 'asc');

        return $qb->getQuery()->getArrayResult();
    }
}
