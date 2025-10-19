<?php

namespace App\Controller;

use App\Domain\Enrollment\Entity\SubjectStudent;
use App\Domain\Student\Entity\Student;
use App\Domain\Subject\Entity\Subject;
use App\Domain\Teacher\Entity\Teacher;
use App\EventListeners\BatchQueueEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class TestController extends AbstractController
{
    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/test', name: 'app_test')]
    public function index(EntityManagerInterface $em): Response
    {
        $student = new Student();
        $student->setName('Test Student')->setRevoked(false);
        $em->persist($student);

        $teacher = new Teacher();
        $teacher->setName('Test Teacher')->setSalary(100000);
        $em->persist($teacher);

        $subject = new Subject();
        $subject->setName('Test Subject')->setTeacher($teacher);
        $em->persist($subject);

        $enrollment = new SubjectStudent();
        $enrollment->setStudent($student)
            ->setSubject($subject)
            ->setExpectedGrade(1)
            ->setGrade(1)
        ;
        $em->persist($enrollment);
        $em->flush();

        return $this->json([
            'message' => 'Test endpoint is working!',
            'status' => 'success',
            'student'=> $student->getName(),
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
}
