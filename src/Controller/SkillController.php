<?php

namespace App\Controller;

use App\Entity\Skill;
use App\Form\SkillType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SkillController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/skills', name: 'app_skills')]
    public function index(): Response
    {
        $em = $this->doctrine->getManager();
        $skills = $em->getRepository(Skill::class)->findAll();
        
        return $this->render('skill/index.html.twig', [
            'skills' => $skills,
        ]);
    }

    #[Route('/create-skill', name: 'create_skill')]
    public function create(Request $request): Response 
    {
        $skill = new Skill();
        $form = $this->createForm(SkillType::class, $skill);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->doctrine->getManager();
            $em->persist($skill);
            $em->flush();
            
            $this->addFlash("success", "La compétence '{$skill->getName()}' a été créée !");
            return $this->redirectToRoute('app_skills');
        }
        
        return $this->render('skill/skill.html.twig', [
            'form' => $form->createView(),
            'skill' => $skill,
        ]);
    }

    #[Route('/edit-skill/{id}', name: 'edit_skill')]
    public function editSkill(Skill $skill, Request $request): Response 
    {
        $form = $this->createForm(SkillType::class, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->doctrine->getManager();
            $em->flush();
            
            $this->addFlash("warning", "La compétence '{$skill->getName()}' a été modifiée !");

            return $this->redirectToRoute('app_skills');
        }
        
        return $this->render('skill/skill.html.twig', [
            'form' => $form->createView(),
            'skill' => $skill,
        ]);
    }

    #[Route('/delete-skill/{id}', name: 'delete_skill')]
    public function deleteSkill(Skill $skill): Response 
    {
        $em = $this->doctrine->getManager();
        $em->remove($skill);
        $em->flush();
        
        $this->addFlash("success", "La compétence '{$skill->getName()}' a été supprimée avec succès !");
    
        return $this->redirectToRoute('app_skills');
    }
    #[Route('/skills', name: 'skills')]
    public function skills(): Response{
        return $this->render('skill/index.html.twig', [
            'controller_name' => 'AboutMeController',
        ]);
    }
}
