<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\NonUniqueResultException;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\UserService;
use eCamp\Core\Repository\CampCollaborationRepository;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class InvitationService {
    private AuthenticationService $authenticationService;
    private CampCollaborationRepository $campCollaborationRepository;
    private UserService $userService;

    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService, UserService $userService) {
        $this->authenticationService = $authenticationService;
        /** @var CampCollaborationRepository $campCollaborationRepository */
        $campCollaborationRepository = $serviceUtils->emGetRepository(CampCollaboration::class);
        $this->campCollaborationRepository = $campCollaborationRepository;
        $this->userService = $userService;
    }

    public function findInvitation(string $inviteKey): ?CampCollaboration {
        return $this->campCollaborationRepository->findByInviteKey($inviteKey);
    }

    /**
     * @throws EntityNotFoundException
     * @throws NonUniqueResultException
     * @throws NoAccessException
     */
    public function acceptInvitation(string $inviteKey, string $userId): CampCollaboration {
        $campCollaboration = $this->findInvitation($inviteKey);
        if (null == $campCollaboration) {
            throw new EntityNotFoundException();
        }
        /** @var User $user */
        $user = $this->userService->fetch($userId);
        if (null == $user) {
            throw new EntityNotFoundException();
        }
        $campCollaboration->setUser($user);
        $campCollaboration->setStatus(CampCollaboration::STATUS_ESTABLISHED);
        $campCollaboration->setInviteKey(null);
        $campCollaboration->setInviteEmail(null);
        $this->campCollaborationRepository->saveWithoutAcl($campCollaboration);

        return $campCollaboration;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function rejectInvitation(string $inviteKey): CampCollaboration {
        $campCollaboration = $this->findInvitation($inviteKey);
        if (null == $campCollaboration) {
            throw new EntityNotFoundException();
        }
        $campCollaboration->setStatus(CampCollaboration::STATUS_LEFT);
        $campCollaboration->setInviteKey(null);
        $this->campCollaborationRepository->saveWithoutAcl($campCollaboration);

        return $campCollaboration;
    }
}
