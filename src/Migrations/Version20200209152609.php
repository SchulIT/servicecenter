<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200209152609 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE announcement (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, created_by_id INT NOT NULL, title VARCHAR(255) NOT NULL, details LONGTEXT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME DEFAULT NULL, INDEX IDX_4DB9D91C12469DE2 (category_id), INDEX IDX_4DB9D91CB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE announcement_room (announcement_id INT NOT NULL, room_id INT NOT NULL, INDEX IDX_5EAE59C7913AEA17 (announcement_id), INDEX IDX_5EAE59C754177093 (room_id), PRIMARY KEY(announcement_id, room_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE announcement_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, problem_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_9474526CA0DCED86 (problem_id), INDEX IDX_9474526CB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device (id INT AUTO_INCREMENT NOT NULL, room_id INT DEFAULT NULL, type_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_92FB68E54177093 (room_id), INDEX IDX_92FB68EC54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_setting (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, is_enabled TINYINT(1) NOT NULL, email LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_8A6A322FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_setting_room (notification_setting_id INT NOT NULL, room_id INT NOT NULL, INDEX IDX_B225974815471850 (notification_setting_id), INDEX IDX_B225974854177093 (room_id), PRIMARY KEY(notification_setting_id, room_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_setting_problem_type (notification_setting_id INT NOT NULL, problem_type_id INT NOT NULL, INDEX IDX_23CA43ED15471850 (notification_setting_id), INDEX IDX_23CA43ED236E4CE0 (problem_type_id), PRIMARY KEY(notification_setting_id, problem_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE placard (id INT AUTO_INCREMENT NOT NULL, room_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, header VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_9D30D94654177093 (room_id), INDEX IDX_9D30D946896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE placard_device (id INT AUTO_INCREMENT NOT NULL, placard_id INT DEFAULT NULL, source VARCHAR(255) NOT NULL, beamer VARCHAR(255) NOT NULL, av VARCHAR(255) NOT NULL, INDEX IDX_CFDDD5664757A25 (placard_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE problem (id INT AUTO_INCREMENT NOT NULL, problem_type_id INT DEFAULT NULL, assignee_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, device_id INT DEFAULT NULL, priority VARCHAR(255) NOT NULL COMMENT \'(DC2Type:priority)\', is_open TINYINT(1) NOT NULL, is_maintenance TINYINT(1) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_D7E7CCC8236E4CE0 (problem_type_id), INDEX IDX_D7E7CCC859EC7D60 (assignee_id), INDEX IDX_D7E7CCC8B03A8386 (created_by_id), INDEX IDX_D7E7CCC894A4C7D4 (device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE problem_filter (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, room_id INT DEFAULT NULL, include_solved TINYINT(1) NOT NULL, include_maintenance TINYINT(1) NOT NULL, sort_column VARCHAR(255) NOT NULL, sort_order VARCHAR(255) NOT NULL, num_items INT NOT NULL, UNIQUE INDEX UNIQ_3E582D52A76ED395 (user_id), INDEX IDX_3E582D5254177093 (room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE problem_type (id INT AUTO_INCREMENT NOT NULL, device_type_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_56D1406B4FFA550E (device_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, alias VARCHAR(32) NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_729F519BE16C6B94 (alias), INDEX IDX_729F519B12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json_array)\', UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wiki_article (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, access VARCHAR(255) NOT NULL COMMENT \'(DC2Type:wiki_access)\', INDEX IDX_CA09FEA312469DE2 (category_id), INDEX IDX_CA09FEA3B03A8386 (created_by_id), INDEX IDX_CA09FEA3896DBBDE (updated_by_id), FULLTEXT INDEX IDX_CA09FEA35E237E06 (name), FULLTEXT INDEX IDX_CA09FEA3FEC530A9 (content), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wiki_category (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, access VARCHAR(255) NOT NULL COMMENT \'(DC2Type:wiki_access)\', INDEX IDX_ED8A1C0E727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(255) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('CREATE TABLE id_entity (entity_id VARCHAR(255) NOT NULL, id VARCHAR(255) NOT NULL, expiry DATETIME NOT NULL, PRIMARY KEY(entity_id, id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log (id INT AUTO_INCREMENT NOT NULL, channel VARCHAR(255) NOT NULL, level INT NOT NULL, message LONGTEXT NOT NULL, time DATETIME NOT NULL, details LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE update_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(128) NOT NULL, date_time DATETIME NOT NULL, UNIQUE INDEX UNIQ_59FF81D6F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE announcement ADD CONSTRAINT FK_4DB9D91C12469DE2 FOREIGN KEY (category_id) REFERENCES announcement_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE announcement ADD CONSTRAINT FK_4DB9D91CB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE announcement_room ADD CONSTRAINT FK_5EAE59C7913AEA17 FOREIGN KEY (announcement_id) REFERENCES announcement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE announcement_room ADD CONSTRAINT FK_5EAE59C754177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA0DCED86 FOREIGN KEY (problem_id) REFERENCES problem (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
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
        $this->addSql('ALTER TABLE problem ADD CONSTRAINT FK_D7E7CCC859EC7D60 FOREIGN KEY (assignee_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE problem ADD CONSTRAINT FK_D7E7CCC8B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE problem ADD CONSTRAINT FK_D7E7CCC894A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id)');
        $this->addSql('ALTER TABLE problem_filter ADD CONSTRAINT FK_3E582D52A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE problem_filter ADD CONSTRAINT FK_3E582D5254177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE problem_type ADD CONSTRAINT FK_56D1406B4FFA550E FOREIGN KEY (device_type_id) REFERENCES device_type (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B12469DE2 FOREIGN KEY (category_id) REFERENCES room_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wiki_article ADD CONSTRAINT FK_CA09FEA312469DE2 FOREIGN KEY (category_id) REFERENCES wiki_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wiki_article ADD CONSTRAINT FK_CA09FEA3B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE wiki_article ADD CONSTRAINT FK_CA09FEA3896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE wiki_category ADD CONSTRAINT FK_ED8A1C0E727ACA70 FOREIGN KEY (parent_id) REFERENCES wiki_category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE announcement_room DROP FOREIGN KEY FK_5EAE59C7913AEA17');
        $this->addSql('ALTER TABLE announcement DROP FOREIGN KEY FK_4DB9D91C12469DE2');
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
        $this->addSql('ALTER TABLE wiki_article DROP FOREIGN KEY FK_CA09FEA3B03A8386');
        $this->addSql('ALTER TABLE wiki_article DROP FOREIGN KEY FK_CA09FEA3896DBBDE');
        $this->addSql('ALTER TABLE wiki_article DROP FOREIGN KEY FK_CA09FEA312469DE2');
        $this->addSql('ALTER TABLE wiki_category DROP FOREIGN KEY FK_ED8A1C0E727ACA70');
        $this->addSql('DROP TABLE announcement');
        $this->addSql('DROP TABLE announcement_room');
        $this->addSql('DROP TABLE announcement_category');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE device');
        $this->addSql('DROP TABLE device_type');
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
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE wiki_article');
        $this->addSql('DROP TABLE wiki_category');
        $this->addSql('DROP TABLE ext_log_entries');
        $this->addSql('DROP TABLE id_entity');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE update_user');
    }
}
