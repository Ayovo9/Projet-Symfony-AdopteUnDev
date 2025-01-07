<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250103143909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE profile_view (id INT AUTO_INCREMENT NOT NULL, viewed_at DATETIME NOT NULL, developer_id INT NOT NULL, company_id INT NOT NULL, job_post_id INT NOT NULL, INDEX IDX_4835241A64DD9267 (developer_id), INDEX IDX_4835241A979B1AD6 (company_id), INDEX IDX_4835241AD166B4B7 (job_post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE profile_view ADD CONSTRAINT FK_4835241A64DD9267 FOREIGN KEY (developer_id) REFERENCES developer_profile (id)');
        $this->addSql('ALTER TABLE profile_view ADD CONSTRAINT FK_4835241A979B1AD6 FOREIGN KEY (company_id) REFERENCES company_profile (id)');
        $this->addSql('ALTER TABLE profile_view ADD CONSTRAINT FK_4835241AD166B4B7 FOREIGN KEY (job_post_id) REFERENCES job_post (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE profile_view DROP FOREIGN KEY FK_4835241A64DD9267');
        $this->addSql('ALTER TABLE profile_view DROP FOREIGN KEY FK_4835241A979B1AD6');
        $this->addSql('ALTER TABLE profile_view DROP FOREIGN KEY FK_4835241AD166B4B7');
        $this->addSql('DROP TABLE profile_view');
    }
}
