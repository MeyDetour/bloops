<?php

namespace App\Controller;

use App\Entity\Bloop;
use App\Repository\CommentRepository;
use Carbon\Carbon;

use App\Entity\Comment;
use App\Entity\Image;
use App\Form\BloopType;
use App\Form\CommentType;
use App\Form\ImageType;
use App\Repository\BloopRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommentController extends AbstractController
{

    #[Route('/comment/new', name: 'new_comment')]
    public function sendComment(EntityManagerInterface $manager, BloopRepository $bloopRepository, \Symfony\Component\HttpFoundation\Request $request): Response

    {
        if (!$this->getUser()) {
            return $this->json("no user connected", 400);

        }
        $data = json_decode($request->getContent(), true);
        if ($data) {
            $author = $this->getUser();
            $bloopId = $data['idBloop'];
            $content = $data['content'];

            $bloop = $bloopRepository->find($bloopId);
            if (!$bloop) {
                return $this->json("bloop not found", 404);
            }
            $comment = new Comment();

            $comment->setBloop($bloop);
            $comment->setAuthor($author);
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setContent($content);

            //affichage de la date
            $createdAt = $comment->getCreatedAt();
            $now = Carbon::now($createdAt->getTimezone());

            $diffInSeconds = $createdAt->getTimestamp() - $now->getTimestamp();
            $diffInDays = intval($diffInSeconds / (3600 * 24));
            $diffInHours = intval($diffInSeconds / 3600);

            if ($diffInHours < 1) {
                // If less than an hour
                $humanReadableDate = "il y a moins d'une heure";
            } elseif ($diffInDays < 1) {
                // If less than a day but more than an hour
                $humanReadableDate = "il y a $diffInHours heure" . ($diffInHours > 1 ? 's' : '');
            } elseif ($diffInDays < 4) {
                // If less than 4 days
                $humanReadableDate = "il y a $diffInDays jour" . ($diffInDays > 1 ? 's' : '');
            } else {
                // If 4 days or more
                $humanReadableDate = $createdAt->format('j F Y');
            }

            $manager->persist($comment);
            $manager->flush();

            return $this->json([
                'message' => 'ok',
                'id' => $comment->getId(),
                'content' => $comment->getContent(),
                'author' => $comment->getAuthor()->getUsername(),
                'createdAt' => $comment->getCreatedAt(),
                'humanReadableDate' => $humanReadableDate,
            ]);
        }
        return $this->json(['message' => 'Aucune data trouvée']);

    }

    #[Route('/comment/delete', name: 'delete_comment')]
    public function delete(EntityManagerInterface $manager, CommentRepository $commentRepository, Request $request): Response

    {

        if (!$this->getUser()) {
            return $this->json("no user connected", 400);
        }
        $data = json_decode($request->getContent(), true);
        // Vérifier si les données sont bien reçues
        if ($data) {
            $comment = $commentRepository->find($data['id']);
            if (!$comment) {
                return $this->json("comment not found", 404);
            }
            if ($this->getUser() != $comment->getAuthor()) {
                return $this->json(['message' => "Propriety error"]);
            }
            $manager->remove($comment);
            $manager->flush();
            return $this->json(['message' => 'ok']);

        }
        return $this->json(['message' => 'Aucune data trouvée']);
    }

}
