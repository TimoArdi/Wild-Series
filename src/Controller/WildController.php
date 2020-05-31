<?php


namespace App\Controller;



use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
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

    /**
     * @Route("/wild/program/{slug}", requirements={"slug"="[a-zA-Z0-9- ]+"}, name="wild_program")
     * @param string $slug
     * @return Response
     */

    public function showByProgram(?string $slug = ''): Response
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

        return $this->render('wild/program.html.twig', [
            'slug' => $slug,
            'program' => $program,
            'seasons'=> $program->getSeasons(),
        ]);
    }
    /**
     * @Route("wild/season/{seasonId}", requirements={"season"="[0-9]+"}, name="show_season")
     * @param int $seasonId
     * @return Response
     */
    public function showBySeason(int $seasonId): Response
    {
        if (!$seasonId) {
            throw $this
                ->createNotFoundException('No season has been found');
        }
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['id' => mb_strtolower($seasonId)]);

        $program  = $season->getProgram();
        $episodes = $season->getEpisodes();

        return $this->render('wild/episodes.html.twig', [
            'episodes' => $episodes,
            'program'  => $program,
            'season'   => $season,
        ]);
    }
    /**
     * @Route("/episode/{id}", name="show_episode")
     * @param Episode $episode
     * @return Response
     */

    public function showEpisode(Episode $episode):Response
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();

        return $this->render('wild/episode.html.twig', [
            'episode' => $episode,
            'program'  => $program,
            'season'   => $season,
        ]);

    }
}