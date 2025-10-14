<?php

namespace App\Service\Queue;



use App\EventListeners\BatchQueueEventInterface;
use App\Handlers\BatchQueueHandlerManger;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class BatchQueueService
{
    const BATCH_EXCHANGE_NAME = 'batch_exchange';
    const BATCH_QUEUE_NAME = 'batch';
    const BATCH_QUEUE_CONSUME_ROUTING_KEY = 'batch.*.*';
    const QUEUE_LIFE_TIME_IN_SECOND = 60;

    private ?AMQPStreamConnection $connection = null;


    public function __construct(
        private readonly string $host,
        private readonly string $port,
        private readonly string $user,
        private readonly string $password,
        private readonly LoggerInterface $logger,
        private readonly BatchQueueHandlerManger $handlerManager
    ){}

    public function publish(BatchQueueEventInterface $event): void
    {
        $routingKey = $event->getKey();
        $eventData  = $event->getData();
        $queueName  = $this->createQueueName($routingKey);

        try {
            if (null === $this->connection) {
                $this->connect();
            }
            $channel = $this->connection->channel();
            $msg = new AMQPMessage($eventData);
            $this->logger->debug("Publishing message with routing key: {$routingKey}");

            // Declare and bind the queue
            $this->queueDeclareAndBind($channel, $queueName, $routingKey);

            $channel->basic_publish($msg, self::BATCH_EXCHANGE_NAME, $routingKey);
            $this->logger->debug("Published batch queue message {$routingKey}", ['data' => $eventData]);

        } catch (\Exception $e){
            $this->logger->error("Batch queue publish error {$routingKey}:", ['error' => $e->getMessage()]);
        }
    }

    public function consume($routingKey): void
    {
        $queueName = $this->createQueueName($routingKey);

        if($queueName) {
            try {
                if (null === $this->connection) {
                    $this->connect();
                }

                $channel = $this->connection->channel();

                // Declare and bind the queue
                $this->queueDeclareAndBind($channel, $queueName, $routingKey);

                $this->basicConsume($channel, $queueName, [$this, 'process']);

                $this->logger->debug("Waiting for incoming messages in queue: " . $routingKey);

                $start = time();
                $elapsedTime = 0;

                while (count($channel->callbacks) && $elapsedTime < self::QUEUE_LIFE_TIME_IN_SECOND) {
                    try {
                        $channel->wait(null, false, self::QUEUE_LIFE_TIME_IN_SECOND);
                    } catch (AMQPTimeoutException $e) {
                        $this->logger->debug(
                            'Timed out waiting for incoming data after ' . self::QUEUE_LIFE_TIME_IN_SECOND . 's'
                        );
                    }

                    $elapsedTime = time() - $start;
                }

                $this->close();
            } catch (\Exception $e) {
                $this->logger->error("Queue message consume error {$routingKey}:", ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * @param AMQPMessage $msg
     * @return void
     */
    public function process(AMQPMessage $msg): void
    {
        $this->logger->debug('Receive message from Queue', [
            'body' => $msg->getBody(),
            'routing_key' => $msg->getRoutingKey()
        ]);

        $this->handlerManager->handle($msg);

        // Acknowledge the message
        $msg->getChannel()->basic_ack($msg->getDeliveryTag());
    }


    /**
     * Connect to AMQ
     * @throws \Exception
     * @return void
     */
    protected function connect(): void
    {
        $this->connection = new AMQPStreamConnection($this->host, $this->port, $this->user, $this->password);
        $channel = $this->connection->channel();
        $this->exchangeDeclare($channel);
    }

    /**
     * Close connection
     * @throws \Exception
     * @return void
     */
    protected function close(): void
    {
        if (null === $this->connection) {
            return;
        }

        $channel = $this->connection->channel();
        $channel->close();
        $this->connection->close();
    }

    /**
     * Declare an Exchange
     * Exchanges are AMQP entities where messages are sent.
     * Exchanges take a message and route it into zero or more queues.
     *
     * @param AMQPChannel $channel
     * @return void
     */
    protected function exchangeDeclare(AMQPChannel $channel): void
    {
        $this->logger->debug('Exchange declare');
        $channel->exchange_declare(
            self::BATCH_EXCHANGE_NAME,
            'topic',
            false,
            true,
            false,
            false,
            false
        );
    }

    /**
     * @param AMQPChannel $channel
     * @param string $queueName
     * @param string $routingKey
     * @return void
     */
    protected function queueDeclareAndBind(AMQPChannel $channel, string $queueName, string $routingKey): void
    {
        // Declare the queue
        $channel->queue_declare(
            $queueName,  // queue name
            false,       // passive
            true,        // durable
            false,       // exclusive
            false        // auto delete
        );

        // Bind the queue to the exchange with the routing key
        $channel->queue_bind(
            $queueName,   // queue name
            self::BATCH_EXCHANGE_NAME,      // exchange name
            $routingKey   // routing key
        );
    }

    /**
     * @param AMQPChannel $channel
     * @param string $queueName
     * @param mixed $callback
     * @return void
     */
    protected function basicConsume(AMQPChannel $channel, string $queueName, $callback): void
    {
        $this->logger->debug('Basic consume');
        $channel->basic_consume($queueName, '', false, false, false, false, $callback);
    }

    /**
     * @param string $routingKey
     * @return string|null
     */
    protected function createQueueName(string $routingKey): ?string
    {
        switch($routingKey) {

            case self::BATCH_QUEUE_CONSUME_ROUTING_KEY:
            case BatchQueueEventInterface::USER_BATCH_NOTIFICATION:
                return self::BATCH_QUEUE_NAME;
            default:
                $this->logger->error("Invalid batch queue name!", ['routingKey' => $routingKey]);
                return null;
        }
    }
}
