<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Ldap\Adapter\ExtLdap;

use Symfony\Component\Ldap\Adapter\AdapterInterface;
use Symfony\Component\Ldap\Exception\LdapException;
use Symfony\Component\Ldap\LdapUtils;

/**
 * @author Charles Sarrazin <charles@sarraz.in>
 */
class Adapter implements AdapterInterface
{
    private $config;
    private $connection;
    private $entryManager;

    public function __construct(array $config = array())
    {
        if (!extension_loaded('ldap')) {
            throw new LdapException('The LDAP PHP extension is not enabled.');
        }

        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        if (null === $this->connection) {
            $this->connection = new Connection($this->config);
        }

        return $this->connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntryManager()
    {
        if (null === $this->entryManager) {
            $this->entryManager = new EntryManager($this->getConnection());
        }

        return $this->entryManager;
    }

    /**
     * {@inheritdoc}
     */
    public function createQuery($dn, $query, array $options = array())
    {
        return new Query($this->getConnection(), $dn, $query, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function escape($subject, $ignore = '', $flags = 0)
    {
        return LdapUtils::escape($subject, $ignore, $flags);
    }
}
