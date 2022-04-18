<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductsRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProductsRepository::class)
 */
class Products
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("product:list")
     * @Groups("product:detail")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups("product:list")
     * @Groups("product:detail")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups("product:detail")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=45)
     * @Groups("product:list")
     * @Groups("product:detail")
     */
    private $color;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("product:detail")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups("product:list")
     * @Groups("product:detail")
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("product:list")
     * @Groups("product:detail")
     */
    private $featured_image;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getFeaturedImage(): ?string
    {
        return $this->featured_image;
    }

    public function setFeaturedImage(?string $featured_image): self
    {
        $this->featured_image = $featured_image;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
