<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220904140728 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` CHANGE COLUMN `roles` `roles` LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\' COLLATE \'utf8mb4_unicode_ci\' AFTER `email`;');
        $this->addSql('ALTER TABLE placard DROP FOREIGN KEY FK_9D30D94654177093');
        $this->addSql('ALTER TABLE placard DROP FOREIGN KEY FK_9D30D946896DBBDE');
        $this->addSql('ALTER TABLE placard_device DROP FOREIGN KEY FK_CFDDD5664757A25');
        $this->addSql('DROP TABLE idp_exchange');
        $this->addSql('DROP TABLE placard');
        $this->addSql('DROP TABLE placard_device');
        $this->addSql('DROP TABLE update_user');
        $this->addSql('ALTER TABLE cron_job_result DROP FOREIGN KEY FK_2CD346EE79099ED8');
        $this->addSql('ALTER TABLE cron_job_result ADD CONSTRAINT FK_2CD346EE79099ED8 FOREIGN KEY (cron_job_id) REFERENCES cron_job (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ext_log_entries CHANGE object_class object_class VARCHAR(191) NOT NULL, CHANGE username username VARCHAR(191) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` CHANGE COLUMN `roles` `roles` LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\' COLLATE \'utf8mb4_unicode_ci\' AFTER `email`;');
        $this->addSql('CREATE TABLE idp_exchange (id INT AUTO_INCREMENT NOT NULL, meta_key VARCHAR(255) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, meta_value BLOB DEFAULT NULL, UNIQUE INDEX meta_key_UNIQUE (meta_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET latin1 COLLATE `latin1_swedish_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE placard (id INT UNSIGNED AUTO_INCREMENT NOT NULL, room_id INT UNSIGNED DEFAULT NULL, updated_by_id INT UNSIGNED DEFAULT NULL, header VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, updated_at DATETIME NOT NULL, uuid CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_9D30D946D17F50A6 (uuid), UNIQUE INDEX UNIQ_9D30D94654177093 (room_id), INDEX IDX_9D30D946896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE placard_device (id INT AUTO_INCREMENT NOT NULL, placard_id INT UNSIGNED DEFAULT NULL, source VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, beamer VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, av VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_CFDDD5664757A25 (placard_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE update_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_time DATETIME NOT NULL, UNIQUE INDEX UNIQ_59FF81D6F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE placard ADD CONSTRAINT FK_9D30D94654177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE placard ADD CONSTRAINT FK_9D30D946896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE placard_device ADD CONSTRAINT FK_CFDDD5664757A25 FOREIGN KEY (placard_id) REFERENCES placard (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cron_job_result DROP FOREIGN KEY FK_2CD346EE79099ED8');
        $this->addSql('ALTER TABLE cron_job_result ADD CONSTRAINT FK_2CD346EE79099ED8 FOREIGN KEY (cron_job_id) REFERENCES cron_job (id)');
        $this->addSql('ALTER TABLE ext_log_entries CHANGE object_class object_class VARCHAR(255) NOT NULL, CHANGE username username VARCHAR(255) DEFAULT NULL');
    }
}
