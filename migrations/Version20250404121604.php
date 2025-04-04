<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250404121604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE sales DROP FOREIGN KEY FK_6B81704464E7214B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_6B81704464E7214B ON sales
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sales CHANGE department_id_id department_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sales ADD CONSTRAINT FK_6B817044AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6B817044AE80F5DF ON sales (department_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE sales DROP FOREIGN KEY FK_6B817044AE80F5DF
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_6B817044AE80F5DF ON sales
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sales CHANGE department_id department_id_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sales ADD CONSTRAINT FK_6B81704464E7214B FOREIGN KEY (department_id_id) REFERENCES department (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6B81704464E7214B ON sales (department_id_id)
        SQL);
    }
}
