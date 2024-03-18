<?php

namespace Weble\Module\Resellers\Controller;

use Doctrine\ORM\EntityManager;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Entity\Lang;
use Symfony\Component\HttpFoundation\Response;
use Weble\Module\Resellers\Entity\Reseller;
use Weble\Module\Resellers\Entity\ResellerGroup;

class ResellerController extends FrameworkBundleAdminController
{
    private EntityManager $em;

    public function __construct()
    {
        $this->em = SymfonyContainer::getInstance()->get('doctrine.orm.entity_manager');
    }

    public function listAction(): Response
    {
        $items = $this->em->getRepository(Reseller::class);

        $items = array_map(
            fn (Reseller $reseller) => $reseller->toArrayWithRelations($this->getContext()->language->iso_code),
            $items->findAll()
        );

        return $this->render("@Modules/mw_resellers/views/templates/admin/list.html.twig", [
            'items' => $items,
            'itemSkeleton' => $this->getItemSkeleton(),
            'languages' => $this->getLanguages(),
            'resellerGroups' => $this->getResellerGroups(),
            'scripts' => [
                'vueAppJs' => "modules/mw_resellers/views/js/app.js",
                'chunkVendorsJs' => "modules/mw_resellers/views/js/chunk-vendors.js",
            ]
        ]);
    }

    private function getLanguages(): array
    {
        $langRepository = $this->em->getRepository(Lang::class);

        return array_map(function (Lang $lang) {
            return [
                'id' => $lang->getId(),
                'name' => $lang->getName(),
                'isoCode' => $lang->getIsoCode(),
                'isContextLang' => $lang->getId() === $this->getContextLangId()
            ];
        }, $langRepository->findAll());
    }

    private function getItemSkeleton(): array
    {
        $definition = [];

        foreach (Reseller::getDefinition() as $key) {
            $definition[$key] = null;
        }

        foreach (ResellerGroup::getDefinition() as $key) {
            $definition['group'][$key] = null;
        }

        $definition['active'] = true;

        return $definition;
    }

    private function getResellerGroups(): array
    {
        return array_map(
            fn(ResellerGroup $group) => $group->toArray($this->getContext()->language->iso_code),
            $this->em->getRepository(ResellerGroup::class)->findAll()
        );
    }
}
