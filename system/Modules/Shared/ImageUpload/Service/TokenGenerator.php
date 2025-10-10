<?php

namespace Modules\Shared\ImageUpload\Service;

class TokenGenerator
{
    public static function randomString($type = 'alnum', $len = 12)
    {
        switch($type)
        {
            case 'basic'	: return mt_rand();
                break;
            case 'alnum'	:
            case 'numeric'	:
            case 'nozero'	:
            case 'alpha'	:

                switch ($type)
                {
                    case 'alpha'	:	$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'alnum'	:	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'numeric'	:	$pool = '0123456789';
                        break;
                    case 'nozero'	:	$pool = '123456789';
                        break;
                }

                $str = '';
                for ($i=0; $i < $len; $i++)
                {
                    $str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
                }
                return $str;
                break;
            case 'unique'	:
            case 'md5'		:
            default:
                return md5(uniqid(mt_rand()));
                break;
        }
    }
}
