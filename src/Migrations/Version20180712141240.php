<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180712141240 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE teacher_student');
        $this->addSql('ALTER TABLE student_teacher DROP FOREIGN KEY FK_5DC0132D41807E1D');
        $this->addSql('ALTER TABLE student_teacher DROP FOREIGN KEY FK_5DC0132DCB944F1A');
        $this->addSql('ALTER TABLE student_teacher ADD CONSTRAINT FK_5DC0132D41807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_teacher ADD CONSTRAINT FK_5DC0132DCB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE teacher_student (teacher_id INT NOT NULL, student_id INT NOT NULL, INDEX IDX_7AE1227241807E1D (teacher_id), INDEX IDX_7AE12272CB944F1A (student_id), PRIMARY KEY(teacher_id, student_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE teacher_student ADD CONSTRAINT FK_7AE1227241807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teacher_student ADD CONSTRAINT FK_7AE12272CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_teacher DROP FOREIGN KEY FK_5DC0132DCB944F1A');
        $this->addSql('ALTER TABLE student_teacher DROP FOREIGN KEY FK_5DC0132D41807E1D');
        $this->addSql('ALTER TABLE student_teacher ADD CONSTRAINT FK_5DC0132DCB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE student_teacher ADD CONSTRAINT FK_5DC0132D41807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
    }
}
