<?php

namespace App\DataFixtures;


use App\Entity\Contact;
use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Entity\Mark;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


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

            // Ingredients 
            $ingredients = []; 
            for ($i=0; $i < 50; $i++) {
                    // nous permet de créé le 1er ingredient dans la base de donné 
                $ingredient = new Ingredient();
                // permet de donné des noms à 1 mots si on met word(2) ce la permetera de mettre un nom a 2 nom
                $ingredient->setName($this->faker->word())
                    // ce la génére un prix entre 1 et 100
                    ->setPrice(mt_rand(1, 100));
                    $ingredients[]=$ingredient;
                    $manager->persist($ingredient);

                }

            //Recipes
            for ($j=0; $j < 25; $j++) {

            $recipe = new Recipe();
            $recipe->setName($this->faker->word())
            // permet de definir le temps le temps peu etre nul
            ->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 1440) : null)
            ->setNbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null)
            ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null)
            ->setDescription($this->faker->text(300))
            ->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 1000) : null)
            ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false);

            for ($k = 0; $k < mt_rand(5, 15); $k++) {
                $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients) - 1)]);
            }

            $manager->persist($recipe);
        
        }
        $manager->flush();
    }
}