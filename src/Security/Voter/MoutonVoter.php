<?php

namespace App\Security\Voter;

use App\Entity\Mouton;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MoutonVoter extends Voter
{
    public const VIEW = 'MOUTON_VIEW';
    public const EDIT = 'MOUTON_EDIT';
    public const DELETE = 'MOUTON_DELETE';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE], true) && $subject instanceof Mouton;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return $this->security->isGranted('ROLE_USER');
    }
}
