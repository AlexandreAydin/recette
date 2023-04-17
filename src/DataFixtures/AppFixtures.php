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

    
        // permet de crée des donnée en Français
        public function __construct()
        {
            $this->faker = Factory::create('fr_FR');

        }    
        //   ce la permet de crée 50 ingredints 
        public function load(ObjectManager $manager ): void
        {

        //Users
        $users = [];

        $admin = new User();
        $admin->setFullName('Administrateur de Site Recette')
            ->setPseudo(null) 
            ->SetEmail('admin@siterecette.fr')
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
            ->setPlainPassword('password');

        $users[] = $admin;
        $manager->persist($admin);

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setFullName($this->faker->name())
                ->setPseudo(mt_rand(0, 1) === 1 ? $this->faker->firstName() : null)
                ->setEmail($this->faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPlainPassword('password');

            $users[] = $user;
            $manager->persist($user);
        }


            // Ingredients 
            $ingredients = []; 
            for ($i=0; $i < 50; $i++) {
                    // nous permet de créé le 1er ingredient dans la base de donné 
                $ingredient = new Ingredient();
                // permet de donné des noms à 1 mots si on met word(2) ce la permetera de mettre un nom a 2 nom
                $ingredient->setName($this->faker->word())
                    // ce la génére un prix entre 1 et 100
                    ->setPrice(mt_rand(1, 100))
                    ->setUser($users[mt_rand(0, count($users) -1)]);
             


                $ingredients[]=$ingredient;
                $manager->persist($ingredient);

                }

            //Recipes
            $recipes = [];
            for ($j=0; $j < 25; $j++) {

            $recipe = new Recipe();
            $recipe->setName($this->faker->word())
            // permet de definir le temps le temps peu etre nul
            ->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 1440) : null)
            ->setNbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null)
            ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null)
            ->setDescription($this->faker->text(300))
            ->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 1000) : null)
            ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false)
            ->setIsPublic(mt_rand(0, 1) == 1 ? true : false)
            ->setUser($users[mt_rand(0, count($users) -1)]);

            for ($k = 0; $k < mt_rand(5, 15); $k++) {
                $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients) - 1)]);
            }

            $recipes[] = $recipe;
            $manager->persist($recipe);
        
        }

         // Marks
         foreach ($recipes as $recipe) {
            for ($i = 0; $i < mt_rand(0, 4); $i++) {
                $mark = new Mark();
                $mark->setMark(mt_rand(1, 5))
                    ->setUser($users[mt_rand(0, count($users) - 1)])
                    ->setRecipe($recipe);

                $manager->persist($mark);
            }
        }

         // Contact
         for ($i = 0; $i < 5; $i++) {
            $contact = new Contact();
            $contact->setFullName($this->faker->name())
                ->setEmail($this->faker->email())
                ->setSubject('Demande n°' . ($i + 1))
                ->setMessage($this->faker->text());

            $manager->persist($contact);
        }






        $manager->flush();
    }
}