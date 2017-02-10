<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Ldap;

/**
 * @author Charles Sarrazin <charles@sarraz.in>
 */
final class LdapExtension
{
    private $type;
    private $value;
    private $critical;

    public function __construct($type, $value, $critical)
    {
        $this->type = $type;
        $this->value = $value;
        $this->critical = $critical;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isCritical()
    {
        return $this->critical;
    }
}
