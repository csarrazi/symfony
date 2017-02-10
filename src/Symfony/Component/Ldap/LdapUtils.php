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
 * Ldap Utilities
 */
class LdapUtils
{
    public static function percentEncode($value)
    {
        if ($value == null) {
            return null;
        }

        $str = '';

        if ('utf-8' !== mb_detect_encoding($value)) {
            $value = utf8_encode($value);
        }

        for ($i = 0; $i < mb_strlen($value); $i++) {
            $ch = $value[$i];
            // uppercase
            if ($ch >= 'A' && $ch <= 'Z') {
                $str .= $ch;
                // lowercase
            } else if ($ch >= 'a' && $ch <= 'z') {
                $str .= $ch;
                // digit
            } else if ($ch >= '0' && $ch <= '9') {
                $str .= $ch;
            } else {
                // unreserved and reserved
                switch ($ch) {
                    case '-':
                    case '.':
                    case '_':
                    case '~':
                    case '!':
                    case '$':
                    case '&':
                    case '\'':
                    case '(':
                    case ')':
                    case '*':
                    case '+':
                    case ',':
                    case ';':
                    case '=':
                        $str .= $ch;
                    break;
                    default:
                        $str .= '%';
                        $str .= bin2hex($ch);
                }
            }
        }

        return $str;
    }

    public static function escape($subject, $ignore = '', $flags = 0)
    {
        $value = ldap_escape($subject, $ignore, $flags);

        // Per RFC 4514, leading/trailing spaces should be encoded in DNs, as well as carriage returns.
        if ((int) $flags & LDAP_ESCAPE_DN) {
            if (!empty($value) && $value[0] === ' ') {
                $value = '\\20'.substr($value, 1);
            }
            if (!empty($value) && $value[strlen($value) - 1] === ' ') {
                $value = substr($value, 0, -1).'\\20';
            }
            $value = str_replace("\r", '\0d', $value);
        }

        return $value;
    }
}