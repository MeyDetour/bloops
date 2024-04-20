<?php

namespace App\Controller;


use App\Entity\Bloop;
use App\Entity\Comment;
use App\Entity\FriendRequest;
use App\Entity\Like;
use App\Repository\AudioRepository;
use App\Repository\BloopRepository;
use App\Repository\CommentRepository;
use App\Repository\FriendRequestRepository;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LikeController extends AbstractController
{
    #[Route('/like/bloop/{id}', name: 'like_article')]
    #[Route('/like/podcast/{id}', name: 'like_podcast')]
    #[Route('/like/comment/{id}', name: 'like_comment')]
    public function like( FriendRequestRepository $friendRequestRepository,Request $request, LikeRepository $likeRepository, AudioRepository $audioRepository, EntityManagerInterface $manager, $id, CommentRepository $commentRepository, BloopRepository $bloopRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json("no user connected", 400);
        }

        $route = $request->attributes->get("_route");
        if ($route == "like_article") {
            $bloop = $bloopRepository->find($id);
            $mode = "bloop";
        }
        if ($route == "like_comment") {
            $comment = $commentRepository->find($id);
            $mode = "comment";
        }
        if ($route == "like_podcast") {
            $podcast = $audioRepository->find($id);
            $mode = "podcast";
        }
        $search = [
            "author" => $user
        ];
        if ($mode == "bloop") {
            $search["bloop"] = $bloop;
            $friendRequest = $friendRequestRepository->findOneBy(['requested' => $bloop->getAuthor(), 'requester' => $this->getUser(), 'type' => 'BLOOP']);
            if ($friendRequest) {
                $manager->remove($friendRequest);

            }
            $friendRequest = new FriendRequest();
            $friendRequest->setStatus('ACTIVE');
            $friendRequest->setStatus('BLOOP');
            $friendRequest->setRequester($this->getUser());
            $friendRequest->setRequested($bloop->getAuthor());
        }
        if ($mode == "comment") {
            $search["comment"] = $comment;
            $friendRequest = $friendRequestRepository->findOneBy(['requested' => $comment->getAuthor(), 'requester' => $this->getUser(), 'type' => 'COMMENT']);
            if ($friendRequest) {
                $manager->remove($friendRequest);

            }
            $friendRequest = new FriendRequest();
            $friendRequest->setStatus('ACTIVE');
            $friendRequest->setStatus('COMMENT');
            $friendRequest->setRequester($this->getUser());
            $friendRequest->setRequested($comment->getAuthor());
        }
        if ($mode == "podcast") {
            $search["podcast"] = $podcast;
            $friendRequest = $friendRequestRepository->findOneBy(['requested' => $podcast->getAuthor(), 'requester' => $this->getUser(), 'type' => 'PODCAST']);
            if ($friendRequest) {
                $manager->remove($friendRequest);

            }
            $friendRequest = new FriendRequest();
            $friendRequest->setStatus('ACTIVE');
            $friendRequest->setStatus('PODCAST');
            $friendRequest->setRequester($this->getUser());
            $friendRequest->setRequested($podcast->getAuthor());

        }

        $like = $likeRepository->findOneBy($search);
        if (!$like) {
            $like = new Like();
            $like->setAuthor($user);
            if ($mode == "bloop") {
                $like->setBloop($bloop);
            }
            if ($mode == "comment") {
                $like->setComment($comment);
            }
            if ($mode == "podcast") {
                $like->setPodcast($podcast);
            }
            $manager->persist($like);
            $isLiked = true;

        } else {
            $manager->remove($like);
            $isLiked = false;
        }
//
        $manager->flush();

        if ($mode == "bloop") {
            $countSearch = [
                "bloop" => $bloop
            ];
        }

        if ($mode == "comment") {
            $countSearch = ["comment" => $comment];
        }
        if ($mode == "podcast") {
            $countSearch = ["podcast" => $podcast];
        }
        $count = $likeRepository->count($countSearch);
        $data = [
            "isLiked" => $isLiked,
            "count" => $count
        ];
        return $this->json($data, 200);
    }

}
