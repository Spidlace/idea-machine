<?php

// src/AM/IdeaMachineBundle/Entity/IdeaRepository.php

namespace AM\IdeaMachineBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * IdeaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class IdeaRepository extends \Doctrine\ORM\EntityRepository
{
	public function getIdeaWithVotes(){
		$qb = $this
		->createQueryBuilder('a')
		->leftJoin('a.votes', 'app')
		->addSelect('app');

		return $qb
		->getQuery()
		->getResult();
	}

	public function getIdeas(){
		$query = $this->createQueryBuilder('a')
		->leftJoin('a.image', 'i')
		->addSelect('i')
		->leftJoin('a.votes', 'v')
		->addSelect('v')
		->orderBy('a.id', 'DESC')
		->getQuery();

		return $query->getResult();
	}
}