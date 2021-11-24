# Audit bundle

This bundle creates an audit log for all doctrine ORM database related changes:

- inserts ion changes, association and dissociation actions.
- if there is an user and updates including their diffs and relation field diffs.
- many to many relatin token storage, it will link him to the log.
- the audit entries are inserted within the same transaction during **flush**,
if something fails the state remains clean.

Basically you can track any change from these log entries if they were
managed through standard **ORM** operations.

**NOTE:** audit cannot track DQL or direct SQL updates or delete statement executions.

## Install

First, install it with composer:

    composer require nti/audit-bundle

Then, add it in your **AppKernel** bundles.

    // app/AppKernel.php
    public function registerBundles()
    {
        $bundles = array(
            ...
            new DataDog\AuditBundle\DataDogAuditBundle(),
            ...
        );
        ...
    }

Using Query:

    //SQL
    CREATE TABLE audit_logs (id INT AUTO_INCREMENT NOT NULL, source_id INT NOT NULL, target_id INT DEFAULT NULL, blame_id INT DEFAULT NULL, action VARCHAR(12) NOT NULL, tbl VARCHAR(128) NOT NULL, diff LONGTEXT DEFAULT NULL COMMENT'(DC2Type:json_array)', logged_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_D62F2858953C1C61 (source_id), UNIQUE INDEX UNIQ_D62F2858158E0B66 (target_id), UNIQUE INDEX UNIQ_D62F28588C082A2E (blame_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB;
    CREATE TABLE audit_associations (id INT AUTO_INCREMENT NOT NULL, typ VARCHAR(128) NOT NULL, tbl VARCHAR(128) DEFAULT NULL, label VARCHAR(255) DEFAULT NULL, fk VARCHAR(255) NOT NULL, class VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB;
    CREATE TABLE audit_request (id INT AUTO_INCREMENT NOT NULL, method VARCHAR(255) DEFAULT NULL, controller VARCHAR(255) DEFAULT NULL, route VARCHAR(255) DEFAULT NULL, route_params LONGTEXT DEFAULT NULL, ip VARCHAR(255) DEFAULT NULL, user_name VARCHAR(255) DEFAULT NULL,portal VARCHAR(255) DEFAULT NULL, query_data LONGTEXT DEFAULT NULL, data LONGTEXT DEFAULT NULL, created_on DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB;     ALTER TABLE audit_logs ADD CONSTRAINT FK_D62F2858953C1C61 FOREIGN KEY (source_id) REFERENCES audit_associations (id);
    ALTER TABLE audit_logs ADD CONSTRAINT FK_D62F2858158E0B66 FOREIGN KEY (target_id) REFERENCES audit_associations (id);
    ALTER TABLE audit_logs ADD CONSTRAINT FK_D62F28588C082A2E FOREIGN KEY (blame_id) REFERENCES audit_associations(id);
    ALTER TABLE audit_associations ADD created_on DATETIME NOT NULL;

Using Doctrine Schema:
    
    // Doctrine
    php bin/console doctrine:schema:update -f

### Config

Enable the audit request in the project:

    // app/config.yml
    # DataDog
    data_dog_audit:
        audit_request:
            enabled: true

If you will use a custom database connection use following config:

    // app/config.yml
    # DataDog
    data_dog_audit:
        database:
            connection_name: nti_logs    
        audit_request:
            enabled: true

See [How to Work with multiple Entity Managers and Connections](https://symfony.com/doc/3.4/doctrine/multiple_entity_managers.html "https://symfony.com/doc/3.4/doctrine/multiple_entity_managers.html")

### Annotations

Annotations has to use in the controller, add Annotations NTIAudit in the class:

    //src/Bundle/Controller/Controller.php
    use DataDog\AuditBundle\Annotations\NTIAudit;

    /**
    * Class Controller
    * @package AppBundle\Controller
    * @NTIAudit()
    * @Route("/controller")
    */
    class Controller extends Controller {
        //TODO CODE HERE
    }

### Unaudited Entities

Sometimes, you might not want to create audit log entries for particular entities.
You can achieve this by listing those entities under the `unaudited_entities` configuration
key in your `config.yml`, for example:

    data_dog_audit:
        unaudited_entities:
            - AppBundle\Entity\NoAuditForThis

### Specify Audited Entities 

Sometimes, it is also possible, that you want to create audit log entries only for particular entities. You can achieve it quite similar to unaudited entities. You can list them under the `audited_entities` configuration key in your `config.yml`, for example:

    data_dog_audit:
        audited_entities:
            - AppBundle\Entity\AuditForThis

You can specify either audited or unaudited entities. If both are specified, only audited entities would be taken into account.

### Command

For delete audit data:

    php bin/console nti:audit:delete {qtyDays}

## License

NTI