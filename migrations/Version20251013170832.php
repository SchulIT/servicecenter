<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251013170832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE problem_filter DROP FOREIGN KEY FK_3E582D52A76ED395');
        $this->addSql('ALTER TABLE problem_filter_room DROP FOREIGN KEY FK_1C5BAEBD4CE3BCF9');
        $this->addSql('ALTER TABLE problem_filter_room DROP FOREIGN KEY FK_1C5BAEBD54177093');
        $this->addSql('DROP TABLE problem_filter');
        $this->addSql('DROP TABLE problem_filter_room');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE problem_filter (id INT AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, include_solved TINYINT(1) NOT NULL, include_maintenance TINYINT(1) NOT NULL, sort_column VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, sort_order VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, num_items INT NOT NULL, UNIQUE INDEX UNIQ_3E582D52A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE problem_filter_room (problem_filter_id INT NOT NULL, room_id INT UNSIGNED NOT NULL, INDEX IDX_1C5BAEBD4CE3BCF9 (problem_filter_id), INDEX IDX_1C5BAEBD54177093 (room_id), PRIMARY KEY(problem_filter_id, room_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE problem_filter ADD CONSTRAINT FK_3E582D52A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE problem_filter_room ADD CONSTRAINT FK_1C5BAEBD4CE3BCF9 FOREIGN KEY (problem_filter_id) REFERENCES problem_filter (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE problem_filter_room ADD CONSTRAINT FK_1C5BAEBD54177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
    }
}
