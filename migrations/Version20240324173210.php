<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Priority;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240324173210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE wiki CHANGE COLUMN `access` `access` VARCHAR(255) NOT NULL COLLATE \'utf8mb4_unicode_ci\' AFTER updated_at');
        $this->addSql('ALTER TABLE problem CHANGE COLUMN `priority` `priority` VARCHAR(255) NOT NULL COLLATE \'utf8mb4_unicode_ci\' AFTER device_id');

        $this->addSql('CREATE TABLE IF NOT EXISTS messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE announcement CHANGE category_id category_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE comment CHANGE problem_id problem_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE device CHANGE room_id room_id INT UNSIGNED NOT NULL, CHANGE type_id type_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE notification_setting DROP FOREIGN KEY FK_8A6A322FA76ED395');
        $this->addSql('ALTER TABLE notification_setting CHANGE user_id user_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE notification_setting ADD CONSTRAINT FK_8A6A322FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE problem CHANGE device_id device_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE problem_filter DROP FOREIGN KEY FK_3E582D52A76ED395');
        $this->addSql('ALTER TABLE problem_filter CHANGE user_id user_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE problem_filter ADD CONSTRAINT FK_3E582D52A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE data data JSON NOT NULL COMMENT \'(DC2Type:json)\'');

        foreach($this->connection->fetchAllAssociative('SELECT * FROM ext_log_entries') as $entry) {
            if(mb_strpos($entry['data'], 'priority') === false) {
                continue;
            }

            $entry['data'] = preg_replace_callback('/O:\d+:"App\\\\Entity\\\\Priority":\d:{s:\d+:".*?";s:\d+:"(.*?)";.*?}(;?)/', function($matches) {
                return serialize(Priority::from($matches[1]));
            }, $entry['data']);

            $this->addSql('UPDATE ext_log_entries SET `data` = ? WHERE id = ?', [
                $entry['data'],
                $entry['id']
            ]);
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE device CHANGE room_id room_id INT UNSIGNED DEFAULT NULL, CHANGE type_id type_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE cron_job_result CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE cron_job_id cron_job_id BIGINT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE notification_setting DROP FOREIGN KEY FK_8A6A322FA76ED395');
        $this->addSql('ALTER TABLE notification_setting CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE user_id user_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE notification_setting ADD CONSTRAINT FK_8A6A322FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cron_job CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE data data JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE problem_filter DROP FOREIGN KEY FK_3E582D52A76ED395');
        $this->addSql('ALTER TABLE problem_filter CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE user_id user_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE problem_filter ADD CONSTRAINT FK_3E582D52A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE announcement CHANGE category_id category_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE problem_filter_room CHANGE problem_filter_id problem_filter_id INT NOT NULL');
        $this->addSql('ALTER TABLE notification_setting_problem_type CHANGE notification_setting_id notification_setting_id INT NOT NULL');
        $this->addSql('ALTER TABLE notification_setting_room CHANGE notification_setting_id notification_setting_id INT NOT NULL');
        $this->addSql('ALTER TABLE problem CHANGE device_id device_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE comment CHANGE problem_id problem_id INT UNSIGNED DEFAULT NULL');
    }
}
