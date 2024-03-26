<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{

    #[Route('/tasks', name: 'task_list')]
    public function listAction(EntityManagerInterface $em): Response
    {
        return $this->render('task/list.html.twig', ['tasks' => $em->getRepository(Task::class)->findAll()]);
    }

    #[Route('/tasks/create', name: 'task_create', methods: ['GET', 'POST'])]
    public function createAction(Request $request, EntityManagerInterface $em): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $task->setUser($this->getUser());

            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/tasks/edit/{id}', name: 'task_edit', methods: ['GET', 'POST'])]
    public function editAction(Task $task, Request $request, EntityManagerInterface $em): Response
    {
        $author = $task->getUser();
        $connectedUser = $this->getUser();
        $roles = $this->getUser()->getRoles();
        $username = $task->getUser()->getUsername();

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if (($form->isSubmitted() && $form->isValid()) && (($author == $connectedUser) || (in_array('ROLE_ADMIN', $roles) && $username == 'anonyme'))){

            $em->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }
        return $this->render('task/edit.html.twig', [
            
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/tasks/{id}/toggle', name: 'task_toggle')]
    public function toggleTaskAction(Task $task, EntityManagerInterface $em)
    {
        $author = $task->getUser();
        $connectedUser = $this->getUser();
        $roles = $this->getUser()->getRoles();
        $username = $task->getUser()->getUsername();

        if (($author == $connectedUser) || (in_array('ROLE_ADMIN', $roles) && $username == 'anonyme')){

        $task->toggle(!$task->isDone());
        $em->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        }else {
            $this->addFlash('error', "Vous n'avez pas les droits pour marquer comme faite cette tâche");
        }
        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    public function deleteTaskAction(Task $task, EntityManagerInterface $em)
    {
        $author = $task->getUser();
        $connectedUser = $this->getUser();
        $roles = $this->getUser()->getRoles();
        $username = $task->getUser()->getUsername();

        if (($author == $connectedUser) || (in_array('ROLE_ADMIN', $roles) && $username == 'anonyme')){

        $em->remove($task);
        $em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        }else {
            $this->addFlash('error', "Vous n'avez pas les droits pour supprimer cette tâche");
        }
        return $this->redirectToRoute('task_list');


    }
}
