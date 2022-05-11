<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220511114921 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE quiz_skills (quiz_id INT NOT NULL, skills_id INT NOT NULL, INDEX IDX_3875E629853CD175 (quiz_id), INDEX IDX_3875E6297FF61858 (skills_id), PRIMARY KEY(quiz_id, skills_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE quiz_skills ADD CONSTRAINT FK_3875E629853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quiz_skills ADD CONSTRAINT FK_3875E6297FF61858 FOREIGN KEY (skills_id) REFERENCES skills (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contrat CHANGE date_fin date_fin DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE tasks CHANGE priorite priorite VARCHAR(255) DEFAULT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE fullname fullname VARCHAR(255) DEFAULT NULL, CHANGE role role JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE quiz_skills');
        $this->addSql('ALTER TABLE contrat CHANGE status status VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE duree duree VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE date_fin date_fin DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE `leave` CHANGE status status VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE leave_type CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE projects CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE duree duree VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE status status VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE question CHANGE title title VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE quiz CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE nombrequestion nombrequestion VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE reponse CHANGE title title VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE reset_password_request CHANGE selector selector VARCHAR(20) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE hashed_token hashed_token VARCHAR(100) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE skills CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE sprint CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE duree duree VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE status status VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE suivi_leave CHANGE annees annees VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE mois mois VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE tasks CHANGE task_name task_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE duree duree VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE status status VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE priorite priorite VARCHAR(255) DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE description description VARCHAR(255) DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE type_contrat CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE role role LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE fullname fullname VARCHAR(255) DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE firstname firstname VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE lastname lastname VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE image image VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
