<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\ParticipantRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/messages', name: 'messages.')]
final class MessageController extends AbstractController
{
    const ATTRIBUTES_TO_SERIALIZE = ['id', 'content', 'createdAt', 'mine'];

    /**
     * @param EntityManagerInterface $entityManager
     * @param MessageRepository $messageRepository
     * @param UserRepository $userRepository
     * @param ParticipantRepository $participantRepository
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageRepository      $messageRepository,
        private UserRepository         $userRepository,
        private ParticipantRepository  $participantRepository,
    )
    {
    }

    /**
     * @param Request $request
     * @param Conversation $conversation
     * @return Response
     */
    #[Route("/{id}", name: "getMessage", requirements: ['id' => '^\d+(?:-\d+)?$'], methods: ['GET'])]
    public function index(Request $request, Conversation $conversation): Response
    {
        // Can view the conversation
        $this->denyAccessUnlessGranted('view', $conversation);

        /** @var User $user */
        $user = $this->getUser();

        $messages = $this->messageRepository->findMessagesByConversationId($conversation->getId());

        /**
         * @var Message $message
         */
        array_map(function (Message $message) use ($user) {
            $message->setMine(
                $message->getUser()->getId() === $user->getId()
            );
        }, $messages);

        return $this->json($messages, Response::HTTP_OK, [], [
            'attributes' => self::ATTRIBUTES_TO_SERIALIZE
        ]);
    }

    /**
     * @param Request $request
     * @param Conversation $conversation
     * @param SerializerInterface $serializer
     * @param HubInterface $hub
     * @return JsonResponse
     * @throws Exception
     * @throws NonUniqueResultException
     */
    #[Route("/create/{id}", name: "createMessage", requirements: ['id' => '^\d+(?:-\d+)?$'], methods: ['POST'])]
    public function create(
        Request             $request,
        Conversation        $conversation,
        SerializerInterface $serializer,
        HubInterface $hub
    ): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $recipient = $this->participantRepository->findParticipantByConversationId($conversation->getId(), $user->getId());

        $content = $request->get('content', null);

        $message = new Message();
        $message->setContent($content);
        $message->setUser($user);

        $conversation->addMessage($message);
        $conversation->setLastMessage($message);

        $this->entityManager->getConnection()->beginTransaction();
        try {
            $this->entityManager->persist($message);
            $this->entityManager->persist($conversation);
            $this->entityManager->flush();

            $this->entityManager->commit();
        } catch (Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
        $message->setMine(false);
        $messageSerialized = $serializer->serialize($message, 'json', [
            'attributes' => array_merge(self::ATTRIBUTES_TO_SERIALIZE, ['conversation' => ['id']])
        ]);

        $update = new Update(
            [
                sprintf('/conversations/%s/%s', $conversation->getId(), $recipient->getUser()->getUsername()),
                sprintf('/conversations/%s', $recipient->getUser()->getUsername())
            ], $messageSerialized
        );

        $hub->publish($update);

        $message->setMine(true);

        return $this->json($message, Response::HTTP_CREATED, [], [
            'attributes' => self::ATTRIBUTES_TO_SERIALIZE
        ]);
    }
}
