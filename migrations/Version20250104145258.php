<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250104145258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE developer_rating (id INT AUTO_INCREMENT NOT NULL, rating INT NOT NULL, created_at DATETIME NOT NULL, rated_developer_id INT NOT NULL, rating_developer_id INT NOT NULL, INDEX IDX_2D696AB581891E43 (rated_developer_id), INDEX IDX_2D696AB55AC2B4CF (rating_developer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE developer_rating ADD CONSTRAINT FK_2D696AB581891E43 FOREIGN KEY (rated_developer_id) REFERENCES developer_profile (id)');
        $this->addSql('ALTER TABLE developer_rating ADD CONSTRAINT FK_2D696AB55AC2B4CF FOREIGN KEY (rating_developer_id) REFERENCES developer_profile (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE developer_rating DROP FOREIGN KEY FK_2D696AB581891E43');
        $this->addSql('ALTER TABLE developer_rating DROP FOREIGN KEY FK_2D696AB55AC2B4CF');
        $this->addSql('DROP TABLE developer_rating');
    }
}
