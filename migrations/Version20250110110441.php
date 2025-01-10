<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250110110441 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job_offer DROP FOREIGN KEY FK_288A3A4E979B1AD6');
        $this->addSql('DROP TABLE job_offer');
        $this->addSql('ALTER TABLE company_profile CHANGE logo logo VARCHAR(255) DEFAULT NULL, CHANGE industry industry VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE developer_profile CHANGE programming_languages programming_languages JSON NOT NULL, CHANGE avatar avatar VARCHAR(255) DEFAULT NULL, CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE github github VARCHAR(255) DEFAULT NULL, CHANGE linkedin linkedin VARCHAR(255) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE job_application CHANGE status status VARCHAR(20) NOT NULL, CHANGE cv_filename cv_filename VARCHAR(255) DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE company_response feedback LONGTEXT DEFAULT NULL, CHANGE applied_at created_at DATETIME NOT NULL, CHANGE job_offer_id job_post_id INT NOT NULL');
        $this->addSql('ALTER TABLE job_application ADD CONSTRAINT FK_C737C688D166B4B7 FOREIGN KEY (job_post_id) REFERENCES job_post (id)');
        $this->addSql('ALTER TABLE job_application ADD CONSTRAINT FK_C737C68864DD9267 FOREIGN KEY (developer_id) REFERENCES developer_profile (id)');
        $this->addSql('CREATE INDEX IDX_C737C688D166B4B7 ON job_application (job_post_id)');
        $this->addSql('ALTER TABLE job_post CHANGE programming_languages programming_languages JSON NOT NULL, CHANGE last_match_check_at last_match_check_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE notification CHANGE data data JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE search_history CHANGE filters filters JSON NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE last_login_at last_login_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE job_offer (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, created_at DATETIME NOT NULL, company_id INT NOT NULL, INDEX IDX_288A3A4E979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE job_offer ADD CONSTRAINT FK_288A3A4E979B1AD6 FOREIGN KEY (company_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE company_profile CHANGE logo logo VARCHAR(255) DEFAULT \'NULL\', CHANGE industry industry VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE developer_profile CHANGE programming_languages programming_languages LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE avatar avatar VARCHAR(255) DEFAULT \'NULL\', CHANGE title title VARCHAR(255) DEFAULT \'NULL\', CHANGE github github VARCHAR(255) DEFAULT \'NULL\', CHANGE linkedin linkedin VARCHAR(255) DEFAULT \'NULL\', CHANGE created_at created_at DATETIME DEFAULT \'current_timestamp()\' NOT NULL');
        $this->addSql('ALTER TABLE job_application DROP FOREIGN KEY FK_C737C688D166B4B7');
        $this->addSql('ALTER TABLE job_application DROP FOREIGN KEY FK_C737C68864DD9267');
        $this->addSql('DROP INDEX IDX_C737C688D166B4B7 ON job_application');
        $this->addSql('ALTER TABLE job_application CHANGE cv_filename cv_filename VARCHAR(255) DEFAULT \'NULL\', CHANGE status status VARCHAR(255) NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\', CHANGE created_at applied_at DATETIME NOT NULL, CHANGE job_post_id job_offer_id INT NOT NULL, CHANGE feedback company_response LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE job_post CHANGE programming_languages programming_languages LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_match_check_at last_match_check_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE notification CHANGE data data LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE search_history CHANGE filters filters LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE `user` CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_login_at last_login_at DATETIME DEFAULT \'NULL\'');
    }
}
