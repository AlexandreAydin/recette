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
    #[IsGranted('ROLE_USER')]
    #[Route('/recette', name: 'app_recipe', methods:['GET'])]
    public function index(RecipeRepository $repository,
        PaginatorInterface $paginator,
      Request $request
      ): Response
    {

        $recipes = $paginator->paginate(
            // permet d'appeler tout les ingrédient qui sont dans la base de données
                // $repository ->findAll(),
             //Permet d'appeler uniquement les recettes que l'utilisateur a crée
             $repository ->findBy( ['user' => $this ->getUser()]),
            $request->query->getInt('page', 1), /*Numéro de page*/
            10 /*limit par page*/
        );


        return $this->render('pages/recipe/index.html.twig',[
            "recipes"=>$recipes
        ]);
    }




    #[Route('/recette/communaute', 'app_recipe.community', methods: ['GET'])]
    public function indexPublic(
        RecipeRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $cache = new FilesystemAdapter();
        $data = $cache->get('recipes', function (ItemInterface $item) use ($repository) {
            $item->expiresAfter(15);
            return $repository->findPublicRecipe(null);
        });

        $recipes = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        
        return $this->render('pages/recipe/community.html.twig', [
            'recipes' => $recipes
        ]);

    }



      /**
     * This Controller allow us to see a recipe id this a one is public
     *
     * @param Recipe $recipe
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and recipe.isIsPublic() === true || user === recipe.getUser()")]
    #[Route('/recette/{id}', name:'app_recipe.show', methods: ['GET', 'POST'] )]
    public function show(
    Recipe $recipe, 
    Request $request,
    MarkRepository $markRepository,
    EntityManagerInterface $manager
    ) : Response
    {
        $mark= new Mark();
        $form = $this->createForm(MarkType::class, $mark);
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            // on prend l'utilisateur courents 
            $mark->setUser($this->getUser())
                //on prend la recette courent
                ->setRecipe($recipe);



            // un utilisateur ne peu pas voter 2 fois 
            $existingMark = $markRepository->findOneBy([
                'user'=>$this->getUser(),
                'recipe'=>$recipe
            ]);

            //si l'utilisateur n'a pas déja voté il peu voté 
            if(!$existingMark){
                $manager->persist ($mark);
            }else {
                // Il peu modifier sa note
                $existingMark->setMark(
                    $form->getData()->getMark()
                );

            }

            $manager->flush();

            $this->addFlash(
                'success',
                'Votre note a bien été prise en compte'
            );
            return $this->redirectToRoute('app_recipe.show', ['id'=> $recipe->getId()]);
        }

        return $this->render('pages/recipe/show.html.twig',[
            "recipe"=>$recipe,
            "form"=>$form->createView()
        ]);
    }



    /**
     * Permet d'ajouter une nouvelle recette 
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/recette_creation','app_recipe.new', methods:['GET','POST'] )]
    
    public function new(
    Request $request,
    RecipeRepository $repository,
    EntityManagerInterface $manager
    ): Response
    {
        $recipe = new Recipe();
        $form=$this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request); 
        if ($form->isSubmitted()&& $form->isValid()){
            $recipe = $form->getData(); 
       
            //permet de attaché la recette à l'utilisateur qui la crée
            $recipe->setUser($this->getUser());

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
    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
    #[Route('/recette/edition/{id}', 'app_recipe.edit', methods: ['GET' , 'POST'])]
    public function edit(
        Recipe $recipe,
        Request $request,
        EntityManagerInterface $manager
        ) : Response 
    {

        // permet de récupéré recette par id (paramconverter symfony permet de récuperer le id sans passer par repository )
        // Permet de récupéré Formulaire qui se trouve dans Recette
        $form= $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        //si la formulaire a était soumit et valid 
        if($form->isSubmitted() && $form->isValid()){
            // Enregistre Le formulaire dans labase de donnéé
            $recipe = $form->getData();

            $manager->persist($recipe);
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

    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
    #[Route('/recette/suppression/{id}', 'app_recipe.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Recipe $recipe ) : Response 
    {
        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre recette a été supprimé avec succès!'
        );

        return $this->redirectToRoute('app_recipe');
    }


}
