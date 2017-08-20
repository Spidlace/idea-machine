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
		->setMaxResults(6)
		->orderBy('a.id', 'DESC')
		->getQuery();

		return $query->getResult();
	}

	public function getIdeaUser($idUser)
	{
		$qb = $this->createQueryBuilder('a');

		// On fait une jointure avec l'entité User avec pour alias « u »
		$qb
		->innerJoin('a.user', 'u')
		->addSelect('u');
		$qb->where('u.id = :idUser');
		$qb->setMaxResults(6);
		$qb->orderBy('a.id', 'DESC');
		$qb->setParameter('idUser', $idUser);

		// Enfin, on retourne le résultat
		return $qb
		->getQuery()
		->getResult();
	}

	public function getOtherIdea($offset, $userid = null){
		$query = $this->createQueryBuilder('a');
		if($userid !== null){
			$query->innerJoin('a.user', 'u');
			$query->addSelect('u');
			$query->where('u.id = :idUser');
			$query->setParameter('idUser', $userid);
		}
		$query->setFirstResult($offset);
		$query->setMaxResults(6);
		$query->orderBy('a.id', 'DESC');

		return $query->getQuery()->getResult();
	}

	public function getCountAjax($userid = null){
		
		if($userid !== null){
			$count = $this->createQueryBuilder('a')->innerJoin('a.user', 'u')->addSelect('u')->where('u.id = :idUser')->setParameter('idUser', $userid)->select('COUNT(a)')->getQuery()->getSingleScalarResult();
		} else {
			$count = $this->createQueryBuilder('a')->select('COUNT(a)')->getQuery()->getSingleScalarResult();
		}

		$count = ceil($count/6);

		return $count;
	}


}