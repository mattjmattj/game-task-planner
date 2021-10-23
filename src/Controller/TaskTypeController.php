<?php

namespace App\Controller;

use App\Entity\TaskType;
use App\Form\TaskTypeType;
use App\Repository\TaskTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/task/type')]
class TaskTypeController extends AbstractController
{
    #[Route('/', name: 'task_type_index', methods: ['GET'])]
    public function index(TaskTypeRepository $taskTypeRepository): Response
    {
        return $this->render('task_type/index.html.twig', [
            'task_types' => $taskTypeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'task_type_new', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $taskType = new TaskType();
        $form = $this->createForm(TaskTypeType::class, $taskType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($taskType);
            $entityManager->flush();

            return $this->redirectToRoute('task_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task_type/new.html.twig', [
            'task_type' => $taskType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'task_type_show', methods: ['GET'])]
    public function show(TaskType $taskType): Response
    {
        return $this->render('task_type/show.html.twig', [
            'task_type' => $taskType,
        ]);
    }

    #[Route('/{id}/edit', name: 'task_type_edit', methods: ['GET','POST'])]
    public function edit(Request $request, TaskType $taskType): Response
    {
        $form = $this->createForm(TaskTypeType::class, $taskType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('task_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task_type/edit.html.twig', [
            'task_type' => $taskType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'task_type_delete', methods: ['POST'])]
    public function delete(Request $request, TaskType $taskType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$taskType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($taskType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('task_type_index', [], Response::HTTP_SEE_OTHER);
    }
}
