<?php

namespace App\Repository;

use App\Entity\Roles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Roles|null find($id, $lockMode = null, $lockVersion = null)
 * @method Roles|null findOneBy(array $criteria, array $orderBy = null)
 * @method Roles[]    findAll()
 * @method Roles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RolesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Roles::class);
    }

    public function findOrganizationsId($value)
    {
        $statement = $this->getEntityManager()->getConnection()->prepare('SELECT organization_id FROM roles WHERE user_id = :user_id');
        $statement->bindValue("user_id", $value);
        $statement->execute();

        return array_map(function ($array) {
            return $array['organization_id'];
        }, $statement->fetchAll());
    }

    public function findUsersIdBelongsOrganization($value)
    {
        $statement = $this->getEntityManager()->getConnection()->prepare('SELECT user_id FROM roles WHERE organization_id = :organization_id');
        $statement->bindValue("organization_id", $value);
        $statement->execute();

        return array_map(function ($array) {
            return $array['user_id'];
        }, $statement->fetchAll());
    }

    public function findUsersRolesBelongsOrganization($value)
    {
        $statement = $this->getEntityManager()->getConnection()->prepare('SELECT role, user_id FROM roles WHERE organization_id = :organization_id');
        $statement->bindValue("organization_id", $value);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function findRolesId($user_id, $org_id) {
        $statement = $this->getEntityManager()->getConnection()->prepare('SELECT id FROM roles WHERE organization_id = :org_id AND user_id = :user_id');
        $statement->bindValue("org_id", $org_id);
        $statement->bindValue("user_id", $user_id);
        $statement->execute();
        return $statement->fetchAll()[0]['id'];
    }

    public function findUserRole($user_id, $org_id) {
        $statement = $this->getEntityManager()->getConnection()->prepare('SELECT role FROM roles WHERE organization_id = :org_id AND user_id = :user_id');
        $statement->bindValue("org_id", $org_id);
        $statement->bindValue("user_id", $user_id);
        $statement->execute();
        return $statement->fetchAll()[0]['role'];
    }

    public function findUserRolesAndOrganizations($user_id) {
        $statement = $this->getEntityManager()->getConnection()->prepare('SELECT organization_id, role FROM roles WHERE user_id = :user_id');
        $statement->bindValue("user_id", $user_id);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function findUserAvatar($user_id, $org_id) {
        $statement = $this->getEntityManager()->getConnection()->prepare('SELECT image_name FROM roles WHERE organization_id = :org_id AND user_id = :user_id');
        $statement->bindValue("org_id", $org_id);
        $statement->bindValue("user_id", $user_id);
        $statement->execute();
        return $statement->fetchAll()[0]['image_name'];
    }
}
