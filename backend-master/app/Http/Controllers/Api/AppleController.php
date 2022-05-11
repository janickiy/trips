<?php 

namespace App\Http\Controllers\Api;

use Firebase\JWT\JWK;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Socialite\Two\InvalidStateException;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Rsa\Sha256;

// Важно!
use Lcobucci\JWT\Signer\Key\InMemory;

use App\Http\Controllers\Api\JWTParserController;

class AppleController
{
    private const URL = 'https://appleid.apple.com';
   
    /**
     * Verify Apple jwt.
     *
     * @param string $jwt
     *
     * @return bool
     *
     * @see https://appleid.apple.com/auth/keys
     */
    public static function verify($jwt)
    {
        $signer = new Sha256();

        $token = (new Parser())->parse((string) $jwt);
       // $token2 = (new JWTParserController())->parse((string) $jwt);
        //dd($token, $token2->test());

        if ($token->getClaim('iss') !== self::URL) {
            throw new InvalidStateException('Invalid Issuer', Response::HTTP_UNAUTHORIZED);
        }
        if ($token->isExpired(new \DateTime('NOW'))) {
            throw new InvalidStateException('Token Expired', Response::HTTP_UNAUTHORIZED);
        }

        // Кеш в хранилище на диске:
        $data = Cache::remember('socialite:Apple-JWKSet', 5 * 60, function () {
            return json_decode(file_get_contents(self::URL.'/auth/keys'), true);
        });
        
        
        $public_keys = JWK::parseKeySet($data); 

        $signature_verified = false;
         
        
        foreach ($public_keys as $res) {
            $publicKey = openssl_pkey_get_details($res);

            
            if ($token->verify($signer, InMemory::plainText($publicKey['key']))) {
                $signature_verified = true;
            }
        }
        if (!$signature_verified) {
            throw new InvalidStateException('Invalid JWT Signature');
        }

        return true;
    }
   
    
  
}
