<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250110200756 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE developer_profile CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE view_count view_count INT NOT NULL');
        $this->addSql('ALTER TABLE profile_view DROP FOREIGN KEY profile_view_ibfk_3');
        $this->addSql('ALTER TABLE profile_view CHANGE job_post_id job_post_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE profile_view ADD CONSTRAINT FK_4835241AD166B4B7 FOREIGN KEY (job_post_id) REFERENCES job_post (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE developer_profile CHANGE view_count view_count INT DEFAULT 0, CHANGE created_at created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE profile_view DROP FOREIGN KEY FK_4835241AD166B4B7');
        $this->addSql('ALTER TABLE profile_view CHANGE job_post_id job_post_id INT NOT NULL');
        $this->addSql('ALTER TABLE profile_view ADD CONSTRAINT profile_view_ibfk_3 FOREIGN KEY (job_post_id) REFERENCES job_post (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
