<?php

namespace App\Domain\Subject\Entity;

use App\Domain\Enrollment\Entity\SubjectStudent;
use App\Domain\Student\Entity\Student;
use App\Domain\Teacher\Entity\Teacher;
use App\Infrastructure\Doctrine\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: Teacher::class, inversedBy: 'subjects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?teacher $teacher = null;

    /**
     * @var Collection<int, Student>
     */
    #[ORM\ManyToMany(targetEntity: Student::class, inversedBy: 'subjects')]
    private Collection $students;

    /**
     * @var Collection<int, SubjectStudent>
     */
    #[ORM\OneToMany(targetEntity: SubjectStudent::class, mappedBy: 'subject', orphanRemoval: true)]
    private Collection $subjectStudents;

    public function __construct()
    {
        $this->students = new ArrayCollection();
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

    public function getTeacher(): ?teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?teacher $teacher): static
    {
        $this->teacher = $teacher;

        return $this;
    }

    /**
     * @return Collection<int, student>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(student $student): static
    {
        if (!$this->students->contains($student)) {
            $this->students->add($student);
        }

        return $this;
    }

    public function removeStudent(student $student): static
    {
        $this->students->removeElement($student);

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
//            $subjectStudent->setSubject($this);
//        }
//
//        return $this;
//    }
//
//    public function removeSubjectStudent(SubjectStudent $subjectStudent): static
//    {
//        if ($this->subjectStudents->removeElement($subjectStudent)) {
//            // set the owning side to null (unless already changed)
//            if ($subjectStudent->getSubject() === $this) {
//                $subjectStudent->setSubject(null);
//            }
//        }
//
//        return $this;
//    }
}
