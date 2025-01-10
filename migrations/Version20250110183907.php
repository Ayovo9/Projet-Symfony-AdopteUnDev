<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250110183907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) NOT NULL, message VARCHAR(255) NOT NULL, data JSON DEFAULT NULL, is_read TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE search_history (id INT AUTO_INCREMENT NOT NULL, search_query VARCHAR(255) NOT NULL, filters JSON NOT NULL, searched_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_AA6B9FD1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE search_history ADD CONSTRAINT FK_AA6B9FD1A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE company_profile DROP created_at');
        $this->addSql('ALTER TABLE developer_profile CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE view_count view_count INT NOT NULL');
        $this->addSql('ALTER TABLE job_application ADD motivation_letter LONGTEXT NOT NULL, ADD cv_filename VARCHAR(255) DEFAULT NULL, ADD feedback LONGTEXT DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, DROP applied_at, DROP message');
        $this->addSql('ALTER TABLE job_application RENAME INDEX idx_c737c6883481d195 TO IDX_C737C688D166B4B7');
        $this->addSql('ALTER TABLE job_post ADD last_match_check_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD last_login_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE search_history DROP FOREIGN KEY FK_AA6B9FD1A76ED395');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE search_history');
        $this->addSql('ALTER TABLE job_application ADD applied_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD message VARCHAR(1000) DEFAULT NULL, DROP motivation_letter, DROP cv_filename, DROP feedback, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE job_application RENAME INDEX idx_c737c688d166b4b7 TO IDX_C737C6883481D195');
        $this->addSql('ALTER TABLE job_post DROP last_match_check_at');
        $this->addSql('ALTER TABLE developer_profile CHANGE view_count view_count INT DEFAULT 0, CHANGE created_at created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` DROP last_login_at');
        $this->addSql('ALTER TABLE company_profile ADD created_at DATETIME DEFAULT NULL');
    }
}
