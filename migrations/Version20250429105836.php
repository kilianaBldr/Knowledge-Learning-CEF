<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250429105836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE certification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, cursus_id INT NOT NULL, lesson_id INT DEFAULT NULL, theme_id INT NOT NULL, date_obtained DATETIME NOT NULL, INDEX IDX_6C3C6D75A76ED395 (user_id), INDEX IDX_6C3C6D7540AEF4B9 (cursus_id), INDEX IDX_6C3C6D75CDF80196 (lesson_id), INDEX IDX_6C3C6D7559027487 (theme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE cursus (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, theme_id INT NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_255A0C3DE12AB56 (created_by), INDEX IDX_255A0C316FE72E1 (updated_by), INDEX IDX_255A0C359027487 (theme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lessons (id INT AUTO_INCREMENT NOT NULL, cursus_id INT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, video_file VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_3F4218D940AEF4B9 (cursus_id), INDEX IDX_3F4218D9DE12AB56 (created_by), INDEX IDX_3F4218D916FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE theme (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_9775E708DE12AB56 (created_by), INDEX IDX_9775E70816FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, name VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, token_registration_life_time DATETIME NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_lessons (user_id INT NOT NULL, lessons_id INT NOT NULL, INDEX IDX_674F06D3A76ED395 (user_id), INDEX IDX_674F06D3FED07355 (lessons_id), PRIMARY KEY(user_id, lessons_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_cursus (user_id INT NOT NULL, cursus_id INT NOT NULL, INDEX IDX_6707BBFEA76ED395 (user_id), INDEX IDX_6707BBFE40AEF4B9 (cursus_id), PRIMARY KEY(user_id, cursus_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D75A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D7540AEF4B9 FOREIGN KEY (cursus_id) REFERENCES cursus (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D75CDF80196 FOREIGN KEY (lesson_id) REFERENCES lessons (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D7559027487 FOREIGN KEY (theme_id) REFERENCES theme (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cursus ADD CONSTRAINT FK_255A0C3DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cursus ADD CONSTRAINT FK_255A0C316FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cursus ADD CONSTRAINT FK_255A0C359027487 FOREIGN KEY (theme_id) REFERENCES theme (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lessons ADD CONSTRAINT FK_3F4218D940AEF4B9 FOREIGN KEY (cursus_id) REFERENCES cursus (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lessons ADD CONSTRAINT FK_3F4218D9DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lessons ADD CONSTRAINT FK_3F4218D916FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE theme ADD CONSTRAINT FK_9775E708DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE theme ADD CONSTRAINT FK_9775E70816FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lessons ADD CONSTRAINT FK_674F06D3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lessons ADD CONSTRAINT FK_674F06D3FED07355 FOREIGN KEY (lessons_id) REFERENCES lessons (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_cursus ADD CONSTRAINT FK_6707BBFEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_cursus ADD CONSTRAINT FK_6707BBFE40AEF4B9 FOREIGN KEY (cursus_id) REFERENCES cursus (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D75A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D7540AEF4B9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D75CDF80196
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D7559027487
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cursus DROP FOREIGN KEY FK_255A0C3DE12AB56
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cursus DROP FOREIGN KEY FK_255A0C316FE72E1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cursus DROP FOREIGN KEY FK_255A0C359027487
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lessons DROP FOREIGN KEY FK_3F4218D940AEF4B9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lessons DROP FOREIGN KEY FK_3F4218D9DE12AB56
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lessons DROP FOREIGN KEY FK_3F4218D916FE72E1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE theme DROP FOREIGN KEY FK_9775E708DE12AB56
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE theme DROP FOREIGN KEY FK_9775E70816FE72E1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lessons DROP FOREIGN KEY FK_674F06D3A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lessons DROP FOREIGN KEY FK_674F06D3FED07355
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_cursus DROP FOREIGN KEY FK_6707BBFEA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_cursus DROP FOREIGN KEY FK_6707BBFE40AEF4B9
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE certification
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cursus
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lessons
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE theme
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_lessons
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_cursus
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
