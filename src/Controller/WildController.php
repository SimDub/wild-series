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
     * Show all rows from Program's entity
     *
     * @Route("/wild", name="wild_index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw  $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }


        return $this->render(
            'wild/index.html.twig',
            ['programs'=> $programs]
        );
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
     *@Route("/wild/category/{categoryName}", name="show_category")
     *@return Response
     */
    public function showByCategory(?string $categoryName):Response
    {
        if (!$categoryName) {
            throw $this
                ->createNotFoundException('No categoryName has been sent to find a program in program\'s table.');
        }
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findBy(array('name' => ucfirst(mb_strtolower($categoryName))));

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
            'category' => $category
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
}