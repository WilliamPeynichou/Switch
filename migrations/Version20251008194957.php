<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251008194957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game_history (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, game_id INT NOT NULL, session_name VARCHAR(255) NOT NULL, total_participants INT NOT NULL, finalist_id INT NOT NULL, finalist_name VARCHAR(255) DEFAULT NULL, winner_id INT NOT NULL, winner_name VARCHAR(255) DEFAULT NULL, total_duration_seconds INT NOT NULL, game_notes LONGTEXT DEFAULT NULL, started_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', completed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B2780F64A76ED395 (user_id), INDEX IDX_B2780F64E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_history ADD CONSTRAINT FK_B2780F64A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE game_history ADD CONSTRAINT FK_B2780F64E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_history DROP FOREIGN KEY FK_B2780F64A76ED395');
        $this->addSql('ALTER TABLE game_history DROP FOREIGN KEY FK_B2780F64E48FD905');
        $this->addSql('DROP TABLE game_history');
    }
}
