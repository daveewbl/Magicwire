<?php

namespace Weble\Module\Resellers\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShopBundle\Entity\Lang;

/**
 * @ORM\Entity()
 * @ORM\Table(name="mw_reseller_groups")
 * */
class ResellerGroup
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id_reseller_group", type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="json")
     */
    private array $name;

    /**
     * @ORM\Column(type="string")
     * */
    private string $zone;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $active = true;

    /**
     * @ORM\OneToMany(targetEntity="Weble\Module\Resellers\Entity\Reseller", cascade={"persist", "remove"}, mappedBy="group")
     * */
    private $resellers;

    public function __construct()
    {
        /** @var EntityManager $em */
        $em = SymfonyContainer::getInstance()->get('doctrine.orm.entity_manager');
        $langRepository = $em->getRepository(Lang::class);

        foreach ($langRepository->findAll() as $lang) {
            $this->name[$lang->getIsoCode()] = null;
        }

        $this->resellers = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name, $langIsoCode): ResellerGroup
    {
        $this->name[$langIsoCode] = $name;
        return $this;
    }

    public function getName($langIsoCode): ?string
    {
        if (array_key_exists($langIsoCode, $this->name)) {
            return $this->name[$langIsoCode];
        }

        return null;
    }

    public function setZone(string $zone): ResellerGroup
    {
        $this->zone = $zone;
        return $this;
    }

    public function getZone(): string
    {
        return $this->zone;
    }

    public function setActive(bool $active): ResellerGroup
    {
        $this->active = $active;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getResellers(): PersistentCollection
    {
        return $this->resellers;
    }

    public function addReseller(Reseller $reseller): ResellerGroup
    {
        $reseller->setGroup($this);
        $this->resellers->add($reseller);

        return $this;
    }

    public function toArray($langIsoCode): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName($langIsoCode),
            'zone' => $this->getZone(),
            'active' => $this->isActive()
        ];
    }

    public function toArrayWithRelations($langIsoCode): array
    {
        return array_merge(
            $this->toArray($langIsoCode),
            array_map(fn (Reseller  $reseller) => $reseller->toArray(), $this->getResellers()->toArray())
        );
    }

    public static function getDefinition(): array
    {
        return [
            'id',
            'name',
            'zone',
            'active'
        ];
    }
}
