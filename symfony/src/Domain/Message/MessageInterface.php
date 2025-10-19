<?php

namespace App\Domain\Message;

interface MessageInterface
{
    public function getName(): string;
    public function getType(): string;
}
