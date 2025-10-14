<?php

namespace App\EventListeners;

interface BatchQueueEventInterface
{
    const DATA_KEY_NAME = 'key';
    const DATA_CONTENT_NAME = 'content';
    const USER_BATCH_NOTIFICATION = 'batch.user.notification';

    public function getData(): string;
    public function getKey(): string;

}
