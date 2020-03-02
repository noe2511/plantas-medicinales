<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200302172040 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE producto_has_usomedico');
        $this->addSql('ALTER TABLE producto DROP imagen');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE producto_has_usomedico (producto_idProducto INT NOT NULL, usomedico_idUsoMedico INT NOT NULL, INDEX fk_producto_has_usomedico_usomedico1_idx (usomedico_idUsoMedico), INDEX fk_producto_has_usomedico_producto1_idx (producto_idProducto), PRIMARY KEY(producto_idProducto, usomedico_idUsoMedico)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE producto_has_usomedico ADD CONSTRAINT fk_producto_has_usomedico_producto1 FOREIGN KEY (producto_idProducto) REFERENCES producto (idProducto) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE producto_has_usomedico ADD CONSTRAINT fk_producto_has_usomedico_usomedico1 FOREIGN KEY (usomedico_idUsoMedico) REFERENCES usomedico (idUsoMedico) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE producto ADD imagen VARCHAR(250) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
    }
}
