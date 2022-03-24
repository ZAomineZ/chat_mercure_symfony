<?php

namespace App\Security\Voter;

use App\Entity\Conversation;
use App\Entity\User;
use App\Repository\ConversationRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class ConversationVoter extends Voter
{
    const VIEW = 'view';

    /**
     * @param ConversationRepository $conversationRepository
     */
    public function __construct(
        private ConversationRepository $conversationRepository
    )
    {
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute == self::VIEW && $subject instanceof Conversation;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     * @throws NonUniqueResultException
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        $result = $this->conversationRepository->checkIfUserIsParticipant(
            $subject->getId(),
            $user->getId()
        );
        return !!$result;
    }
}
