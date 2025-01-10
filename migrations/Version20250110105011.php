<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250110105011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company_profile CHANGE logo logo VARCHAR(255) DEFAULT NULL, CHANGE industry industry VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE developer_profile CHANGE programming_languages programming_languages JSON NOT NULL, CHANGE avatar avatar VARCHAR(255) DEFAULT NULL, CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE github github VARCHAR(255) DEFAULT NULL, CHANGE linkedin linkedin VARCHAR(255) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE job_application ADD motivation_letter LONGTEXT NOT NULL, ADD cv_filename VARCHAR(255) DEFAULT NULL, ADD feedback LONGTEXT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, CHANGE status status VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE job_post CHANGE programming_languages programming_languages JSON NOT NULL, CHANGE last_match_check_at last_match_check_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE notification CHANGE data data JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE search_history CHANGE filters filters JSON NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE last_login_at last_login_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company_profile CHANGE logo logo VARCHAR(255) DEFAULT \'NULL\', CHANGE industry industry VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE developer_profile CHANGE programming_languages programming_languages LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE avatar avatar VARCHAR(255) DEFAULT \'NULL\', CHANGE title title VARCHAR(255) DEFAULT \'NULL\', CHANGE github github VARCHAR(255) DEFAULT \'NULL\', CHANGE linkedin linkedin VARCHAR(255) DEFAULT \'NULL\', CHANGE created_at created_at DATETIME DEFAULT \'current_timestamp()\' NOT NULL');
        $this->addSql('ALTER TABLE job_application DROP motivation_letter, DROP cv_filename, DROP feedback, DROP updated_at, CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE job_post CHANGE programming_languages programming_languages LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_match_check_at last_match_check_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE notification CHANGE data data LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE search_history CHANGE filters filters LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE `user` CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_login_at last_login_at DATETIME DEFAULT \'NULL\'');
    }
}
