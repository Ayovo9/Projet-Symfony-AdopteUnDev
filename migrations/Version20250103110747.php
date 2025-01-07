<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250103110747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE matches (id INT AUTO_INCREMENT NOT NULL, match_score NUMERIC(5, 2) NOT NULL, created_at DATETIME NOT NULL, is_viewed TINYINT(1) NOT NULL, is_applied TINYINT(1) NOT NULL, is_saved TINYINT(1) NOT NULL, developer_id INT NOT NULL, job_post_id INT NOT NULL, INDEX IDX_62615BA64DD9267 (developer_id), INDEX IDX_62615BAD166B4B7 (job_post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE matches ADD CONSTRAINT FK_62615BA64DD9267 FOREIGN KEY (developer_id) REFERENCES developer_profile (id)');
        $this->addSql('ALTER TABLE matches ADD CONSTRAINT FK_62615BAD166B4B7 FOREIGN KEY (job_post_id) REFERENCES job_post (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE matches DROP FOREIGN KEY FK_62615BA64DD9267');
        $this->addSql('ALTER TABLE matches DROP FOREIGN KEY FK_62615BAD166B4B7');
        $this->addSql('DROP TABLE matches');
    }
}
