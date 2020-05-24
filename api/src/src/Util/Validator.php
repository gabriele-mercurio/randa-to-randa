<?php

namespace App\Util;

use App\Repository\MediaRepository;

class Validator
{
    /** @var MediaRepository */
    private $mediaRepository;

    /** @var Swift_Mailer */
    private $swift_Mailer;

    public function __construct(MediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Generic link validator
     *
     * @param string $link
     * @return bool
     */
    public static function validateLink(string $link)
    {
        return !!preg_match("/^(?:http(?:s)?:\/\/)?[a-z0-9]+(?:\.[\w.\-]+)+[\w-.,:;'~\/&#@[\]\(\)\!\?\=\$\*\+]+$/mi", $link);
    }

    public function validateEmail(string $email)
    {
        return false !== \filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function validateMedia(array $media): bool
    {
        foreach ($media as $m) {
            if (!is_array($m)) {
                return false;
            }

            $keys = array_keys($m);
            if (!in_array("id", $keys)) {
                return false;
            } else {
                $id = $m['id'];
                $media = $this->mediaRepository->find($id);
                if(empty($media)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Generic phone number validator
     *
     * @param string $phoneNumbers
     * @return bool
     */
    public static function validatePhoneNumber(string $phoneNumbers)
    {
        return !!preg_match("/^(?:[+]*[(]{0,1}[\d]{1,4}[)]{0,1})?[-\s\d]{4,}$/mi", $phoneNumbers);
    }

    /**
     * Generic slug validator
     *
     * @param string $slug
     * @return bool
     */
    public static function validateSlug(string $slug)
    {
        return !!preg_match("/^[\w-]{3,255}$/", $slug);
    }

    /**
     * priceType validator
     *
     * @param string $priceType
     * @return bool
     */
    public static function validatePriceType(string $priceType)
    {
        return !!preg_match("/^(unit|m)$/", $priceType);
    }
}
