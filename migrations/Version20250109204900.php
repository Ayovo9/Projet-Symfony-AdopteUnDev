<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250109204900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add created_at column to search_history table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE search_history CHANGE searched_at created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE search_history CHANGE created_at searched_at DATETIME NOT NULL');
    }
}
