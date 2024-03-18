<?php

use Doctrine\ORM\EntityManager;
use Weble\Module\Resellers\Entity\Reseller;
use Weble\Module\Resellers\Entity\ResellerGroup;

class Mw_resellersSpainModuleFrontController extends ModuleFrontControllerCore
{
    public function initContent()
    {
        parent::initContent();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        $resellerGroupRepository = $em->getRepository(ResellerGroup::class);

        $group = $resellerGroupRepository->findOneBy(['zone' => 'sp']);
        $resellers = array_map(
            fn (Reseller $reseller) => $reseller->toArray(),
            $group->getResellers()->filter(fn (Reseller $reseller) => $reseller->isActive())->toArray());

        $this->context->smarty->assign([
            'resellerGroup' => $group->getName($this->context->language->iso_code),
            'resellers' => array_values($resellers),
            'mapCenter' => $group->getMapCenter(),
            'vueAppJs' => "modules/mw_resellers/views/js/front.js"
        ]);

        $this->setTemplate('module:mw_resellers/views/templates/front/map.tpl');
    }
}
