<?php

use Doctrine\ORM\EntityManager;
use Weble\Module\Resellers\Entity\Reseller;
use Weble\Module\Resellers\Entity\ResellerGroup;

class Mw_resellersItalyModuleFrontController extends ModuleFrontControllerCore
{
    public function initContent()
    {
        parent::initContent();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        $resellerGroupRepository = $em->getRepository(ResellerGroup::class);

        $group = $resellerGroupRepository->findOneBy(['zone' => 'it']);
        $resellers = array_map(fn (Reseller $reseller) => $reseller->toArray(), $group->getResellers()->toArray());

        $this->context->smarty->assign([
            'resellerGroup' => $group->getName($this->context->language->iso_code),
            'resellers' => $resellers
        ]);

        $this->setTemplate('module:mw_resellers/views/templates/front/map.tpl');
    }
}
