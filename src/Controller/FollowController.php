<?php

namespace App\Controller;

use App\Entity\FriendRequest;
use App\Entity\User;
use App\Repository\BloopRepository;

use App\Repository\FriendRequestRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FollowController extends AbstractController
{
    #[Route('/follow', name: 'app_follow')]
    public function sendFriend(User $user): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->json(['message' => 'ECHEC']);
    }

    #[Route('/user/{id}', name: 'show_user', priority: 4)]
    public function showUser(User $user, BloopRepository $bloopRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $likesTotal = 0;
        $myBloops = $bloopRepository->findBy(['author' => $user]);
        foreach ($myBloops as $bloop) {
            $likesTotal += count($bloop->getLikes());
        }

        return $this->render('/client/user/show.html.twig', ['user' => $user, 'totalLikes' => $likesTotal]);
    }

    #[Route('/friend/list', name: 'friend_list_component')]
    public function index(BloopRepository $repository): Response
    {
        if (!$this->getUser()) {
            return $this->json(['message' => 'no user']);
        }
        $likesTotal = 0;
        $myBloops = $repository->findBy(['author' => $this->getUser()]);
        foreach ($myBloops as $bloop) {
            $likesTotal += count($bloop->getLikes());
        }

        return $this->json('client/bloop/index.html.twig');
    }


//============================API ==================
    #[Route('/follow/user', name: 'follow_user')]
    public function followUser(UserRepository $userRepository, FriendRequestRepository $friendRequestRepository, EntityManagerInterface $manager, \Symfony\Component\HttpFoundation\Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $data = json_decode($request->getContent(), true);
        if ($data) {
            $user = $userRepository->find($data['id']);
            if (!$user) {
                return $this->json(['message' => 'user not found']);
            }
            if ($user == $this->getUser()) {
                return $this->json(['message' => 'same user']);

            }

            $user->addFollower($this->getUser());

            $friendRequest = new FriendRequest();
            $friendRequest->setStatus('ACTIVE');
            $friendRequest->setType('FOLLOW');
            $friendRequest->setVisible(true);
            $friendRequest->setRequester($this->getUser());
            $friendRequest->setRequested($user);
            $friendRequest->setCreatedAt(new \DateTimeImmutable());


            $manager->persist($friendRequest);
            $manager->persist($user);
            $manager->persist($this->getUser());
            $manager->flush();
            return $this->json(['message' => 'ok',
                'user1' => $user->getUsername(),
                'user2' => $this->getUser()->getUsername(),
                'requesterUsername' => $friendRequest->getRequester()->getUsername(),
                'requestedUsername' => $friendRequest->getRequested()->getUsername(),
                'action' => $friendRequest->getType(),

            ]);
        }

        return $this->json(['message' => 'no data']);
    }

    #[Route('/unfollow/user', name: 'unfollow_user')]
    public function unfollowUser(UserRepository $userRepository, FriendRequestRepository $friendRequestRepository, EntityManagerInterface $manager, \Symfony\Component\HttpFoundation\Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $data = json_decode($request->getContent(), true);
        if ($data) {
            $user = $userRepository->find($data['id']);
            if (!$user) {
                return $this->json(['message' => 'user not found']);
            }
            $user->removeFollower($this->getUser());
            $this->getUser()->removeFollowing($user);

            $manager->persist($user);
            $manager->persist($this->getUser());
            $manager->flush();
            return $this->json(['message' => 'ok',
            ]);
        }

        return $this->json(['message' => 'no data']);
    }

    #[Route('/followers/get', name: 'get_followers')]
    public function getFollowers(UserRepository $userRepository, FriendRequestRepository $friendRequestRepository, EntityManagerInterface $manager, \Symfony\Component\HttpFoundation\Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $data = json_decode($request->getContent(), true);
        if ($data) {
            $user = $userRepository->find($data['id']);
            if (!$user) {
                return $this->json(['message' => 'user not found']);
            }
            $userList = [];
            foreach ($user->getFollowers() as $follower) {
                $authorImageUrl = $follower->getImage() ? $follower->getImage()->getImageUrl() : 'images/imgProfil.png';
                $userList[] = ['image' => $authorImageUrl, 'id' => $follower->getId(), 'username' => $follower->getUsername()];
            }
            return $this->json($userList);
        }

        return $this->json(['message' => 'no data']);
    }

    #[Route('/followings/get', name: 'get_followings')]
    public function getFollowings(UserRepository $userRepository, FriendRequestRepository $friendRequestRepository, EntityManagerInterface $manager, \Symfony\Component\HttpFoundation\Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $data = json_decode($request->getContent(), true);
        if ($data) {
            $user = $userRepository->find($data['id']);
            if (!$user) {
                return $this->json(['message' => 'user not found']);
            }
            $userList = [];
            foreach ($user->getFollowings() as $follower) {
                $authorImageUrl = $follower->getImage() ? $follower->getImage()->getImageUrl() : 'images/imgProfil.png';
                $userList[] = ['image' => $authorImageUrl, 'id' => $follower->getId(), 'username' => $follower->getUsername()];
            }
            return $this->json($userList);
        }

        return $this->json(['message' => 'no data']);
    }

    #[Route('/friends/get', name: 'get_friends')]
    public function getFriends(UserRepository $userRepository, FriendRequestRepository $friendRequestRepository, EntityManagerInterface $manager, \Symfony\Component\HttpFoundation\Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $data = json_decode($request->getContent(), true);
        if ($data) {
            $user = $userRepository->find($data['id']);
            if (!$user) {
                return $this->json(['message' => 'user not found']);
            }
            $userList = [];
            foreach ($user->getFollowers() as $follower) {
                if ($follower->isFollowedBy($user)) {
                    $authorImageUrl = $follower->getImage() ? $follower->getImage()->getImageUrl() : 'images/imgProfil.png';
                    $userList[] = ['image' => $authorImageUrl, 'id' => $follower->getId(), 'username' => $follower->getUsername()];

                }
            }
            return $this->json($userList);
        }

        return $this->json(['message' => 'no data']);
    }

    #[Route('/request/get', name: 'get_request')]
    public function getRequest(FriendRequestRepository $repository, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['message' => 'no user']);
        }
        $requests = [];
        $n = 0;
        foreach ($repository->findBy(['requested' => $user], ['createdAt' => 'DESC']) as $request) {

            if ($n >= 10) {
                $manager->remove($request);
            } else {
                if ($request->isVisible() && ($request->getRequested() !== $request->getRequester())) {
                    $authorImageUrl = $request->getRequester()->getImage() ? $request->getRequester()->getImage()->getImageUrl() : 'images/imgProfil.png';
                    $requests[] = [
                        'requestId' => $request->getId(),
                        'requesterId' => $request->getRequester()->getId(),
                        'requesterUsername' => $request->getRequester()->getUsername(),
                        'requesterImageUrl' => $authorImageUrl,
                        'action' => $request->getType(),

                    ];
                }
            }
            $n += 1;
        }

        $manager->flush();
        return $this->json($requests);

    }


    #[
        Route('/request/delete', name: 'delete_request')]
    public function deleteRequest(Request $request, EntityManagerInterface $manager, FriendRequestRepository $friendRequestRepository): Response
    {
        if (!$this->getUser()) {
            return $this->json(['message' => 'no user']);
        }
        $data = json_decode($request->getContent(), true);
        if ($data) {
            $id = $data['id'];
            $friendRequest = $friendRequestRepository->find($id);
            if (!$friendRequest) {
                return $this->json(['message' => 'request not found']);
            }
            if ($friendRequest->getRequested() != $this->getUser()) {
                return $this->json(['message' => 'unknown']);
            }
            $friendRequest->setVisible(false);
            $manager->persist($friendRequest);
            $manager->flush();
            return $this->json(['message' => 'ok']);
        }

        return $this->json('no data');
    }

    #[Route('/stat/month/{month}/{year}', name: 'stat_month')]
    public function statsMonth($year, $month, Request $request, UserRepository $userRepository, FriendRequestRepository $repository): Response
    {
        if (!$this->getUser()) {
            return $this->json(['message' => 'no user']);
        }
        $data = json_decode($request->getContent(), true);
        if ($data) {
            $user = $userRepository->find($data['id']);
            if (!$user) {
                return $this->json(['message' => 'user not found']);
            }

            $month += 1;
            $date1 = new \DateTime("$year-$month-01");
            $date2 = clone $date1;
            $date2->modify('last day of this month');

            $stat = [];
            for ($i = 1; $i <= $date2->format('d'); $i++) {
                $firstDate = new \DateTimeImmutable("$year-$month-$i");
                $firstDate = $firstDate->setTime(0, 0, 0); // Débute à minuit
                $endDate = clone $firstDate;
                $endDate = $firstDate->setTime(23, 59, 59); // Va jusqu'à la fin de la journée

                $stat[] = $repository->findRequestsCreatedBetweenDates($firstDate, $endDate, $user);
            };

            return $this->json([
                'request' => $stat,

            ]);
        }
        return $this->json([
            'message' => 'no data',

        ]);
    }
}
