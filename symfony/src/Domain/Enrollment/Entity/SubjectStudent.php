<?php

namespace App\Domain\Enrollment\Entity;

use App\Domain\Student\Entity\Student;
use App\Domain\Subject\Entity\Subject;
use App\Infrastructure\Doctrine\Repository\SubjectStudentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubjectStudentRepository::class)]
#[ORM\Table(name: 'subject_student')]
class SubjectStudent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity:Subject::class, inversedBy: 'subjectStudents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subject $subject = null;

    #[ORM\ManyToOne(targetEntity:Student::class, inversedBy: 'subjectStudents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $expectedGrade = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $grade = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;

        return $this;
    }

    public function getExpectedGrade(): ?string
    {
        return $this->expectedGrade;
    }

    public function setExpectedGrade(?string $expectedGrade): static
    {
        $this->expectedGrade = $expectedGrade;

        return $this;
    }

    public function getGrade(): ?string
    {
        return $this->grade;
    }

    public function setGrade(?string $grade): static
    {
        $this->grade = $grade;

        return $this;
    }
}
