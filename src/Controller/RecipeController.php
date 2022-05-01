<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{




    /**
     * this controller display all recipes
     */
    #[Route('/recette', name: 'recipe.index', methods: ['GET'])]
    public function index(
        Request $request,
        PaginatorInterface $paginator,
        RecipeRepository $repository
    ): Response {
        $recipes = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }


    /**
     *this controller creates of recipes
     */
    #[Route('/recette/creation',  'recipe.new', methods: ['GET', 'POST'])]
    public function new(
        EntityManagerInterface $em,
        Request $request
    ): Response {

        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $em->persist($recipe);
            $em->flush();

            $this->addFlash(
                'success',
                'Votre recette a été créé avec succès !'
            );
            return $this->redirectToRoute('recipe.index');
        }



        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/recette/edition/{id}', 'recipe.edit', methods: ['GET', 'POST'])]
    public function edit(
        Recipe $recipe,
        Request $request,
        EntityManagerInterface $em
    ): Response {

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $em->persist($recipe);
            $em->flush();


            $this->addFlash(
                'success',
                'Votre recette à bien été modifié ! '
            );

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView()

        ]);
    }

    // param converter
    #[Route('/recette/suppression/{id}', 'recipe.delete', methods: ['GET'])]
    public function delete(
        EntityManagerInterface $em,
        Recipe $recipe
    ): Response {

        $em->remove($recipe);
        $em->flush();

        $this->addFlash(
            'success',
            'Votre recette à bien été supprimé'
        );

        return $this->redirectToRoute('recipe.index');
    }
}
