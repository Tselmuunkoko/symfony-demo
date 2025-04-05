<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250405120946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE sales CHANGE department_id department_id INT DEFAULT NULL
        SQL);

        $this->addSql('ALTER TABLE sales DROP FOREIGN KEY FK_6B817044AE80F5DF');
        $this->addSql('ALTER TABLE sales ADD CONSTRAINT FK_6B817044AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE sales CHANGE department_id department_id INT NOT NULL
        SQL);
    }
}
