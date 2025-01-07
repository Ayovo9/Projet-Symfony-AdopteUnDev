<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250103094856 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE developer_profile (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, programming_languages JSON NOT NULL, experience_level INT NOT NULL, min_salary NUMERIC(10, 2) NOT NULL, bio LONGTEXT NOT NULL, avatar VARCHAR(255) DEFAULT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_EFC94CA4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE job_post (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, technologies JSON NOT NULL, experience_required INT NOT NULL, salary NUMERIC(10, 2) NOT NULL, description LONGTEXT NOT NULL, user_id INT NOT NULL, INDEX IDX_DD461ACCA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE developer_profile ADD CONSTRAINT FK_EFC94CA4A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE job_post ADD CONSTRAINT FK_DD461ACCA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user ADD username VARCHAR(255) NOT NULL, ADD role VARCHAR(20) NOT NULL, ADD created_at DATETIME NOT NULL, DROP roles, DROP user_type, DROP profile_image, DROP first_name, DROP last_name, DROP location, DROP programming_languages, DROP experience_level, DROP minimum_salary, DROP biography, CHANGE email email VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE developer_profile DROP FOREIGN KEY FK_EFC94CA4A76ED395');
        $this->addSql('ALTER TABLE job_post DROP FOREIGN KEY FK_DD461ACCA76ED395');
        $this->addSql('DROP TABLE developer_profile');
        $this->addSql('DROP TABLE job_post');
        $this->addSql('ALTER TABLE `user` ADD roles JSON NOT NULL, ADD user_type VARCHAR(50) NOT NULL, ADD profile_image VARCHAR(255) DEFAULT NULL, ADD first_name VARCHAR(50) DEFAULT NULL, ADD last_name VARCHAR(50) DEFAULT NULL, ADD location VARCHAR(100) DEFAULT NULL, ADD programming_languages JSON DEFAULT NULL, ADD experience_level INT DEFAULT NULL, ADD minimum_salary NUMERIC(10, 2) DEFAULT NULL, ADD biography LONGTEXT DEFAULT NULL, DROP username, DROP role, DROP created_at, CHANGE email email VARCHAR(180) NOT NULL');
    }
}
