<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260630072927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artist_profile (id INT AUTO_INCREMENT NOT NULL, stage_name VARCHAR(120) NOT NULL, slug VARCHAR(150) NOT NULL, bio LONGTEXT DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, city VARCHAR(120) DEFAULT NULL, postal_code VARCHAR(20) DEFAULT NULL, region VARCHAR(120) DEFAULT NULL, country VARCHAR(120) DEFAULT NULL, profile_picture VARCHAR(255) DEFAULT NULL, cover_picture VARCHAR(255) DEFAULT NULL, artist_type VARCHAR(20) NOT NULL, spotify_url VARCHAR(255) DEFAULT NULL, youtube_url VARCHAR(255) DEFAULT NULL, instagram_url VARCHAR(255) DEFAULT NULL, facebook_url VARCHAR(255) DEFAULT NULL, tiktok_url VARCHAR(255) DEFAULT NULL, profile_completion INT NOT NULL, is_certified TINYINT NOT NULL, verification_status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_3618F438A76ED395 (user_id), UNIQUE INDEX UNIQ_ARTIST_PROFILE_SLUG (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE artist_profile ADD CONSTRAINT FK_3618F438A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist_profile DROP FOREIGN KEY FK_3618F438A76ED395');
        $this->addSql('DROP TABLE artist_profile');
    }
}
