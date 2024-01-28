<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Pen;
use App\Entity\Type;
use App\Entity\User;
use App\Entity\Brand;
use App\Entity\Color;
use App\Entity\Material;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;
    
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
         $faker = Factory::create();
        // Création d'un user "normal"
        $user = new User();
        $user->setEmail("user@bookapi.com");
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, "azerty"));
        $manager->persist($user);
        
        // Création d'un user admin
        $userAdmin = new User();
        $userAdmin->setEmail("demo@apipen.fr");
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "azery"));
        $manager->persist($userAdmin);
        // Création des types
        $types = [];
        foreach (['Bille', 'Plume', 'Rollerball', 'Feutre'] as $typeName) {
            $type = new Type();
            $type->setName($typeName);
            $manager->persist($type);
            $types[] = $type;
        }

        // Création des marques
        $brands = [];
        foreach (['Parker', 'Montblanc', 'Lamy', 'Waterman', 'Cross'] as $brandName) {
            $brand = new Brand();
            $brand->setName($brandName);
            $manager->persist($brand);
            $brands[] = $brand;
        }

        // Création des matériaux
        $materials = [];
        foreach (['Plastique', 'Métal', 'Bois', 'Acier', 'Aluminium'] as $materialName) {
            $material = new Material();
            $material->setName($materialName);
            $manager->persist($material);
            $materials[] = $material;
        }

        // Création des couleurs
        $colors = [];
        foreach (['Noir', 'Bleu', 'Rouge', 'Vert', 'Orange'] as $colorName) {
            $color = new Color();
            $color->setName($colorName);
            $manager->persist($color);
            $colors[] = $color;
        }

        // Génération de 100 stylos
        for ($i = 0; $i < 100; $i++) {
            $pen = new Pen();
            $pen->setName($faker->word);
            $pen->setPrice($faker->randomFloat(2, 5, 50));
            $pen->setDescription($faker->sentence);
            $pen->setRef($faker->unique()->ean13);

            $pen->setType($types[$faker->numberBetween(0, 3)]);
            $pen->setMaterial($materials[$faker->numberBetween(0, 4)]);
            $pen->setBrand($brands[$faker->numberBetween(0, 4)]);

            $colorCount = $faker->numberBetween(1, 3);
            for ($j = 0; $j < $colorCount; $j++) {
                $pen->addColor($colors[$faker->numberBetween(0, 4)]);
            }

            $manager->persist($pen);
        }

        $manager->flush();
    }
}
