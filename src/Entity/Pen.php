<?php

namespace App\Entity;

use App\Repository\PenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PenRepository::class)]
class Pen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Groups("pen_list")]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups("pen_list")]
    private ?float $price = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups("pen_list")]
    private ?string $description = null;

    #[ORM\Column(length: 10)]
    #[Groups("pen_list")]
    private ?string $ref = null;

    #[ORM\ManyToOne(inversedBy: 'pens')]
    #[Groups("pen_list")]
    private ?Type $type = null;

    #[ORM\ManyToOne(inversedBy: 'pens')]
    #[Groups("pen_list")]
    private ?Material $material = null;

    #[ORM\ManyToMany(targetEntity: Color::class, inversedBy: 'pens')]
    #[Groups("pen_list")]
    private Collection $color;

    #[ORM\ManyToOne(inversedBy: 'pens')]
    #[Groups("pen_list")]
    private ?Brand $brand = null;

    public function __construct()
    {
        $this->color = new ArrayCollection();
        $this->name = null;
        $this->price = null;
        $this->description = null;
        $this->ref = null;
        $this->type = null;
        $this->material = null;
        $this->brand = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(string $ref): static
    {
        $this->ref = $ref;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getMaterial(): ?Material
    {
        return $this->material;
    }

    public function setMaterial(?Material $material): static
    {
        $this->material = $material;

        return $this;
    }

    /**
     * @return Collection<int, Color>
     */
    public function getColor(): Collection
    {
        return $this->color;
    }

    public function addColor(Color $color): static
    {
        if (!$this->color->contains($color)) {
            $this->color->add($color);
        }

        return $this;
    }

    public function removeColor(Color $color): static
    {
        $this->color->removeElement($color);

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): static
    {
        $this->brand = $brand;

        return $this;
    }
}
