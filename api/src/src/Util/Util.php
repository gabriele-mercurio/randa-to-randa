<?php

namespace App\Util;

use App\Entity\Region;
use App\Entity\User;
use App\Repository\DirectorRepository;
use App\Repository\UserRepository;
use DateTime;
use DateTimeZone;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * Generate a string of random chars and symbols of a given length
     *
     * @param int $length The length of the returned string;
     * @return string
     */
    public static function generatePassword(int $length = 12): string
    {
        $password = "";
        $availableCharacters = [
            "!@#$%^&",
            "abcdefghijkmnpqrstuvwxyz",
            "123456789",
            "ABCDEFGHJKLMNPQRSTUVWXYZ"
        ];

        for($i = 0; $i < $length; $i++) {
            $group = $availableCharacters[mt_rand(0, count($availableCharacters) - 1)];
            $pos = mt_rand(0, strlen($group) - 1);
            $password .= $group[$pos];
        }
        return $password;
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
     * Check if the given user is admin, can impersonate the user defined by $actAsId,
     * can assume the given $role and check if it is in the role range defined by $roleCheck
     *
     * @param User                  $user               Required. The user to check for
     * @param Region|null           $region             Required. The region to check for; pass null only if want to check for national director
     * @param array                 $roleCheck          Required. An array of roles to check user for
     * @param UserRepository        $userRepository     Required. The User doctrine repository
     * @param DirectorRepository    $directorRepository Required. The Director doctrine repository
     * @param string|null           $actAsId            Optional. The id of the user to impersonate
     * @param string|null           $role               Optional. The role to assume
     *
     * @return array    An array with the following keys:
     *                      'actAs':    the impersoned user or null;
     *                      'code':     an http response code to return by the caller controller function;
     *                      'director': the representing director object or null;
     *                      'isAdmin':  a boolean meanings if the given user is an admin;
     *                      'role':     the real role of the performer;
     */
    public static function getPerformerData(User $user, ?Region $region, array $roleCheck, UserRepository $userRepository, DirectorRepository $directorRepository, ?string $actAsId = null, ?string $role = null): array
    {
        $isAdmin = $user->isAdmin() && is_null($actAsId);
        $director = null;

        $checkUser = $userRepository->checkUser($user, $actAsId);
        $actAs = static::arrayGetValue($checkUser, 'user');
        $code = static::arrayGetValue($checkUser, 'code');

        if ($code == Response::HTTP_OK && !$isAdmin) {
            $u = is_null($actAsId) ? $user : $actAs;
            
            $dir = $directorRepository->findOneBy([
                "user" => $user,
                "region" => $region
            ]);
            $role = $dir->getRole();
            $checkDirectorRole = $directorRepository->checkDirectorRole($u, $region, $role);

            $code = static::arrayGetValue($checkDirectorRole, 'code', $code);
            $director = static::arrayGetValue($checkDirectorRole, 'director', null);
            $role = $director ? $director->getRole() : $role;
        }

        if ($code == Response::HTTP_OK) {
            $role = $isAdmin ? Constants::ROLE_EXECUTIVE : $role;
            if (!in_array($role, $roleCheck)) {
                $code = Response::HTTP_FORBIDDEN;
            }
        }

        return [
            'actAS'     => $actAs,
            'code'      => $code,
            'director'  => $director,
            'isAdmin'   => $isAdmin,
            'role'      => $role
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

    public static function normalizeRequest(Request $request): Request
    {
        $content = str_replace(["\n", "\t"], "", $request->getContent());

        if (!empty($content) && empty($request->request->all())) {
            $allowedContentTypes = [
                'application/json',
                'application/x-json'
            ];
            $allowedRequestMethods = [
                'POST',
                'PUT',
                'DELETE',
                'PATCH'
            ];
            $contentType = $request->headers->get('CONTENT_TYPE');
            $requestMethod = strtoupper($request->server->get('REQUEST_METHOD', 'GET'));

            if (in_array($contentType, $allowedContentTypes) && in_array($requestMethod, $allowedRequestMethods)) {
                $newRequest = json_decode($content, true);
                if (!is_null($newRequest) && is_array($newRequest)) {
                    $query = $request->query->all();
                    $newRequest = array_merge($request->request->all(), $newRequest);
                    $attributes = $request->attributes->all();
                    $cookies = $request->cookies->all();
                    $files = $request->files;
                    $server = $request->server->all();
                    $request = new Request($query, $newRequest, $attributes, $cookies, [], $server, $content);
                    $request->files = $files;
                }
            }
        }

        return $request;
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


    public static function getTimeslotFromCurrentMonth() {
        $month = date("m");
        $numeric_timeslot = floor($month / 3);
        header("timeslot: " . $month . "-" . $numeric_timeslot);
        return "T" . $numeric_timeslot;
    }
}
