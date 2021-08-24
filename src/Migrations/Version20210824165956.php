<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210824165956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE problem DROP FOREIGN KEY FK_D7E7CCC894A4C7D4');
        $this->addSql('ALTER TABLE problem ADD CONSTRAINT FK_D7E7CCC894A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE problem DROP FOREIGN KEY FK_D7E7CCC894A4C7D4');
        $this->addSql('ALTER TABLE problem ADD CONSTRAINT FK_D7E7CCC894A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id)');
    }
}
