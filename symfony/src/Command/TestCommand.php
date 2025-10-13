<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:worker',
    description: 'This command simulates a long-running worker process.
    This is useful for testing purposes, such as simulating a worker that processes tasks in the background(supervisord).
    The worker will run for a maximum of 60 seconds, outputting the current time every 5 seconds until it stops.
    You can stop the worker by pressing Ctrl+C.
    You can see the output inside the symfony_apache container running this command:
        docker exec -it symfony_apache tail -f /var/log/supervisor/worker.log',
)]
class TestCommand extends Command
{

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = new \DateTimeImmutable();

        $output->writeln('Worker started. Press Ctrl+C to stop.');

        while (true) {
            $output->writeln('Working... ' . date('Y-m-d H:i:s'));
            // Example: wait 5 seconds between iterations
            sleep(5);

            $now = new \DateTimeImmutable();
            $elapsed = $now->getTimestamp() - $start->getTimestamp();

            $output->writeln('Elapsed: ' . $elapsed . ' second/s');

            // Check if the command has been running for more than 60 seconds
            if ($elapsed >= 60) {
                break;
            }
        }

        $output->writeln('Worker stop! ' . date('Y-m-d H:i:s'));

        return Command::SUCCESS;
    }
}
