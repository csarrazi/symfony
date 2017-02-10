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
 *
 * A LDAP URL implementation of RFC 2255 and RFC 4516.
 */
final class LdapUrl
{
    const LDAP_URL = '#^((?P<scheme>[^:/?]+):)?(//(?P<host>[^:/?]*)(:(?P<port>[^/?]*))?)?/(?P<dn>[^?\n]*)(\?(?P<attributes>[^?\n]*)(\?(?P<scope>[^?\n]*)(\?(?P<filter>[^?\n]*)(\?(?P<extensions>[^?\n]*))?)?)?)?$#';

    private $scheme;
    private $host;
    private $port;
    private $dn;
    private $attributes;
    private $scope;
    private $filter;
    private $extensions;

    private function __construct() {}

    public static function fromParts($scheme = 'ldap', $host = 'localhost', $port = 389, $dn = '', array $attributes = array('*'), $scope = 'base', $filter = '(objectClass=*)', array $extensions = array())
    {
        $obj = new self();
        $obj->scheme = $scheme;
        $obj->host = $host;
        $obj->port = $port;
        $obj->dn = $dn;
        $obj->attributes = $attributes;
        $obj->scope = $scope;
        $obj->filter = $filter;
        $obj->extensions = $extensions;

        return $obj;
    }

    public static function fromUrl($url)
    {
        $parts = self::getParts($url);

        $scheme = $parts['scheme'] ?: 'ldap';
        $host = $parts['host'] ?: 'localhost';
        $port = (int) $parts['port'] ?: 389;
        $dn = $parts['dn'];

        $attributes = $parts['attributes'] ? explode(',', $parts['attributes']) : array('*');

        $scope = $parts['scope'] ?: 'base';
        $filter = $parts['filter'] ?: '(objectClass=*)';

        $extensions = array();

        if ($parts['extensions']) {
            foreach (explode(',', $parts['extensions']) as $extension) {
                list($type, $value) = explode('=', $extension, 2);
                $isCritical = '!' === $type[0];

                if ($isCritical) {
                    $type = substr($type, 1);
                }

                $extensions[] = new LdapExtension($type, urldecode($value), $isCritical);
            }
        }

        return self::fromParts($scheme, $host, $port, $dn, $attributes, $scope, $filter, $extensions);
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getDn()
    {
        return $this->dn;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @return string
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @return LdapExtension[]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    public function __toString()
    {
        $parts = [];

        $parts[] = sprintf('%s://%s:%d/%s', $this->scheme, $this->host, $this->port, LdapUtils::percentEncode($this->dn));
        $parts[] = (array('*') === $this->attributes || empty($this->attributes)) ? '' : implode(',', $this->attributes);
        $parts[] = ('base' === $this->scope || empty($this->scope)) ? '' : $this->scope;
        $parts[] = '(objectClass=*)' === $this->filter ? '' : LdapUtils::percentEncode($this->filter);
        $parts[] = $this->extensions ? implode(',', array_map(function (LdapExtension $ext) {
            return sprintf('%s%s=%s', $ext->isCritical() ? '!' : '', $ext->getType(), str_replace(',', '%2c', LdapUtils::percentEncode($ext->getValue())));
        }, $this->extensions)) : '';

        return rtrim(implode('?', $parts), '?');
    }

    private function encode($str)
    {
        return '';
    }

    private function decode($str)
    {
        return '';
    }

    private static function getParts($url)
    {
        if (preg_match(self::LDAP_URL, $url, $matches)) {
            $keys = array_fill_keys(array('scheme', 'host', 'port', 'dn', 'attributes', 'scope', 'filter', 'extensions'), null);

            return array_merge($keys, array_intersect_key($matches, $keys));
        }

        throw new \Exception('Invalid LDAP URL: "' . $url . '"');
    }
}
