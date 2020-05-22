<?php


namespace App\Controller;



use App\Entity\Category;
use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WildController extends AbstractController
{
    /**
     * @Route("/wild", name="wild_index")
     * @return Response A response instance
     */
    public function index() :Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();
        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }
        return $this->render(
            'wild/index.html.twig', [
            'programs' => $programs,
        ]);
    }

    /**
     * @Route("/wild/show/{slug}", requirements={"slug"="[a-z0-9-]+"}, name="wild_show")
     * @param string $slug
     * @return Response
     */
    public function show(?string $slug = ''): Response
    {
        if (!$slug) {
            throw $this->createNotFoundException(
                'No slug has been sent to find a program in program\'s table .'
            );
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$slug. ' title, found in program\'s table .'
            );
        }

        return $this->render('wild/show.html.twig', [
            'slug' => $slug,
            'program' => $program,
        ]);
    }
    /**
     * @Route("/wild/category/{categoryName}", requirements={"category"="[a-z0-9-]+"}, name="show_category")
     * @param string $categoryName
     * @return Response
     */

    public function showByCategory(string $categoryName = ''): Response
    {
        if (!$categoryName) {
            throw $this->createNotFoundException('No Category Name has been sent to find a program');
        }
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => mb_strtolower($categoryName)]);

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category' => $category->getId()],['id' => 'DESC'],3);

        return $this->render('wild/category.html.twig', [
            'programs' => $programs,
            'category' => $category,
        ]);
    }


}