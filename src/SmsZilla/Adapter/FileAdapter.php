<?php
/**
 * SmsZilla
 * PHP library for sending SMS through various gateways.
 * @link https://github.com/Orajo/sms-zilla Homepage
 * @copyright Copyright (c) 2016 Jarosław Wasilewski <orajo@windowslive.com>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

namespace SmsZilla\Adapter;

use SmsZilla\Adapter\AbstractAdapter;
use SmsZilla\ConfigurationException;
use SmsZilla\MessageInterface;
use SmsZilla\SmsMessageModel;
use SmsZilla\SendingError;

/**
 * Save message as file.
 * Creates separate file for every recepient. Folder with this files 
 * can be monitored by SMS gateway server.
 *
 * @subpackage Adapter
 * @author Jarosław Wasilewski <orajo@windowslive.com>
 */
class FileAdapter extends AbstractAdapter {
    const FILE_EXT = '.call';
    
    const ERROR_NOT_SAVED = 1;
    
    protected $params  = [
        'store_path' => null,
        'path_chmod' => 660,
        'format' => "[%s]" . PHP_EOL . "%s" . PHP_EOL,
    ];
    
    /**
     * Save message in file
     * @param SmsMessageModel $message
     * @return bool
     */
    public function send(MessageInterface $message, $skipErrors = true) {
        $this->clearErrors();
        
        $storePath = $this->getParam('store_path');
        if (empty($storePath)) {
            throw new ConfigurationException(__CLASS__ . ' is not configured properly. Please set "store_path" parameter.');
        }
        
        $pathChmod = $this->getParam('path_chmod');
        $dir = realpath($storePath);
        if (!is_dir($dir)) {
            if (!mkdir($storePath, $pathChmod, true)) {
                return false;
            }
        }

        $format = $this->getParam('format');
        foreach ($message->getRecipient() as $recipient) {
            $savePath = $storePath . DIRECTORY_SEPARATOR . $recipient . self::FILE_EXT;
            $content = sprintf($format, $recipient, $message->getText());

            $return = file_put_contents($savePath, $content);
            if($return === false) {
                $errorMsg = sprintf("Error while saving file \"%s\" with SMS message.", $savePath);
                $this->addError(new SendingError($recipient, self::ERROR_NOT_SAVED, $errorMsg));
                if (!$skipErrors){
                    throw new RuntimeException($errorMsg, self::ERROR_NOT_SAVED);
                }
            }
        }
        return $this->getErrors()->count() > 0;
    }
}
