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
		
		$sql = 'SELECT event FROM CeladCecileBundle:Project event ';
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
    public function getProject($id = 0) {
        return $this->find($id);
    }
    
    public function getProjectName() {
        
        $sql = "SELECT p.id, p.name FROM ".Project::CLASS_NAME." p WHERE p.environment = '".Project::ENV_REPO."' GROUP BY p.name ORDER BY p.name";
		$query = $this->getEntityManager()->createQuery($sql);
		
		$events = $query->getResult();
		return $events;
        
    }


    public function getRepoFrom($projetId = 0) {

        $sql = "SELECT p
                FROM ".Project::CLASS_NAME." p
                WHERE p.domain =".Project::ENV_REPO
                    ." AND p.name =:projectId";
        $query = $this->getEntityManager()->createQuery($sql);
        $query->setParameter('projectId', $projetId);

        return $query->getResult();

    }

    public function getEnvIds($repoId = 0) {

        $sql = "SELECT subP.name FROM ".Project::CLASS_NAME." subP WHERE subP.id = :repoId";
        $query = $this->getEntityManager()->createQuery($sql);
        $query->setParameter(":repoId", $repoId);
        $where = $query->getResult();

        if (count($where) > 0) {
            $sql = "SELECT p FROM ".Project::CLASS_NAME." p WHERE p.name = :name";
            $query = $this->getEntityManager()->createQuery($sql);
            $query->setParameter('name', $where[0]["name"]);

            return $query->getResult();
        }

        return array();
    }

}