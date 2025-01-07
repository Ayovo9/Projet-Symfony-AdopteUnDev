<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250106221942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company_profile DROP created_at');
        $this->addSql('ALTER TABLE developer_profile CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE profile_view DROP FOREIGN KEY FK_4835241A54B4C416');
        $this->addSql('ALTER TABLE profile_view DROP FOREIGN KEY FK_4835241ACE4C79D5');
        $this->addSql('DROP INDEX IDX_4835241A54B4C416 ON profile_view');
        $this->addSql('DROP INDEX IDX_4835241ACE4C79D5 ON profile_view');
        $this->addSql('ALTER TABLE profile_view ADD company_id INT NOT NULL, ADD job_post_id INT NOT NULL, DROP viewer_developer_id, DROP viewer_company_id');
        $this->addSql('ALTER TABLE profile_view ADD CONSTRAINT FK_4835241A979B1AD6 FOREIGN KEY (company_id) REFERENCES company_profile (id)');
        $this->addSql('ALTER TABLE profile_view ADD CONSTRAINT FK_4835241AD166B4B7 FOREIGN KEY (job_post_id) REFERENCES job_post (id)');
        $this->addSql('CREATE INDEX IDX_4835241A979B1AD6 ON profile_view (company_id)');
        $this->addSql('CREATE INDEX IDX_4835241AD166B4B7 ON profile_view (job_post_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE profile_view DROP FOREIGN KEY FK_4835241A979B1AD6');
        $this->addSql('ALTER TABLE profile_view DROP FOREIGN KEY FK_4835241AD166B4B7');
        $this->addSql('DROP INDEX IDX_4835241A979B1AD6 ON profile_view');
        $this->addSql('DROP INDEX IDX_4835241AD166B4B7 ON profile_view');
        $this->addSql('ALTER TABLE profile_view ADD viewer_developer_id INT DEFAULT NULL, ADD viewer_company_id INT DEFAULT NULL, DROP company_id, DROP job_post_id');
        $this->addSql('ALTER TABLE profile_view ADD CONSTRAINT FK_4835241A54B4C416 FOREIGN KEY (viewer_company_id) REFERENCES company_profile (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE profile_view ADD CONSTRAINT FK_4835241ACE4C79D5 FOREIGN KEY (viewer_developer_id) REFERENCES developer_profile (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_4835241A54B4C416 ON profile_view (viewer_company_id)');
        $this->addSql('CREATE INDEX IDX_4835241ACE4C79D5 ON profile_view (viewer_developer_id)');
        $this->addSql('ALTER TABLE company_profile ADD created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE developer_profile CHANGE created_at created_at DATETIME DEFAULT NULL');
    }
}
