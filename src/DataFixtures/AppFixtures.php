<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use Faker\Generator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    // public function load(ObjectManager $manager): void
    // {

    //      // nous permet de créé le 1er ingredient dans la base de donné 
    //         //  $ingredient = new Ingredient();
    //         //  $ingredient->setName("Ingredient #1")
    //         //      ->setPrice(3.0);
    //         //  $manager->persist($ingredient);

    //         //  $manager->flush();
    // }

        /**
         *  @var Generator
         */
        private Generator $faker;
        // per met de crée des donnée en Français
        public function __construct()
        {
            $this->faker = Factory::create('fr_FR');
        }    
        //   ce la permet de crée 50 ingredints 
        public function load(ObjectManager $manager): void
        {

            for ($i=0; $i < 50; $i++) {
                    // nous permet de créé le 1er ingredient dans la base de donné 
                $ingredient = new Ingredient();
                // permet de donné des noms à 1 mots si on met word(2) ce la permetera de mettre un nom a 2 nom
                $ingredient->setName($this->faker->word())
                    // ce la génére un prix entre 1 et 100
                    ->setPrice(mt_rand(1, 100));
                    $manager->persist($ingredient);

                }
                $manager->flush();
        }

     
}
