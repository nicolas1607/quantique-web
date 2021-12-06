<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use App\Entity\Contract;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NoteController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/admin/note/add/{contract}", name="add_note")
     */
    public function add(Request $request, Contract $contract): Response
    {
        $note = new Note();
        $note->setMessage(str_replace("\r\n", "\n", $request->get('message'))) // with twig nl2br
            ->setReleasedAt(new DateTime())
            ->setContract($contract)
            ->setUser($this->getUser());
        $contract->addNote($note);

        $this->em->persist($note);
        $this->em->persist($contract);
        $this->em->flush();

        return $this->redirectToRoute('show_contracts', ['company' => $contract->getWebsite()->getCompany()->getId()]);
    }

    /**
     * @Route("/admin/note/delete/{note}", name="delete_note")
     */
    public function delete(Note $note): Response
    {
        $this->em->remove($note);
        $note->getUser()->removeNote($note);
        $this->em->persist($note->getUser());
        $this->em->flush();

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}
