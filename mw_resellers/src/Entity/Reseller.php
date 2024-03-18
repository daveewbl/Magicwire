<?php

namespace Weble\Module\Resellers\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Weble\Module\Resellers\Repository\ResellerRepository")
 * @ORM\Table(name="mw_resellers")
 * */
class Reseller
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id_reseller", type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $address;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $city;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $phone;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $email;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $lat;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $lng;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $active;

    /**
     * @ORM\ManyToOne(targetEntity="Weble\Module\Resellers\Entity\ResellerGroup", inversedBy="resellers")
     * @ORM\JoinColumn(name="id_reseller_group", referencedColumnName="id_reseller_group")
     * */
    private ?ResellerGroup $group;

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): Reseller
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setAddress(?string $address): Reseller
    {
        $this->address = $address;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address ?? null;
    }

    public function setCity(?string $city): Reseller
    {
        $this->city = $city;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city ?? null;
    }

    public function setPhone(?string $phone): Reseller
    {
        $this->phone = $phone;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone ?? null;
    }

    public function setEmail(?string $email): Reseller
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email ?? null;
    }

    public function setLat(?string $lat): Reseller
    {
        $this->lat = $lat;
        return $this;
    }

    public function getLat(): ?string
    {
        return $this->lat ?? null;
    }

    public function setLng(?string $lng): Reseller
    {
        $this->lng = $lng;
        return $this;
    }

    public function getLng(): ?string
    {
        return $this->lng ?? null;
    }

    public function setActive(bool $active): Reseller
    {
        $this->active = $active;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setGroup(ResellerGroup $group): Reseller
    {
        $this->group = $group;
        return $this;
    }

    public function getGroup(): ?ResellerGroup
    {
        return $this->group;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'address' => $this->getAddress(),
            'city' => $this->getCity(),
            'phone' => $this->getPhone(),
            'email' => $this->getEmail(),
            'lat' => $this->getLat(),
            'lng' => $this->getLng(),
            'active' => $this->isActive()
        ];
    }

    public function toArrayWithRelations($langIsoCode): array
    {
        return array_merge(
            $this->toArray(),
            ['group' => $this->getGroup() ? $this->getGroup()->toArray($langIsoCode) : null]
        );
    }

    public static function getDefinition(): array
    {
        return [
            'id',
            'name',
            'address',
            'city',
            'phone',
            'email',
            'lat',
            'lng',
            'active',
        ];
    }
}
