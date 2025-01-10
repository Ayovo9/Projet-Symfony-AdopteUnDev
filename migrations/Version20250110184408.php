<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250110184408 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company_profile DROP created_at');
        $this->addSql('ALTER TABLE developer_profile CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE view_count view_count INT NOT NULL');
        
        // Recreate job_application table with correct structure
        $this->addSql('DROP TABLE IF EXISTS job_application');
        $this->addSql('CREATE TABLE job_application (
            id INT AUTO_INCREMENT NOT NULL,
            job_post_id INT NOT NULL,
            developer_id INT NOT NULL,
            motivation_letter LONGTEXT NOT NULL,
            cv_filename VARCHAR(255) DEFAULT NULL,
            status VARCHAR(20) NOT NULL,
            feedback LONGTEXT DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            INDEX IDX_C737C6883481D195 (job_post_id),
            INDEX IDX_C737C68864DD9267 (developer_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        $this->addSql('ALTER TABLE job_application ADD CONSTRAINT FK_C737C6883481D195 FOREIGN KEY (job_post_id) REFERENCES job_post (id)');
        $this->addSql('ALTER TABLE job_application ADD CONSTRAINT FK_C737C68864DD9267 FOREIGN KEY (developer_id) REFERENCES developer_profile (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE IF EXISTS job_application');
        $this->addSql('ALTER TABLE company_profile ADD created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE developer_profile CHANGE view_count view_count INT DEFAULT 0, CHANGE created_at created_at DATETIME DEFAULT NULL');
    }
}
