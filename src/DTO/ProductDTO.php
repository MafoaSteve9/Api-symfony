<?php
  
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProductDTO
{
    #[Assert\NotBlank(message: "Le nom est requis")]
    #[Assert\Length(max: 255, maxMessage: "Le nom ne doit pas dépasser 255 caractères")]
    public string $name;

    #[Assert\NotNull(message: "Le prix est requis")]
    #[Assert\Positive(message: "Le prix doit être positif")]
    public float $price;

    #[Assert\NotBlank(message: "La catégorie est requise")]
    #[Assert\Length(max: 100, maxMessage: "La catégorie ne doit pas dépasser 100 caractères")]
    public string $category;

    #[Assert\NotNull(message: "Le stock est requis")]
    public bool $stock;
}

