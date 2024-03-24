<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200713145716 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE announcement (id INT UNSIGNED AUTO_INCREMENT NOT NULL, category_id INT UNSIGNED DEFAULT NULL, created_by_id INT UNSIGNED NOT NULL, title VARCHAR(255) NOT NULL, details LONGTEXT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_4DB9D91CD17F50A6 (uuid), INDEX IDX_4DB9D91C12469DE2 (category_id), INDEX IDX_4DB9D91CB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE announcement_room (announcement_id INT UNSIGNED NOT NULL, room_id INT UNSIGNED NOT NULL, INDEX IDX_5EAE59C7913AEA17 (announcement_id), INDEX IDX_5EAE59C754177093 (room_id), PRIMARY KEY(announcement_id, room_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE announcement_category (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_7D019332D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT UNSIGNED AUTO_INCREMENT NOT NULL, problem_id INT UNSIGNED DEFAULT NULL, created_by_id INT UNSIGNED DEFAULT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_9474526CD17F50A6 (uuid), INDEX IDX_9474526CA0DCED86 (problem_id), INDEX IDX_9474526CB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cron_job (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, command VARCHAR(255) NOT NULL, arguments VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, running_instances INT UNSIGNED DEFAULT 0 NOT NULL, max_instances INT UNSIGNED DEFAULT 1 NOT NULL, number INT UNSIGNED DEFAULT 1 NOT NULL, period VARCHAR(255) NOT NULL, last_use DATETIME DEFAULT NULL, next_run DATETIME NOT NULL, enable TINYINT(1) DEFAULT \'1\' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cron_job_result (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, cron_job_id BIGINT UNSIGNED NOT NULL, run_at DATETIME NOT NULL, run_time DOUBLE PRECISION NOT NULL, status_code INT NOT NULL, output LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_2CD346EE79099ED8 (cron_job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device (id INT UNSIGNED AUTO_INCREMENT NOT NULL, room_id INT UNSIGNED DEFAULT NULL, type_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_92FB68ED17F50A6 (uuid), INDEX IDX_92FB68E54177093 (room_id), INDEX IDX_92FB68EC54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device_type (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_5E78213D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(255) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('CREATE TABLE id_entity (entity_id VARCHAR(255) NOT NULL, id VARCHAR(255) NOT NULL, expiry DATETIME NOT NULL, PRIMARY KEY(entity_id, id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log (id INT AUTO_INCREMENT NOT NULL, channel VARCHAR(255) NOT NULL, level INT NOT NULL, message LONGTEXT NOT NULL, time DATETIME NOT NULL, details LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_setting (id INT AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, is_enabled TINYINT(1) NOT NULL, email LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_8A6A322FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_setting_room (notification_setting_id INT NOT NULL, room_id INT UNSIGNED NOT NULL, INDEX IDX_B225974815471850 (notification_setting_id), INDEX IDX_B225974854177093 (room_id), PRIMARY KEY(notification_setting_id, room_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_setting_problem_type (notification_setting_id INT NOT NULL, problem_type_id INT UNSIGNED NOT NULL, INDEX IDX_23CA43ED15471850 (notification_setting_id), INDEX IDX_23CA43ED236E4CE0 (problem_type_id), PRIMARY KEY(notification_setting_id, problem_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE placard (id INT UNSIGNED AUTO_INCREMENT NOT NULL, room_id INT UNSIGNED DEFAULT NULL, updated_by_id INT UNSIGNED DEFAULT NULL, header VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_9D30D946D17F50A6 (uuid), UNIQUE INDEX UNIQ_9D30D94654177093 (room_id), INDEX IDX_9D30D946896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE placard_device (id INT AUTO_INCREMENT NOT NULL, placard_id INT UNSIGNED DEFAULT NULL, source VARCHAR(255) NOT NULL, beamer VARCHAR(255) NOT NULL, av VARCHAR(255) NOT NULL, INDEX IDX_CFDDD5664757A25 (placard_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE problem (id INT UNSIGNED AUTO_INCREMENT NOT NULL, problem_type_id INT UNSIGNED DEFAULT NULL, assignee_id INT UNSIGNED DEFAULT NULL, created_by_id INT UNSIGNED DEFAULT NULL, device_id INT UNSIGNED DEFAULT NULL, priority VARCHAR(255) NOT NULL COMMENT \'(DC2Type:priority)\', is_open TINYINT(1) NOT NULL, is_maintenance TINYINT(1) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_D7E7CCC8D17F50A6 (uuid), INDEX IDX_D7E7CCC8236E4CE0 (problem_type_id), INDEX IDX_D7E7CCC859EC7D60 (assignee_id), INDEX IDX_D7E7CCC8B03A8386 (created_by_id), INDEX IDX_D7E7CCC894A4C7D4 (device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE problem_filter (id INT AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, room_id INT UNSIGNED DEFAULT NULL, include_solved TINYINT(1) NOT NULL, include_maintenance TINYINT(1) NOT NULL, sort_column VARCHAR(255) NOT NULL, sort_order VARCHAR(255) NOT NULL, num_items INT NOT NULL, UNIQUE INDEX UNIQ_3E582D52A76ED395 (user_id), INDEX IDX_3E582D5254177093 (room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE problem_type (id INT UNSIGNED AUTO_INCREMENT NOT NULL, device_type_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_56D1406BD17F50A6 (uuid), INDEX IDX_56D1406B4FFA550E (device_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room (id INT UNSIGNED AUTO_INCREMENT NOT NULL, category_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_729F519BD17F50A6 (uuid), INDEX IDX_729F519B12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room_category (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_A6AAD905D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE update_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(128) NOT NULL, date_time DATETIME NOT NULL, UNIQUE INDEX UNIQ_59FF81D6F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT UNSIGNED AUTO_INCREMENT NOT NULL, idp_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', username VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', data LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wiki (id INT UNSIGNED AUTO_INCREMENT NOT NULL, created_by_id INT UNSIGNED DEFAULT NULL, updated_by_id INT UNSIGNED DEFAULT NULL, root_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, access VARCHAR(255) NOT NULL COMMENT \'(DC2Type:wiki_access)\', `left` INT NOT NULL, level INT NOT NULL, `right` INT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', `parent` INT UNSIGNED DEFAULT NULL, UNIQUE INDEX UNIQ_22CDDC06D17F50A6 (uuid), INDEX IDX_22CDDC06B03A8386 (created_by_id), INDEX IDX_22CDDC06896DBBDE (updated_by_id), INDEX IDX_22CDDC0679066886 (root_id), INDEX IDX_22CDDC06514BFC18 (`parent`), FULLTEXT INDEX IDX_22CDDC065E237E06 (name), FULLTEXT INDEX IDX_22CDDC06FEC530A9 (content), UNIQUE INDEX unique_parent_slug (parent, slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE announcement ADD CONSTRAINT FK_4DB9D91C12469DE2 FOREIGN KEY (category_id) REFERENCES announcement_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE announcement ADD CONSTRAINT FK_4DB9D91CB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE announcement_room ADD CONSTRAINT FK_5EAE59C7913AEA17 FOREIGN KEY (announcement_id) REFERENCES announcement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE announcement_room ADD CONSTRAINT FK_5EAE59C754177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA0DCED86 FOREIGN KEY (problem_id) REFERENCES problem (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE cron_job_result ADD CONSTRAINT FK_2CD346EE79099ED8 FOREIGN KEY (cron_job_id) REFERENCES cron_job (id)');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68E54177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68EC54C8C93 FOREIGN KEY (type_id) REFERENCES device_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification_setting ADD CONSTRAINT FK_8A6A322FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification_setting_room ADD CONSTRAINT FK_B225974815471850 FOREIGN KEY (notification_setting_id) REFERENCES notification_setting (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification_setting_room ADD CONSTRAINT FK_B225974854177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification_setting_problem_type ADD CONSTRAINT FK_23CA43ED15471850 FOREIGN KEY (notification_setting_id) REFERENCES notification_setting (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification_setting_problem_type ADD CONSTRAINT FK_23CA43ED236E4CE0 FOREIGN KEY (problem_type_id) REFERENCES problem_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE placard ADD CONSTRAINT FK_9D30D94654177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE placard ADD CONSTRAINT FK_9D30D946896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE placard_device ADD CONSTRAINT FK_CFDDD5664757A25 FOREIGN KEY (placard_id) REFERENCES placard (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE problem ADD CONSTRAINT FK_D7E7CCC8236E4CE0 FOREIGN KEY (problem_type_id) REFERENCES problem_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE problem ADD CONSTRAINT FK_D7E7CCC859EC7D60 FOREIGN KEY (assignee_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE problem ADD CONSTRAINT FK_D7E7CCC8B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE problem ADD CONSTRAINT FK_D7E7CCC894A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id)');
        $this->addSql('ALTER TABLE problem_filter ADD CONSTRAINT FK_3E582D52A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE problem_filter ADD CONSTRAINT FK_3E582D5254177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE problem_type ADD CONSTRAINT FK_56D1406B4FFA550E FOREIGN KEY (device_type_id) REFERENCES device_type (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B12469DE2 FOREIGN KEY (category_id) REFERENCES room_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wiki ADD CONSTRAINT FK_22CDDC06B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE wiki ADD CONSTRAINT FK_22CDDC06896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE wiki ADD CONSTRAINT FK_22CDDC0679066886 FOREIGN KEY (root_id) REFERENCES wiki (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wiki ADD CONSTRAINT FK_22CDDC06514BFC18 FOREIGN KEY (`parent`) REFERENCES wiki (`id`) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE announcement_room DROP FOREIGN KEY FK_5EAE59C7913AEA17');
        $this->addSql('ALTER TABLE announcement DROP FOREIGN KEY FK_4DB9D91C12469DE2');
        $this->addSql('ALTER TABLE cron_job_result DROP FOREIGN KEY FK_2CD346EE79099ED8');
        $this->addSql('ALTER TABLE problem DROP FOREIGN KEY FK_D7E7CCC894A4C7D4');
        $this->addSql('ALTER TABLE device DROP FOREIGN KEY FK_92FB68EC54C8C93');
        $this->addSql('ALTER TABLE problem_type DROP FOREIGN KEY FK_56D1406B4FFA550E');
        $this->addSql('ALTER TABLE notification_setting_room DROP FOREIGN KEY FK_B225974815471850');
        $this->addSql('ALTER TABLE notification_setting_problem_type DROP FOREIGN KEY FK_23CA43ED15471850');
        $this->addSql('ALTER TABLE placard_device DROP FOREIGN KEY FK_CFDDD5664757A25');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA0DCED86');
        $this->addSql('ALTER TABLE notification_setting_problem_type DROP FOREIGN KEY FK_23CA43ED236E4CE0');
        $this->addSql('ALTER TABLE problem DROP FOREIGN KEY FK_D7E7CCC8236E4CE0');
        $this->addSql('ALTER TABLE announcement_room DROP FOREIGN KEY FK_5EAE59C754177093');
        $this->addSql('ALTER TABLE device DROP FOREIGN KEY FK_92FB68E54177093');
        $this->addSql('ALTER TABLE notification_setting_room DROP FOREIGN KEY FK_B225974854177093');
        $this->addSql('ALTER TABLE placard DROP FOREIGN KEY FK_9D30D94654177093');
        $this->addSql('ALTER TABLE problem_filter DROP FOREIGN KEY FK_3E582D5254177093');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B12469DE2');
        $this->addSql('ALTER TABLE announcement DROP FOREIGN KEY FK_4DB9D91CB03A8386');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CB03A8386');
        $this->addSql('ALTER TABLE notification_setting DROP FOREIGN KEY FK_8A6A322FA76ED395');
        $this->addSql('ALTER TABLE placard DROP FOREIGN KEY FK_9D30D946896DBBDE');
        $this->addSql('ALTER TABLE problem DROP FOREIGN KEY FK_D7E7CCC859EC7D60');
        $this->addSql('ALTER TABLE problem DROP FOREIGN KEY FK_D7E7CCC8B03A8386');
        $this->addSql('ALTER TABLE problem_filter DROP FOREIGN KEY FK_3E582D52A76ED395');
        $this->addSql('ALTER TABLE wiki DROP FOREIGN KEY FK_22CDDC06B03A8386');
        $this->addSql('ALTER TABLE wiki DROP FOREIGN KEY FK_22CDDC06896DBBDE');
        $this->addSql('ALTER TABLE wiki DROP FOREIGN KEY FK_22CDDC0679066886');
        $this->addSql('ALTER TABLE wiki DROP FOREIGN KEY FK_22CDDC06514BFC18');
        $this->addSql('DROP TABLE announcement');
        $this->addSql('DROP TABLE announcement_room');
        $this->addSql('DROP TABLE announcement_category');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE cron_job');
        $this->addSql('DROP TABLE cron_job_result');
        $this->addSql('DROP TABLE device');
        $this->addSql('DROP TABLE device_type');
        $this->addSql('DROP TABLE ext_log_entries');
        $this->addSql('DROP TABLE id_entity');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE notification_setting');
        $this->addSql('DROP TABLE notification_setting_room');
        $this->addSql('DROP TABLE notification_setting_problem_type');
        $this->addSql('DROP TABLE placard');
        $this->addSql('DROP TABLE placard_device');
        $this->addSql('DROP TABLE problem');
        $this->addSql('DROP TABLE problem_filter');
        $this->addSql('DROP TABLE problem_type');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE room_category');
        $this->addSql('DROP TABLE update_user');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE wiki');
    }
}
