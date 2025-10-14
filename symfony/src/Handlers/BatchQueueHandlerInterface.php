<?php

namespace App\Handlers;

interface BatchQueueHandlerInterface
{
    public function handle(string $content, string $key);
}
