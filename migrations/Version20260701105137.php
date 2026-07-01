<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260701105137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create artist_profile_genre join table (ArtistProfile <-> Genre ManyToMany)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE artist_profile_genre (artist_profile_id INT NOT NULL, genre_id INT NOT NULL, INDEX IDX_E9BE4A6B2F85CDC1 (artist_profile_id), INDEX IDX_E9BE4A6B4296D31F (genre_id), PRIMARY KEY (artist_profile_id, genre_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE artist_profile_genre ADD CONSTRAINT FK_E9BE4A6B2F85CDC1 FOREIGN KEY (artist_profile_id) REFERENCES artist_profile (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE artist_profile_genre ADD CONSTRAINT FK_E9BE4A6B4296D31F FOREIGN KEY (genre_id) REFERENCES genre (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE artist_profile_genre DROP FOREIGN KEY FK_E9BE4A6B2F85CDC1');
        $this->addSql('ALTER TABLE artist_profile_genre DROP FOREIGN KEY FK_E9BE4A6B4296D31F');
        $this->addSql('DROP TABLE artist_profile_genre');
    }
}
