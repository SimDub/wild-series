<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\CategoryType;
use App\Form\ProgramSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WildController extends AbstractController
{
    /**
     * Show all rows from Program's entity
     *
     * @Route("/wild", name="wild_index")
     * @return Response A response instance
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(ProgramSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $programs = $this->getDoctrine()
                ->getRepository(Program::class)
                ->findBy(['title' => $data['searchField']]);
        } else {
            $programs = $this->getDoctrine()
                ->getRepository(Program::class)
                ->findAll();
        }

        return $this->render('wild/index.html.twig', [
            'programs'=> $programs,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/wild/show/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="wild_show")
     * @return Response
     */
    public function show(?string $slug):Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
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
                'No program with '.$slug.' title, found in program\'s table.'
            );
        }
        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug'  => $slug,
        ]);
    }

    /**
     *
     *@Route("/wild/category/{categoryName}", defaults={"categoryName" = null}, name="show_category")
     *@return Response
     */
    public function showByCategory(?string $categoryName):Response
    {
        if ($categoryName === null) {
            $category = $this->getDoctrine()
                ->getRepository(Category::class)
                ->findAll();
        } else {
            $category = $this->getDoctrine()
                ->getRepository(Category::class)
                ->findBy(array('name' => ucfirst(mb_strtolower($categoryName))));
        }

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(
                ["category"=>$category],
                ["id" => 'DESC'],
                3,
                0
                );

        if (!$category){
            throw $this->createNotFoundException(
                'No program with '.$categoryName.' , found in program\'s table.'
            );
        }

        return $this->render('wild/category.html.twig', [
            'programs' => $programs,
            'categoryName'  => $categoryName,
            'category' => $category,
        ]);
    }

    /**
     *
     *@Route("/wild/program/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="wild_program")
     *@return Response
     */
    public function showByProgram(?string $slug):Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);

        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findByProgram($program);


        return $this->render('wild/program.html.twig', [
            'program' => $program,
            'slug'  => $slug,
            'seasons' => $seasons
        ]);
    }

    /**
     *
     *@Route("/wild/season/{id}", defaults={"id" = null}, name="wild_season")
     *@return Response
     */
    public function showBySeason(int $id):Response

    {
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->find($id);

        return $this->render('wild/season.html.twig', [
            'season' => $season,
            'id'  => $id,
        ]);
    }

    /**
     *
     *@Route("/wild/episode/{id}", defaults={"id" = null}, name="wild_episode")
     *@return Response
     */
    public function showEpisode(Episode $episode):Response

    {
        $title = $episode->getSeason()->getProgram()->getTitle();
        $title = preg_replace(
            '/ /',
            '-', strtolower($title)
        );
        return $this->render('wild/episode.html.twig', [
            'episode' => $episode,
            'title' => $title
        ]);
    }
}