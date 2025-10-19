<?php

namespace App\Domain\Student\Message;

use App\Domain\Message\MessageInterface;

class StudentMessage implements MessageInterface
{
    public function __construct(
        private readonly string $name,
        private readonly string $type,
        private readonly int $studentId,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getStudentId(): int
    {
        return $this->studentId;
    }
}
