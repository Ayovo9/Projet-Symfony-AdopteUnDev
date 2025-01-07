<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250103101804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company_profile (id INT AUTO_INCREMENT NOT NULL, company_name VARCHAR(255) NOT NULL, logo VARCHAR(255) DEFAULT NULL, location VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, industry VARCHAR(255) DEFAULT NULL, company_size INT DEFAULT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_A105B0D8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE company_profile ADD CONSTRAINT FK_A105B0D8A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company_profile DROP FOREIGN KEY FK_A105B0D8A76ED395');
        $this->addSql('DROP TABLE company_profile');
    }
}
