<?php

namespace App\Domain\Student\Entity;

use App\Domain\Enrollment\Entity\SubjectStudent;
use App\Domain\Subject\Entity\Subject;
use App\Infrastructure\Doctrine\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $revoked = null;

    /**
     * @var Collection<int, Subject>
     */
    #[ORM\ManyToMany(targetEntity: Subject::class, mappedBy: 'student')]
    private Collection $subjects;

    /**
     * @var Collection<int, SubjectStudent>
     */
    #[ORM\OneToMany(targetEntity: SubjectStudent::class, mappedBy: 'student', orphanRemoval: true)]
    private Collection $subjectStudents;

    public function __construct()
    {
        $this->subjects = new ArrayCollection();
        $this->subjectStudents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isRevoked(): ?bool
    {
        return $this->revoked;
    }

    public function setRevoked(bool $revoked): static
    {
        $this->revoked = $revoked;

        return $this;
    }

    /**
     * @return Collection<int, Subject>
     */
    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): static
    {
        if (!$this->subjects->contains($subject)) {
            $this->subjects->add($subject);
            $subject->addStudent($this);
        }

        return $this;
    }

    public function removeSubject(Subject $subject): static
    {
        if ($this->subjects->removeElement($subject)) {
            $subject->removeStudent($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, SubjectStudent>
     */
    public function getSubjectStudents(): Collection
    {
        return $this->subjectStudents;
    }

//    public function addSubjectStudent(SubjectStudent $subjectStudent): static
//    {
//        if (!$this->subjectStudents->contains($subjectStudent)) {
//            $this->subjectStudents->add($subjectStudent);
//            $subjectStudent->setStudent($this);
//        }
//
//        return $this;
//    }
//
//    public function removeSubjectStudent(SubjectStudent $subjectStudent): static
//    {
//        if ($this->subjectStudents->removeElement($subjectStudent)) {
//            // set the owning side to null (unless already changed)
//            if ($subjectStudent->getStudent() === $this) {
//                $subjectStudent->setStudent(null);
//            }
//        }
//
//        return $this;
//    }
}
