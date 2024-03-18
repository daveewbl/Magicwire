<?php

namespace Weble\Module\Resellers\Controller;

use Doctrine\ORM\EntityManager;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Weble\Module\Resellers\Entity\Reseller;
use Weble\Module\Resellers\Entity\ResellerGroup;
use Weble\Module\Resellers\Repository\ResellerRepository;

class AjaxResellerController extends FrameworkBundleAdminController
{
    private EntityManager $em;

    public function __construct()
    {
        $this->em = SymfonyContainer::getInstance()->get('doctrine.orm.entity_manager');
    }

    public function creteOrUpdateReseller(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            return new Response("Not an ajax request", 400);
        }

        $data = json_decode($request->getContent(), true);

        /** @var ResellerRepository $repository */
        $repository = $this->em->getRepository(Reseller::class);

        if (!$repository->createOrUpdate($data)) {
            return new Response("Error ".($data['id'] ? "updating" : "creating")." item", 500);
        }

        $items = $this->em->getRepository(Reseller::class)->findAll();

        return new JsonResponse(['items' => array_map(
            fn (Reseller $reseller) => $reseller->toArrayWithRelations($this->getContext()->language->iso_code),
            $items)]);
    }

    public function updateItemActiveState(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            return new Response("Not an ajax request", 400);
        }

        $data = json_decode($request->getContent(), true);

        $id = $data['id'] ?? null;

        if (!$id) {
            return new Response("No id provided", 500);
        }

        $state = $data['state'] ?? null;

        if ($state === null) {
            return new Response("No state provided", 500);
        }

        $item = $this->em->find(Reseller::class, $id);

        if (!$item) {
            return new Response("Error retrieving item", 500);
        }

        $item->setActive($state);

        $this->em->persist($item);
        $this->em->flush();

        return new Response();
    }

    public function deleteReseller(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            return new Response("Not an ajax request", 400);
        }

        $data = json_decode($request->getContent(), true);

        $id = $data['id'] ?? null;

        if (!$id) {
            return new Response("No id provided", 500);
        }

        $item = $this->em->find(Reseller::class, $id);

        if (!$item) {
            return new Response("Can't retrieve item", 500);
        }

        $this->em->remove($item);
        $this->em->flush();

        $items = $this->em->getRepository(Reseller::class)->findAll();

        return new JsonResponse(['items' => array_map(
            fn (Reseller $reseller) => $reseller->toArrayWithRelations($this->getContext()->language->iso_code),
            $items)]);
    }
}
