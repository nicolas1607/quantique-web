<?php

namespace App\Controller;

use PDO;
use DateTime;
use PDOException;
use App\Entity\Note;
use App\Form\NoteType;
use App\Entity\Contract;
use App\Entity\User;
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
        // $note = new Note();
        $msg = str_replace("\r\n", "\n", $request->get('message'));
        $datetime = new DateTime();
        $date = $datetime->format('Y-m-d H:i:s');
        $user = $this->getUser()->getId();
        $contract = $contract->getId();
        // $note->setMessage($msg) // with twig nl2br
        //     ->setReleasedAt(new DateTime())
        //     ->setContract($contract)
        //     ->setUser($this->getUser());
        // $contract->addNote($note);

        // Connexion PDO
        $username = 'root';
        $password = 'root';

        try {
            $conn = new PDO('mysql:host=127.0.0.1:8889;dbname=quantique-web', $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            $stmt = $conn->prepare(
                "INSERT INTO note (user_id, contract_id, message, released_at) 
                VALUES (:user, :contract, :msg, :date)"
            );
            $stmt->bindParam(':user', $user);
            $stmt->bindParam(':contract', $contract);
            $stmt->bindParam(':msg', $msg);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }

        // $this->em->persist($note);
        // $this->em->persist($contract);
        // $this->em->flush();

        return $this->redirect($_SERVER['HTTP_REFERER']);
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

    /**
     * @Route("/admin/script/note/add/{user}/{contract}", name="script_add_note")
     */
    function addNote(Request $request, User $user, Contract $contract)
    {
        $user = $user->getId();
        $contract = $contract->getId();
        $msg = str_replace("\r\n", "\n", $request->get('message'));
        $datetime = new DateTime();
        $date = $datetime->format('Y-m-d H:i:s');

        try {
            $conn = new PDO('mysql:host=127.0.0.1:8889;dbname=quantique-web', 'root', 'root');
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }


        $stmt = $conn->prepare(
            "INSERT INTO note (user_id, contract_id, message, released_at) 
                    VALUES (:user, :contract, :msg, :date)"
        );
        $stmt->bindParam(':user', $user);
        $stmt->bindParam(':contract', $contract);
        $stmt->bindParam(':msg', $msg);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
    }
}
