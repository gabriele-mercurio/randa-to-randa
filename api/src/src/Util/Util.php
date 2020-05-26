<?php

namespace App\Util;

use DateTime;
use DateTimeZone;
use Webmozart\PathUtil\Path;

class Util
{
    /**
     * Return the value of the key(s)/property(es) inside the given arry/object or default.
     * Nested key(s)/poperty(es) must be coma separated inside the $key argument: to get the value
     * of $arr['key1']['key2'], $key must be "key1,key2"
     *
     * @param array|object $arr
     * @param string $key
     * @param mixed $default    Optional value that specifies the default value to return if undefined key(s)/property(es)
     *                          are searched inside $arr. Defaults to NULL
     * @return mixed    The searched value or $default
     */
    public static function arrayGetSubItem(&$arr, $key, $default = NULL)
    {
        $parts = explode(",", $key);
        $vett = &$arr;

        for ($p = 0; $p < count($parts); $p++) {
            if (is_object($vett)) {
                $member_name = $parts[$p];
                $func = method_exists($vett, $member_name);
                $retval = ($func ? @$vett->$member_name() : @$vett->$member_name) or NULL;

                if (is_null($retval)) {
                    return $default;
                }

                if ($p == count($parts) - 1) {
                    return $func ? $vett->$member_name() : $vett->$member_name;
                } else {
                    if ($func) {
                        $vett = &$vett->$member_name();
                    } else {
                        $vett = &$vett->$member_name;
                    }
                }
            } else {
                if (is_array($vett) && array_key_exists($parts[$p], $vett)) {
                    if ($p == count($parts) - 1) {
                        return $vett[$parts[$p]];
                    } else {
                        $vett = &$vett[$parts[$p]];
                    }
                } else {
                    return $default;
                }
            }
        }
    }

    /**
     * Return the value of the key(s)/property(es) inside the given arry/object or default.
     * Nested key(s)/poperty(es) must be coma separated inside the $key argument: to get the value
     * of $arr['key1']['key2'], $key must be "key1,key2"
     *
     * @param array|object $arr
     * @param string $key
     * @param mixed $default    Optional value that specifies the default value to return if undefined key(s)/property(es)
     *                          are searched inside $arr. Defaults to ""
     */
    public static function arrayGetValue($arr, $key, $default = "")
    {
        return static::arrayGetSubItem($arr, $key, $default);
    }

    /**
     * Builds an array with correct $_FILES fields or relative defaults
     *
     * @param string $file_name
     * @return array
     */
    public static function cleanFile($file_name): array
    {
        $file['name'] = isset($_FILES[$file_name]['name']) ? $_FILES[$file_name]['name'] : "";
        $file['type'] = isset($_FILES[$file_name]['type']) ? $_FILES[$file_name]['type'] : "text/plain";
        $file['tmp_name'] = isset($_FILES[$file_name]['tmp_name']) ? $_FILES[$file_name]['tmp_name'] : "";
        $file['error'] = isset($_FILES[$file_name]['error']) ? $_FILES[$file_name]['error'] : UPLOAD_ERR_NO_FILE;
        $file['size'] = isset($_FILES[$file_name]['size']) ? $_FILES[$file_name]['size'] : 0;
        return $file;
    }

    /**
     * Check if a given string ends with a given substring.
     * Recommended version, using strpos
     *
     * @param string $where The string to search in;
     * @param string $what The string to search for;
     * @return bool
     */
    public static function endsWith($where, $what): bool
    {
        return strrpos($where, $what) === strlen($where) - strlen($what);
    }

    /**
     * Generate a string of random chars of a given length
     *
     * @param int $length The length of the returned string;
     * @return string
     */
    public static function generateCode(int $length = 12): string
    {
        $o = "";
        for($i = 0; $i < ceil($length / 2); $i++) {
            $o .= dechex(mt_rand(0, 255));
        }
        return substr($o, 0, $length);
    }

    /**
     * @return string[]
     */
    public static function getAllowedContactTypes()
    {
        return [
            "facebook",
            "twitter",
            "instagram",
            "skype",
            "phone",
            "telegram",
            "mobile",
            "email",
            "pinterest"
        ];
    }

    /**
     * Return the real path of the uploaded files directory appending the subfolder if given
     * @param string $subfolder Optional string that identifies the subfolder; defaults to ""
     * @return string
     */
    public static function getUploadedFilesDirectory($subfolder = ""): string
    {
        $path = Path::join(dirname(__FILE__), "..", "..", "public", "resources");
        if (strlen($subfolder)) {
            $path = Path::join($path, $subfolder);
        }

        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }

        return realpath($path);
    }

    public static function getUploadedRelativeURLDirectory($subfolder = ""): string
    {
        $url = "resources";
        if (strlen($subfolder)) {
            $url = Path::join($url, $subfolder);
        }

        return $url;
    }

    /**
     * Return the base URL of the uploaded files directory appending the subfolder if given
     * @param string $subfolder Optional string that identifies the subfolder; defaults to ""
     * @return string
     */
    public static function getUploadedURLDirectory($subfolder = ""): string
    {
        $protocol = strtoupper(!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : $_SERVER["SERVER_PROTOCOL"]);
        $host = !empty($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER["SERVER_NAME"];

        $hosts = explode(", ", $host);
        $host = $hosts[count($hosts) - 1];

        $url = Path::join($host, static::getUploadedRelativeURLDirectory($subfolder));

        return (substr($protocol, 0, 5) == "HTTPS" ? "https" : "http") . "://" . $url;
    }

    public static function translateRecaptchaErrors(array $errors): string
    {
        if (empty($errors)) {
            return "";
        }

        $result = [];
        foreach ($errors as $error) {
            if (in_array($error, [
                "invalid-input-response",
                "missing-input-response",
                "timeout-or-duplicate"
            ])) {
                $result[] = "Invio bloccato a causa di recaptcha mancante, errato o già utilizzato. Si prega ricaricare la pagina e reinviare il form.";
            } else {
                $result[] = "Si è verificato un errore interno al server. Si prega riprovare più tardi.";
                error_log("RECAPTCHA ERROR: The recaptcha secret or the request are missing, invalid or malformed! ($error)");
            }
        }

        return implode("; ", array_unique($result));
    }

    /**
     * Get the UTC DateTime of a given localized moment
     *
     * @param string $moment
     * @return DateTime
     */
    public static function UTCDateTime($moment = "now"): DateTime
    {
        $tz = new DateTimeZone("UTC");
        return new DateTime($moment, $tz);
    }

    public static function verifyRecaptchaToken(string $token): array
    {
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $data = http_build_query([
            'secret' => $_ENV['GOOGLE_RECAPTCHA_SECRET'],
            'response' => $token
        ]);
        $header = [
            "Content-Type: application/x-www-form-urlencoded",
            "Content-Length: " . strlen($data)
        ];
        $options = [
            'content' => $data,
            'header' => implode("\r\n", $header),
            'method' => 'POST'
        ];
        $context = stream_context_create([
            'http' => $options
        ]);

        return json_decode(file_get_contents($url, false, $context), true);
    }
}
