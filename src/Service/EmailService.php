<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class EmailService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validateEmailFormatAndUniqueness($value, ExecutionContextInterface $context): void
    {
        if (false === strpos($value, '@')) {
            $context->buildViolation('L\'adresse e-mail doit contenir un @.')
                ->addViolation();
        }

        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $value]);
        if ($existingUser) {
            $context->buildViolation('Cette adresse e-mail est déjà utilisée.')
                ->addViolation();
        }
    }
}
