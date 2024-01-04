<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/project', name: 'app_project')]
    public function index(): Response
    {
        $em = $this->doctrine->getManager();
        $projects = $em->getRepository(Project::class)->findAll();
        
        return $this->render('main/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/create-project', name: 'create_project')]
    public function create(Request $request): Response 
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->doctrine->getManager();
            $em->persist($project);
            $em->flush();
            
            $this->addFlash("success", "Le projet '{$project->getName()}' a été créé !");
            return $this->redirectToRoute('app_project');
        }
        
        return $this->render('main/project.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/edit-project/{id}', name: 'edit_project')]
    public function editProject(Project $project, Request $request): Response 
    {
      
        
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->doctrine->getManager();
            $em->flush();
            
            $this->addFlash("warning", "Le projet '{$project->getName()}' a été modifié !");

            return $this->redirectToRoute('app_project');
        }
        
        return $this->render('main/project.html.twig', [
            'form' => $form->createView(),
            'project' => $project,
        ]);
    }
    #[Route('/delete-project/{id}', name: 'delete_project')]
    public function deleteProject(Project $project): Response 
    {
        $em = $this->doctrine->getManager();
        $em->remove($project);
        $em->flush();
        
        $this->addFlash("success", "Le projet '{$project->getName()}' a été supprimé avec succès !");
    
        return $this->redirectToRoute('app_project');
    }

    #[Route('/about-me', name: 'about_me')]
    public function about(): Response{
        return $this->render('about_me/index.html.twig', [
            'controller_name' => 'AboutMeController',
        ]);
    }
    #[Route('/home', name: 'home')]
    public function home(): Response{
        return $this->render('home/home.html.twig', [
            'controller_name' => 'AboutMeController',
        ]);
    }
    
    
}   

   

