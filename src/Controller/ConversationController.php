<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Participant;
use App\Entity\User;
use App\Repository\ConversationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Discovery;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\WebLink\Link;

#[Route('/conversations', name: 'conversations.')]
final class ConversationController extends AbstractController
{
    /**
     * @param UserRepository $userRepository
     * @param ConversationRepository $conversationRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private UserRepository         $userRepository,
        private ConversationRepository $conversationRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\DBAL\Exception
     * @throws Exception
     */
    #[Route('/{id}', name: 'index', methods: ["POST"])]
    public function index(Request $request): Response
    {
        $otherUser = $request->get('otherUser', 0);
        $otherUser = $this->userRepository->find($otherUser);

        if (is_null($otherUser)) {
            throw new Exception('The user was not found');
        }

        // Cannot create a conversation with myself
        /** @var User $user */
        $user = $this->getUser();
        if ($otherUser->getId() === $user->getId()) {
            throw new Exception('Cannot create a conversation with yourself');
        }

        // Check if the conversation exist
        $conversation = $this->conversationRepository->findConversationByParticipants(
            $otherUser->getId(),
            $user->getId()
        );

        if (count($conversation) !== 0) {
            throw new Exception('The conversation already exists');
        }

        $conversation = new Conversation();

        $participant = new Participant();
        $participant->setUser($user);
        $participant->setConversation($conversation);

        $otherParticipant = new Participant();
        $otherParticipant->setUser($otherUser);
        $otherParticipant->setConversation($conversation);

        $this->entityManager->getConnection()->beginTransaction();
        try {
            $this->entityManager->persist($conversation);
            $this->entityManager->persist($participant);
            $this->entityManager->persist($otherParticipant);

            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Exception $e) {
            $this->entityManager->rollBack();

            throw $e;
        }

        return $this->json([
            'id' => $conversation->getId()
        ], Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param Discovery $discovery
     * @return JsonResponse
     */
    #[Route("/", name: "getConversations", methods: ["GET"])]
    public function getConversations(Request $request, Discovery $discovery): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $conversations = $this->conversationRepository->findConversationByUser($user->getId());

        $discovery->addLink($request);
        return $this->json($conversations, Response::HTTP_OK);
    }

}
