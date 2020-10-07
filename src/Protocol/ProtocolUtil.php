<?php

declare(strict_types=1);

namespace Longyan\Kafka\Protocol;

use Longyan\Kafka\Protocol\Type\Int32;
use Longyan\Kafka\Protocol\Type\Int64;
use Longyan\Kafka\Protocol\Type\UInt32;

class ProtocolUtil
{
    /**
     * platform is big endian.
     *
     * @var bool
     */
    private static $nativeIsBigEndian;

    public static function nativeIsBigEndian(): bool
    {
        if (null === self::$nativeIsBigEndian) {
            self::$nativeIsBigEndian = pack('L', 1) === pack('N', 1);
        }

        return self::$nativeIsBigEndian;
    }

    public static function int32(int $value): int
    {
        if ($value < Int32::MIN_VALUE || $value > Int32::MAX_VALUE) {
            return unpack('l', pack('l', $value))[1];
        } else {
            return $value;
        }
    }

    public static function uint32(int $value): int
    {
        if ($value < UInt32::MIN_VALUE || $value > UInt32::MAX_VALUE) {
            return unpack('N', pack('N', $value))[1];
        } else {
            return $value;
        }
    }

    public static function int64(int $value): int
    {
        if ($value < Int64::MIN_VALUE || $value > Int64::MAX_VALUE) {
            return unpack('l', pack('l', $value))[1];
        } else {
            return $value;
        }
    }

    /**
     * unsigned int32 >>.
     *
     * @param mixed  $x
     * @param string $bits
     *
     * @return mixed
     */
    public static function shr32($x, $bits)
    {
        if ($bits <= 0) {
            return $x;
        }
        if ($bits >= 32) {
            return 0;
        }
        $bin = decbin($x);
        $l = \strlen($bin);
        if ($l > 32) {
            $bin = substr($bin, $l - 32, 32);
        } elseif ($l < 32) {
            $bin = str_pad($bin, 32, '0', \STR_PAD_LEFT);
        }

        return bindec(str_pad(substr($bin, 0, 32 - $bits), 32, '0', \STR_PAD_LEFT));
    }

    /**
     * unsigned int32 <<.
     *
     * @param mixed  $x
     * @param string $bits
     *
     * @return mixed
     */
    public static function shl32($x, $bits)
    {
        if ($bits <= 0) {
            return $x;
        }
        if ($bits >= 32) {
            return 0;
        }
        $bin = decbin($x);
        $l = \strlen($bin);
        if ($l > 32) {
            $bin = substr($bin, $l - 32, 32);
        } elseif ($l < 32) {
            $bin = str_pad($bin, 32, '0', \STR_PAD_LEFT);
        }

        return bindec(str_pad(substr($bin, $bits), 32, '0', \STR_PAD_RIGHT));
    }
}
