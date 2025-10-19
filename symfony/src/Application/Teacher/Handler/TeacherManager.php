<?php

namespace App\Application\Teacher\Handler;

use App\Application\Handler\BaseEntityManger;
use App\Domain\Teacher\Entity\Teacher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TeacherManager extends BaseEntityManger
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ValidatorInterface $validator,
    ){}

    public function createTeacher(Teacher $teacher): array
    {
        $violations = $this->validator->validate($teacher);
        $violationsErrors = $this->getViolations($violations);
        if (empty($violationsErrors)) {
            $this->em->persist($teacher);
            $this->em->flush();
        }

        return [$violationsErrors, $teacher];
    }
}
