<?php

namespace App\Services;

class TotpService
{
    private const BASE32_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a random base32 secret string (16 chars = 80 bits).
     */
    public function generateSecret(int $length = 16): string
    {
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= self::BASE32_CHARS[random_int(0, 31)];
        }
        return $secret;
    }

    /**
     * Calculate TOTP code for a given secret and timestamp.
     */
    public function getCode(string $secret, ?int $timestamp = null, int $timeStep = 30, int $digits = 6): string
    {
        $timestamp = $timestamp ?? time();
        $counter = (int) floor($timestamp / $timeStep);
        $binaryCounter = pack('N*', 0) . pack('N*', $counter);

        $binarySecret = $this->base32Decode($secret);
        $hash = hash_hmac('sha1', $binaryCounter, $binarySecret, true);

        $offset = ord(substr($hash, -1)) & 0x0F;
        $hashPart = substr($hash, $offset, 4);
        $value = unpack('N', $hashPart)[1] & 0x7FFFFFFF;

        $mod = 10 ** $digits;
        return str_pad((string) ($value % $mod), $digits, '0', STR_PAD_LEFT);
    }

    /**
     * Verify a 6-digit TOTP code against a secret with a window tolerance.
     */
    public function verify(string $secret, string $code, int $window = 1, ?int $timestamp = null): bool
    {
        $timestamp = $timestamp ?? time();
        $code = trim($code);

        for ($i = -$window; $i <= $window; $i++) {
            $testTime = $timestamp + ($i * 30);
            if (hash_equals($this->getCode($secret, $testTime), $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate OTPAuth URI for Google Authenticator / Microsoft Authenticator.
     */
    public function getOtpAuthUri(string $company, string $accountName, string $secret): string
    {
        $encodedCompany = rawurlencode($company);
        $encodedAccount = rawurlencode($accountName);
        return "otpauth://totp/{$encodedCompany}:{$encodedAccount}?secret={$secret}&issuer={$encodedCompany}&algorithm=SHA1&digits=6&period=30";
    }

    /**
     * Generate 8 recovery backup codes (8 characters each).
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(4)));
        }
        return $codes;
    }

    /**
     * Aliases for convenience across controllers and tests.
     */
    public function getQrCodeUrl(string $accountName, string $secret, string $company = 'Bệnh Viện Tâm Anh'): string
    {
        return $this->getOtpAuthUri($company, $accountName, $secret);
    }

    public function getCurrentCode(string $secret): string
    {
        return $this->getCode($secret);
    }

    public function verifyCode(string $secret, string $code): bool
    {
        return $this->verify($secret, $code);
    }

    /**
     * Decode a base32 string into binary data.
     */
    private function base32Decode(string $b32): string
    {
        $b32 = strtoupper(preg_replace('/[^A-Z2-7]/', '', $b32));
        $buffer = 0;
        $bitsLeft = 0;
        $output = '';

        $length = strlen($b32);
        for ($i = 0; $i < $length; $i++) {
            $val = strpos(self::BASE32_CHARS, $b32[$i]);
            if ($val === false) {
                continue;
            }
            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $output;
    }
}
