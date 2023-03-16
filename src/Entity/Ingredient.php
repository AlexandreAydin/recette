<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
// permet de ne pas avoir plusieur ingrédient du portant le même nom
#[UniqueEntity('name')]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    // on ne veu pas que la valeur sois nul 
    #[Assert\NotBlank()]
    //permet de metrre min 2 max 50
    #[Assert\Length(min: 2, max: 50)]
    private ?string $name = null;

    #[ORM\Column]
    // on ne veu pas que la valeur sois nul 
    #[Assert\NotNull()]
    // permet de mettre min 1
    #[Assert\Positive]
    // permet de metrre max 200
    #[Assert\LessThan(200)]
    private ?float $price = null;

    #[ORM\Column]
    // on ne veu pas que la valeur sois nul 
    #[Assert\NotNull()]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'ingredients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    //permet de crée la date  de creation de ingrédients automatiquement
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    //Pour enlevé l'erreur object of class App\Entity\Ingredient could not be converted to string
    public function __toString()
    {
        return $this->name;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
