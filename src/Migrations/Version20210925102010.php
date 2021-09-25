<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210925102010 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE problem_filter_room (problem_filter_id INT NOT NULL, room_id INT UNSIGNED NOT NULL, INDEX IDX_1C5BAEBD4CE3BCF9 (problem_filter_id), INDEX IDX_1C5BAEBD54177093 (room_id), PRIMARY KEY(problem_filter_id, room_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE problem_filter_room ADD CONSTRAINT FK_1C5BAEBD4CE3BCF9 FOREIGN KEY (problem_filter_id) REFERENCES problem_filter (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE problem_filter_room ADD CONSTRAINT FK_1C5BAEBD54177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE problem_filter DROP FOREIGN KEY FK_3E582D5254177093');
        $this->addSql('DROP INDEX IDX_3E582D5254177093 ON problem_filter');
        $this->addSql('ALTER TABLE problem_filter DROP room_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE problem_filter_room');
        $this->addSql('ALTER TABLE problem_filter ADD room_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE problem_filter ADD CONSTRAINT FK_3E582D5254177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_3E582D5254177093 ON problem_filter (room_id)');
    }
}
