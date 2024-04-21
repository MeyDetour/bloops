<?php

namespace App\Controller;

use App\Entity\Report;
use App\Entity\User;
use App\Repository\BloopRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReportController extends AbstractController
{
    #[Route('/report', name: 'app_report')]
    public function report(Request                $request, BloopRepository $bloopRepository, UserRepository $userRepository,
                           EntityManagerInterface $manager): Response
    {

        if (!$this->getUser()) {
            return $this->json("no user connected", 400);
        }
        $data = json_decode($request->getContent(), true);
        // Vérifier si les données sont bien reçues
        if ($data) {

            $user2 = $userRepository->find($data['id2']);
            $bloop = $bloopRepository->find($data['idBloop']);
            $reason = $data['reason'];
            if (!$user2) {
                return $this->json(['message' => 'Accounts not found'], Response::HTTP_NOT_FOUND);
            }
            if (!$bloop) {
                return $this->json(['message' => 'Bloop does not exist'], Response::HTTP_NOT_FOUND);
            }
            if ($bloop->getAuthor() != $user2) {
                return $this->json(['message' => 'owner error'], Response::HTTP_NOT_FOUND);
            }
            $report = new Report();
            $report->setAuthor($this->getUser());
            $report->setTargetAuthor($user2);
            $report->setBloop($bloop);
            $report->setReason($reason);
            $report->setCreatedAt(new \DateTimeImmutable('now'));
            $manager->persist($report);
            $manager->flush();
            return $this->json(['message' => 'ok']);

        }
        return $this->json(['message' => 'Aucune data trouvée']);
    }
}