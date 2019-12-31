<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SearchRepository")
 */
class Search
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titleProduct;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitleProduct(): ?string
    {
        return $this->titleProduct;
    }

    public function setTitleProduct(string $titleProduct): self
    {
        $this->titleProduct = $titleProduct;

        return $this;
    }
}
