<?php
namespace Rsv\DeployBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\Collection;

use Rsv\DeployBundle\Entity\Project;

class ProjectRepository extends EntityRepository
{
/*
	public function findAllComingFights() {

		return $this->findByType(Label::EVT_FIGHT);
	}

	private function findByType($value, $all = true) {
		
		$sql = 'SELECT e FROM '.Event::CLASS_NAME.' e ';
 		$sql .= 'INNER JOIN e.type t  ';
		$sql .= 'WHERE t.type = :type ';
		$sql .= 'AND t.value = :value ';
		$sql .= 'ORDER BY e.when ASC';
		
		$query = $this->getEntityManager()->createQuery($sql);
		$query->setParameter('type', Label::TYPE_EVT);
		$query->setParameter('value', $value);
		
		$events = $query->getResult();
		return $events;
	}
    
*/
    
    public function getProjectName() {
        
        $sql = 'SELECT p.id, p.name FROM '.Project::CLASS_NAME.' p GROUP BY p.name ORDER BY p.name';
		$query = $this->getEntityManager()->createQuery($sql);
		
		$events = $query->getResult();
		return $events;
        
    }
    
}