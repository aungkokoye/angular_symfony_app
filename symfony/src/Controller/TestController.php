<?php

namespace App\Controller;

use App\Application\Teacher\Handler\TeacherManager;
use App\Domain\Student\Entity\Student;
use App\Domain\Student\Message\StudentMessage;
use App\Domain\Teacher\Entity\Teacher;
use App\EventListeners\BatchQueueEvent;
use App\Infrastructure\MessagePublisher\StudentMessagePublisher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class TestController extends AbstractController
{
    /**
     * @throws ExceptionInterface
     */
    #[Route('/test/messenger', name: 'app_test_messenger')]
    public function messenger(EntityManagerInterface $em, StudentMessagePublisher $publisher): Response
    {
        $reflection = new \ReflectionClass(Student::class);
        $shortName = strtolower($reflection->getShortName());
        $message = new StudentMessage($shortName, 'welcome',5);
        $publisher->publishNotification($shortName, 'welcome', $message);

        return $this->json([
            'message' => 'Test endpoint is working!',
            'status' => 'success',
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/notification', name: 'app_notification')]
    public function notification(MailerInterface $mailer): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $email = (new Email())
            ->from('hello@example.com')
            ->to('sender@example.com')
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);

        return $this->json([
            'message' => 'Test Symfony mailer sending email endpoint is working!',
            'status' => 'success',
        ]);
    }

    #[Route('/batch-notification', name: 'app_batch_notification')]
    public function batchNotification(EventDispatcherInterface $dispatcher): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $event = new BatchQueueEvent('This is a test batch notification', BatchQueueEvent::NAME);
        $dispatcher->dispatch($event, BatchQueueEvent::NAME);

        return $this->json([
            'message' => 'Test Symfony batch notification is working!',
            'status' => 'success',
        ]);
    }

    /**
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    #[Route('/test/teacher', name: 'app_test_teacher', methods: ['POST'])]
    public function testTeacher(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        TeacherManager $teacherManager,
    ): Response
    {
        $content = $request->getContent();
        $dto = $serializer->deserialize($content, Teacher::class, 'json');
        [$violationsErrors, $teacher] = $teacherManager->createTeacher($dto);

        if (!empty($violationsErrors)) {
            return $this->json($violationsErrors, Response::HTTP_BAD_REQUEST);
        }

        return $this->json( $teacher, Response::HTTP_OK);
    }
}
