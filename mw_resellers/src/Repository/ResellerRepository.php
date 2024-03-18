<?php

namespace Weble\Module\Resellers\Repository;

use Doctrine\ORM\EntityRepository;
use Weble\Module\Resellers\Entity\Reseller;
use Weble\Module\Resellers\Entity\ResellerGroup;

class ResellerRepository extends EntityRepository
{
    public function createOrUpdate($data)
    {
        $data = (object) $data;

        if ($data->id) {
            $item = $this->find($data->id);
        } else {
            $item = new Reseller();
        }

        if (!$data->group && $data->group['id']) {
            return false;
        }

        $group = $this->getEntityManager()->find(ResellerGroup::class, $data->group['id']);

        if(!$group) {
            return false;
        }

        $item
            ->setName($data->name)
            ->setAddress($data->address)
            ->setCity($data->city)
            ->setEmail($data->email)
            ->setPhone($data->phone)
            ->setLat($data->lat)
            ->setLng($data->lng)
            ->setActive($data->active)
            ->setGroup($group);

        $this->getEntityManager()->persist($item);

        $this->getEntityManager()->flush();

        return true;
    }
}
