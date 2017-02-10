<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Ldap\Tests;

use Symfony\Component\Ldap\LdapExtension;
use Symfony\Component\Ldap\LdapUrl;
use PHPUnit\Framework\TestCase;

/**
 * @author Charles Sarrazin <charles@sarraz.in>
 */
class LdapUrlTest extends TestCase
{
    /**
     * @dataProvider provideTestCases
     */
    public function testLdapUrl($url, $scheme, $host, $port, $dn, $attributes, $scope, $filter, $extensions)
    {
        $ldapUrl = LdapUrl::fromUrl($url);

        $this->assertEquals($scheme, $ldapUrl->getScheme());
        $this->assertEquals($host, $ldapUrl->getHost());
        $this->assertEquals($port, $ldapUrl->getPort());
        $this->assertEquals($dn, $ldapUrl->getDn());
        $this->assertEquals($attributes, $ldapUrl->getAttributes());
        $this->assertEquals($scope, $ldapUrl->getScope());
        $this->assertEquals($filter, $ldapUrl->getFilter());
        $this->assertEquals($extensions, $ldapUrl->getExtensions());
        $this->assertEquals($url, (string)$ldapUrl);
    }

    public function provideTestCases()
    {
        return array(
            array(
                'ldap://symfony.com:1234/cn=fabien,o=symfony',
                'ldap',
                'symfony.com',
                1234,
                'cn=fabien,o=symfony',
                array('*'),
                'base',
                '(objectClass=*)',
                array(),
            ),
            array(
                'ldap://symfony.com:1234/cn=fabien,o=symfony?hello',
                'ldap',
                'symfony.com',
                1234,
                'cn=fabien,o=symfony',
                array('hello'),
                'base',
                '(objectClass=*)',
                array(),
            ),
            array(
                'ldap://symfony.com:1234/cn=fabien,o=symfony?hello',
                'ldap',
                'symfony.com',
                1234,
                'cn=fabien,o=symfony',
                array('hello'),
                'base',
                '(objectClass=*)',
                array(),
            ),
            array(
                'ldap://symfony.com:1234/cn=fabien,o=symfony?hello,world?one',
                'ldap',
                'symfony.com',
                1234,
                'cn=fabien,o=symfony',
                array('hello', 'world'),
                'one',
                '(objectClass=*)',
                array(),
            ),
            array(
                'ldap://symfony.com:1234/cn=fabien,o=symfony?foo,bar,baz?sub',
                'ldap',
                'symfony.com',
                1234,
                'cn=fabien,o=symfony',
                array('foo', 'bar', 'baz'),
                'sub',
                '(objectClass=*)',
                array(),
            ),
            array(
                'ldap://symfony.com:1234/cn=fabien,o=symfony?foo,bar,baz??(&(objectclass=person)(ou=Maintainers))',
                'ldap',
                'symfony.com',
                1234,
                'cn=fabien,o=symfony',
                array('foo', 'bar', 'baz'),
                'base',
                '(&(objectclass=person)(ou=Maintainers))',
                array(),
            ),
            array(
                'ldap://symfony.com:1234/cn=fabien,o=symfony?foo,bar,baz??test?bindname=cn=Fabien%20Potencier%2cdc=symfony%2cdc=com',
                'ldap',
                'symfony.com',
                1234,
                'cn=fabien,o=symfony',
                array('foo', 'bar', 'baz'),
                'base',
                'test',
                array(
                    new LdapExtension('bindname', 'cn=Fabien Potencier,dc=symfony,dc=com', false),
                ),
            ),
            array(
                'ldap://symfony.com:1234/cn=fabien,o=symfony????bindname=cn=Fabien%20Potencier%2cdc=symfony%2cdc=com',
                'ldap',
                'symfony.com',
                1234,
                'cn=fabien,o=symfony',
                array('*'),
                'base',
                '(objectClass=*)',
                array(
                    new LdapExtension('bindname', 'cn=Fabien Potencier,dc=symfony,dc=com', false),
                ),
            ),
            array(
                'ldap://symfony.com:1234/cn=fabien,o=symfony???test?!bindname=cn=Fabien%20Potencier%2cdc=symfony%2cdc=com,test=12',
                'ldap',
                'symfony.com',
                1234,
                'cn=fabien,o=symfony',
                array('*'),
                'base',
                'test',
                array(
                    new LdapExtension('bindname', 'cn=Fabien Potencier,dc=symfony,dc=com', true),
                    new LdapExtension('test', '12', false),
                ),
            ),
        );
    }
}
