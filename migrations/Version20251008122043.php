<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251008122043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE difficulty (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, level INT NOT NULL, description LONGTEXT DEFAULT NULL, color_code VARCHAR(7) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, created_by_user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, base_rules LONGTEXT NOT NULL, min_players INT NOT NULL, max_players INT NOT NULL, default_duration_minutes INT NOT NULL, game_type VARCHAR(50) NOT NULL, is_official TINYINT(1) NOT NULL, image_url VARCHAR(500) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_active TINYINT(1) NOT NULL, INDEX IDX_232B318C7D182D95 (created_by_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_event (id INT AUTO_INCREMENT NOT NULL, session_id INT NOT NULL, player_id INT NOT NULL, event_type VARCHAR(50) NOT NULL, from_position INT DEFAULT NULL, to_position INT DEFAULT NULL, points_scored INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, timestamp_seconds INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_99D7328613FECDF (session_id), INDEX IDX_99D732899E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_position (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, position_number INT NOT NULL, position_name VARCHAR(255) NOT NULL, position_description LONGTEXT DEFAULT NULL, coordinates_x DOUBLE PRECISION DEFAULT NULL, coordinates_y DOUBLE PRECISION DEFAULT NULL, is_final_position TINYINT(1) NOT NULL, points_value INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_ED681BD3E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_session (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, difficulty_id INT NOT NULL, master_user_id INT NOT NULL, session_name VARCHAR(255) NOT NULL, status VARCHAR(50) NOT NULL, started_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', paused_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', completed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', total_duration_seconds INT DEFAULT NULL, notes LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_4586AAFBE48FD905 (game_id), INDEX IDX_4586AAFBFCFA9DAE (difficulty_id), INDEX IDX_4586AAFB7B493EFD (master_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_statistic (id INT AUTO_INCREMENT NOT NULL, session_id INT NOT NULL, player_id INT NOT NULL, shots_attempted INT NOT NULL, shots_made INT NOT NULL, airballs_count INT NOT NULL, bricks_count INT NOT NULL, positions_advanced INT NOT NULL, positions_lost INT NOT NULL, time_played_seconds INT NOT NULL, final_position_reached INT DEFAULT NULL, total_points INT NOT NULL, INDEX IDX_B63D879E613FECDF (session_id), INDEX IDX_B63D879E99E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session_note (id INT AUTO_INCREMENT NOT NULL, session_id INT NOT NULL, note_text LONGTEXT NOT NULL, note_type VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9CCFA3E613FECDF (session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session_participant (id INT AUTO_INCREMENT NOT NULL, session_id INT NOT NULL, user_id INT DEFAULT NULL, player_name VARCHAR(255) NOT NULL, is_anonymous TINYINT(1) NOT NULL, player_number INT NOT NULL, current_position INT NOT NULL, is_active TINYINT(1) NOT NULL, joined_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', left_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2BC67566613FECDF (session_id), INDEX IDX_2BC67566A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, role VARCHAR(50) NOT NULL, avatar_url VARCHAR(500) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C7D182D95 FOREIGN KEY (created_by_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE game_event ADD CONSTRAINT FK_99D7328613FECDF FOREIGN KEY (session_id) REFERENCES game_session (id)');
        $this->addSql('ALTER TABLE game_event ADD CONSTRAINT FK_99D732899E6F5DF FOREIGN KEY (player_id) REFERENCES session_participant (id)');
        $this->addSql('ALTER TABLE game_position ADD CONSTRAINT FK_ED681BD3E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game_session ADD CONSTRAINT FK_4586AAFBE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game_session ADD CONSTRAINT FK_4586AAFBFCFA9DAE FOREIGN KEY (difficulty_id) REFERENCES difficulty (id)');
        $this->addSql('ALTER TABLE game_session ADD CONSTRAINT FK_4586AAFB7B493EFD FOREIGN KEY (master_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE game_statistic ADD CONSTRAINT FK_B63D879E613FECDF FOREIGN KEY (session_id) REFERENCES game_session (id)');
        $this->addSql('ALTER TABLE game_statistic ADD CONSTRAINT FK_B63D879E99E6F5DF FOREIGN KEY (player_id) REFERENCES session_participant (id)');
        $this->addSql('ALTER TABLE session_note ADD CONSTRAINT FK_9CCFA3E613FECDF FOREIGN KEY (session_id) REFERENCES game_session (id)');
        $this->addSql('ALTER TABLE session_participant ADD CONSTRAINT FK_2BC67566613FECDF FOREIGN KEY (session_id) REFERENCES game_session (id)');
        $this->addSql('ALTER TABLE session_participant ADD CONSTRAINT FK_2BC67566A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C7D182D95');
        $this->addSql('ALTER TABLE game_event DROP FOREIGN KEY FK_99D7328613FECDF');
        $this->addSql('ALTER TABLE game_event DROP FOREIGN KEY FK_99D732899E6F5DF');
        $this->addSql('ALTER TABLE game_position DROP FOREIGN KEY FK_ED681BD3E48FD905');
        $this->addSql('ALTER TABLE game_session DROP FOREIGN KEY FK_4586AAFBE48FD905');
        $this->addSql('ALTER TABLE game_session DROP FOREIGN KEY FK_4586AAFBFCFA9DAE');
        $this->addSql('ALTER TABLE game_session DROP FOREIGN KEY FK_4586AAFB7B493EFD');
        $this->addSql('ALTER TABLE game_statistic DROP FOREIGN KEY FK_B63D879E613FECDF');
        $this->addSql('ALTER TABLE game_statistic DROP FOREIGN KEY FK_B63D879E99E6F5DF');
        $this->addSql('ALTER TABLE session_note DROP FOREIGN KEY FK_9CCFA3E613FECDF');
        $this->addSql('ALTER TABLE session_participant DROP FOREIGN KEY FK_2BC67566613FECDF');
        $this->addSql('ALTER TABLE session_participant DROP FOREIGN KEY FK_2BC67566A76ED395');
        $this->addSql('DROP TABLE difficulty');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_event');
        $this->addSql('DROP TABLE game_position');
        $this->addSql('DROP TABLE game_session');
        $this->addSql('DROP TABLE game_statistic');
        $this->addSql('DROP TABLE session_note');
        $this->addSql('DROP TABLE session_participant');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
