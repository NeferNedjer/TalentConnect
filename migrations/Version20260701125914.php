<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260701125914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE announcement (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(150) NOT NULL, slug VARCHAR(150) NOT NULL, description LONGTEXT NOT NULL, announcement_type VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, remuneration_type VARCHAR(255) NOT NULL, remuneration_amount NUMERIC(10, 2) DEFAULT NULL, currency VARCHAR(3) NOT NULL, city VARCHAR(120) DEFAULT NULL, region VARCHAR(120) DEFAULT NULL, country VARCHAR(120) DEFAULT NULL, is_remote TINYINT NOT NULL, published_at DATETIME DEFAULT NULL, closed_at DATETIME DEFAULT NULL, expires_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_by_id INT NOT NULL, publisher_artist_id INT DEFAULT NULL, publisher_professional_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_4DB9D91C989D9B62 (slug), INDEX IDX_4DB9D91CB03A8386 (created_by_id), INDEX IDX_4DB9D91C4F62FCE8 (publisher_artist_id), INDEX IDX_4DB9D91C4C3AE638 (publisher_professional_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE announcement_genre (announcement_id INT NOT NULL, genre_id INT NOT NULL, INDEX IDX_E1A11F2F913AEA17 (announcement_id), INDEX IDX_E1A11F2F4296D31F (genre_id), PRIMARY KEY (announcement_id, genre_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE announcement ADD CONSTRAINT FK_4DB9D91CB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE announcement ADD CONSTRAINT FK_4DB9D91C4F62FCE8 FOREIGN KEY (publisher_artist_id) REFERENCES artist_profile (id)');
        $this->addSql('ALTER TABLE announcement ADD CONSTRAINT FK_4DB9D91C4C3AE638 FOREIGN KEY (publisher_professional_id) REFERENCES professional_profile (id)');
        $this->addSql('ALTER TABLE announcement_genre ADD CONSTRAINT FK_E1A11F2F913AEA17 FOREIGN KEY (announcement_id) REFERENCES announcement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE announcement_genre ADD CONSTRAINT FK_E1A11F2F4296D31F FOREIGN KEY (genre_id) REFERENCES genre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE genre RENAME INDEX uniq_genre_slug TO UNIQ_835033F8989D9B62');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE announcement DROP FOREIGN KEY FK_4DB9D91CB03A8386');
        $this->addSql('ALTER TABLE announcement DROP FOREIGN KEY FK_4DB9D91C4F62FCE8');
        $this->addSql('ALTER TABLE announcement DROP FOREIGN KEY FK_4DB9D91C4C3AE638');
        $this->addSql('ALTER TABLE announcement_genre DROP FOREIGN KEY FK_E1A11F2F913AEA17');
        $this->addSql('ALTER TABLE announcement_genre DROP FOREIGN KEY FK_E1A11F2F4296D31F');
        $this->addSql('DROP TABLE announcement');
        $this->addSql('DROP TABLE announcement_genre');
        $this->addSql('ALTER TABLE genre RENAME INDEX uniq_835033f8989d9b62 TO UNIQ_GENRE_SLUG');
    }
}
