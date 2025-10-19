<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251017181007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE student (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(255) NOT NULL,
            revoked TINYINT(1) NOT NULL,
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql('CREATE TABLE subject (
            id INT AUTO_INCREMENT NOT NULL,
            teacher_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            INDEX IDX_FBCE3E7A41807E1D (teacher_id),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql('CREATE TABLE teacher (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(255) NOT NULL,
            salary INT NOT NULL,
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql('CREATE TABLE subject_student (
            subject_id INT NOT NULL,
            student_id INT NOT NULL,
            expected_grade INT,
            grade INT NOT NULL,
            INDEX IDX_12A1039123EDC87 (subject_id),
            INDEX IDX_12A10391CB944F1A (student_id),
            PRIMARY KEY(subject_id, student_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7A41807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
        $this->addSql('ALTER TABLE subject_student ADD CONSTRAINT FK_12A1039123EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subject_student ADD CONSTRAINT FK_12A10391CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7A41807E1D');
        $this->addSql('ALTER TABLE subject_student DROP FOREIGN KEY FK_12A1039123EDC87');
        $this->addSql('ALTER TABLE subject_student DROP FOREIGN KEY FK_12A10391CB944F1A');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE subject');
        $this->addSql('DROP TABLE subject_student');
        $this->addSql('DROP TABLE teacher');
    }
}
