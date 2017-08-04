<?php
/**
 * Created by PhpStorm.
 * User: nyinyilwin
 * Date: 8/1/17
 * Time: 7:22 PM.
 */

namespace PhpJunior\Laravel2C2P\Encryption;

class Encryption
{
    private $config;

    /**
     * Encryption constructor.
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function pkcs7_123_encrypt($text)
    {
        if (!file_exists(storage_path('tmp/'))) {
            mkdir(storage_path('tmp/'));
        }

        $msg_file = storage_path('tmp/msg.txt');
        $enc_file = storage_path('tmp/enc.txt');
        $dec_file = storage_path('tmp/dec.txt');

        file_put_contents($msg_file, $text);

        $strOri = 'MIME-Version: 1.0
Content-Disposition: attachment; filename="smime.p7m"
Content-Type: application/x-pkcs7-mime; smime-type=enveloped-data; name="smime.p7m"
Content-Transfer-Encoding: base64

';
        $key = file_get_contents($this->config->get('laravel-2c2p.123_public_key_path'));

        if (openssl_pkcs7_encrypt($msg_file, $enc_file, $key, [])) {
            $tempStr = file_get_contents($enc_file);
            $pos = strpos($tempStr, 'base64');
            $tempStr = trim(substr($tempStr, $pos + 6));

            return str_replace($strOri, '', $tempStr);
        } else {
            return 'Cannot Encrypt';
        }
    }

    /**
     * @param $text
     *
     * @return bool|mixed|string
     */
    public function pkcs7_encrypt($text)
    {
        if (!file_exists(storage_path('tmp/'))) {
            mkdir(storage_path('tmp/'));
        }

        $filename = storage_path('tmp/'.time().'.txt');
        $this->text_to_file($text, $filename);
        $filename_enc = storage_path('tmp/'.time().'.enc');

        $key = file_get_contents($this->config->get('laravel-2c2p.public_key_path'));

        if (openssl_pkcs7_encrypt($filename, $filename_enc, $key, [])) {
            unlink($filename);
            if (!$handle = fopen($filename_enc, 'r')) {
                echo "Cannot open file ($filename_enc)";
                exit;
            }

            $contents = fread($handle, filesize($filename_enc));
            fclose($handle);
            $contents = str_replace('MIME-Version: 1.0
Content-Disposition: attachment; filename="smime.p7m"
Content-Type: application/pkcs7-mime; smime-type=enveloped-data; name="smime.p7m"
Content-Transfer-Encoding: base64
', '', $contents);

            $contents = str_replace("\n", '', $contents);
            unlink($filename_enc);

            return $contents;
        } else {
            unlink($filename);
            unlink($filename_enc);
            echo 'ENCRYPT FAIL';
            exit;
        }
    }

    public function pkcs7_decrypt($text)
    {
        $arr = str_split($text, 64);
        $text = '';

        foreach ($arr as $val) {
            $text .= $val."\n";
        }

        $text = 'MIME-Version: 1.0
Content-Disposition: attachment; filename="smime.p7m"
Content-Type: application/pkcs7-mime; smime-type=enveloped-data; name="smime.p7m"
Content-Transfer-Encoding: base64

'.$text;

        $text = rtrim($text, "\n");

        if (!file_exists(storage_path('tmp/'))) {
            mkdir(storage_path('tmp/'));
        }

        $in_file_name = storage_path('tmp/'.time().'.txt');
        $this->text_to_file($text, $in_file_name);
        $out_file_name = storage_path('tmp/'.time().'.dec');
        $public = file_get_contents($this->config->get('laravel-2c2p.public_key_path'));

        $private = [
            file_get_contents($this->config->get('laravel-2c2p.private_key_path')),
            $this->config->get('laravel-2c2p.private_key_pass'),
        ];

        if (openssl_pkcs7_decrypt($in_file_name, $out_file_name, $public, $private)) {
            unlink($in_file_name);
            $content = file_get_contents($out_file_name);
            unlink($out_file_name);

            return $content;
        } else {
            unlink($out_file_name);
            unlink($in_file_name);
            echo 'DECRYPT FAIL';
            exit;
        }
    }

    private function text_to_file($text, $filename)
    {
        if (!$handle = fopen($filename, 'w')) {
            echo "Cannot open file ($filename)";
            exit;
        }

        if (fwrite($handle, $text) === false) {
            echo "Cannot write to file ($filename)";
            exit;
        }
    }
}
