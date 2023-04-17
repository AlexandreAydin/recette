<?php

namespace App\Tests\Functional;

use App\Entity\Ingredient;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IngredientTest extends WebTestCase
{
    public function testIfCreateIngredientIsSuccessfull(): void
    {
        $client = static::createClient();
        //recup urlGenerator
        $urlGenerator = $client->getContainer()->get('router');
        //recup entity manager
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        // récup utilisateur 
        $user = $entityManager->find(User::class, 1);

        $client->loginUser($user);
        //Se rendre sur la page création d'ingrédient
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app_ingredient.new'));
        //Gerer le formulaire
        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => "Ingredient " . uniqid(),
            'ingredient[price]' => floatval(199)
        ]);

        $client->submit($form);
        //Gerer la redirection
        $statusCode = $client->getResponse()->getStatusCode();
      
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();
        //Gerer l'alert box et la rout
        // $this->assertSelectorTextContains('div.alert-success', 'Votre ingrédient a été créé avec succès !');

        $this->assertRouteSame('app_ingredient');
    }

    public function testIfListingredientIsSuccessful(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router');

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);

        $client->loginUser($user);

        $client->request(Request::METHOD_GET, $urlGenerator->generate('app_ingredient'));

        $this->assertResponseIsSuccessful();

        $this->assertRouteSame('app_ingredient');
    }

    public function testIfUpdateAnIngredientIsSuccessfull(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy([
            'user' => $user
        ]);

        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('app_ingredient.edit', ['id' => $ingredient->getId()])
        );

        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => "Un ingrédient 2" . uniqid(),
            'ingredient[price]' => floatval(34)
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-success', 'Votre ingrédient a été modifié avec succès !');

        $this->assertRouteSame('app_ingredient');
    }

    public function testIfDeleteAnIngredientIsSuccessful(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy([
            'user' => $user
        ]);

        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('app_ingredient.delete', ['id' => $ingredient->getId()])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-success', 'Votre ingrédient a été supprimé avec succès !');

        $this->assertRouteSame('app_ingredient');
    }
}

