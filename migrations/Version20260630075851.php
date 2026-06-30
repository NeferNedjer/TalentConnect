<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260630075851 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create professional_profile table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE professional_profile (id INT AUTO_INCREMENT NOT NULL, company_name VARCHAR(150) NOT NULL, slug VARCHAR(150) NOT NULL, type VARCHAR(30) NOT NULL, description LONGTEXT DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, email_public VARCHAR(180) DEFAULT NULL, telephone_public VARCHAR(30) DEFAULT NULL, city VARCHAR(120) DEFAULT NULL, postal_code VARCHAR(20) DEFAULT NULL, region VARCHAR(120) DEFAULT NULL, country VARCHAR(120) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, cover_picture VARCHAR(255) DEFAULT NULL, is_verified TINYINT NOT NULL, verification_status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_E728A82A76ED395 (user_id), UNIQUE INDEX UNIQ_PROFESSIONAL_PROFILE_SLUG (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE professional_profile ADD CONSTRAINT FK_E728A82A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE professional_profile DROP FOREIGN KEY FK_E728A82A76ED395');
        $this->addSql('DROP TABLE professional_profile');
    }
}
