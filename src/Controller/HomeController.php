<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\RecipeRepository;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', 'app_home', methods: ['GET'] )]
    public function index(RecipeRepository $recipeRepository): Response
    {
        return $this->render('pages/home.html.twig', [
            'recipes' => $recipeRepository->findPublicRecipe(3)
        ]);
    }
}
