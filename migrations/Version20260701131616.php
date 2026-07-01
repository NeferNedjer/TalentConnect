<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260701131616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX IDX_ANNOUNCEMENT_STATUS ON announcement (status)');
        $this->addSql('CREATE INDEX IDX_ANNOUNCEMENT_PUBLISHED_AT ON announcement (published_at)');
        $this->addSql('CREATE INDEX IDX_ANNOUNCEMENT_CITY ON announcement (city)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_ANNOUNCEMENT_STATUS ON announcement');
        $this->addSql('DROP INDEX IDX_ANNOUNCEMENT_PUBLISHED_AT ON announcement');
        $this->addSql('DROP INDEX IDX_ANNOUNCEMENT_CITY ON announcement');
    }
}
