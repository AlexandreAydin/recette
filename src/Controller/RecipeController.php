<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Form\MarkType;
use App\Form\RecipeType;
use App\Repository\MarkRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;



class RecipeController extends AbstractController
{

    /**
     * Le controller permet d'afficher tout les recette
     *
     * @param RecipeRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/recette', name: 'app_recipe', methods:['GET'])]
    public function index(RecipeRepository $repository,
        PaginatorInterface $paginator,
      Request $request
      ): Response
    {

        $recipes = $paginator->paginate(
            // permet d'appeler tout les ingrédient qui sont dans la base de données
            $repository ->findAll(),
            $request->query->getInt('page', 1), /*Numéro de page*/
            10 /*limit par page*/
        );


        return $this->render('pages/recipe/index.html.twig',[
            "recipes"=>$recipes
        ]);
    }


    /**
     * Permet d'ajouter une nouvelle recette 
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recette/creation','app_recipe.new', methods:['GET','POST'] )]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $recipe = new Recipe();
        $form=$this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request); 
        if ($form->isSubmitted()&& $form->isValid()){
            $recipe = $form->getData(); 

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été créé avec succès!'
            );

            return $this->redirectToRoute('app_recipe');
        }


        return $this->render('pages/recipe/new.html.twig', [
        'form'=>$form->createView()
        ]);
    }


    /**
     * permet de modifier les recette
     *
     * @param Recipe $recette
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recette/edition/{id}', 'app_recette.edit', methods: ['GET' , 'POST'])]
    public function edit(
        Recipe $recette,
        Request $request,
        EntityManagerInterface $manager
        ) : Response 
    {

        // permet de récupéré recette par id (paramconverter symfony permet de récuperer le id sans passer par repository )
        // Permet de récupéré Formulaire qui se trouve dans Recette
        $form= $this->createForm(RecipeType::class, $recette);

        $form->handleRequest($request);
        //si la formulaire a était soumit et valid 
        if($form->isSubmitted() && $form->isValid()){
            // Enregistre Le formulaire dans labase de donnéé
            $recette = $form->getData();

            $manager->persist($recette);
            $manager->flush();       

            // Message pour affiché la validation 
            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec succès!'
            );

            return $this->redirectToRoute('app_recipe');
        }

        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView()
        ]);


    }

    #[Route('/recette/suppression/{id}', 'app_recipe.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Recipe $recette ) : Response 
    {
        $manager->remove($recette);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre recette a été supprimé avec succès!'
        );

        return $this->redirectToRoute('app_recipe');
    }




}
