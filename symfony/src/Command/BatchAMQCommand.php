<?php

namespace App\Command;

use App\Service\Queue\BatchQueueService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:batch-amq:consume',
    description: 'Consume messages from a batch AMQ queue. Example command: bin/console app:batch-amq:consume batch.*.*',
)]
class BatchAMQCommand extends Command
{
    public function __construct(private BatchQueueService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'routing_key',
                InputArgument::REQUIRED,
                'Batch AMQ routing key <batch.*.*>'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $routingKey = $input->getArgument('routing_key');
        $output->writeln("Start command to consume the batch queue (routing_key: {$routingKey}).");

        $this->service->consume($routingKey);

        $output->writeln("End command to consume the batch queue (routing_key: {$routingKey}).");

        return Command::SUCCESS;
    }
}
