<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager\services\scanners;

use DemonDogSL\translateManager\services\Scanner;

class ScannerPhpArray extends ScannerFile {

    const EXTENSION = '*.php';

    /**
     * @param string $route
     * @param array $params
     * @inheritdoc
     */
    public function run($route, $params = []) {
        foreach (self::$files[static::EXTENSION] as $file) {
            foreach ($this->_getTranslators($file) as $translator) {
                $this->extractMessages($file, [
                    'translator' => [$translator],
                    'begin' => (preg_match('#array\s*$#i', $translator) != false) ? '(' : '[',
                    'end' => ';',
                ]);
            }
        }
    }

    /**
     * @param string $file Path to the file to scan.
     * @return array List of arrays storing the language elements to be translated.
     */
    private function _getTranslators($file) {
        $subject = file_get_contents($file);
        preg_match_all($this->module->patternArrayTranslator, $subject, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        $translators = [];
        foreach ($matches as $data) {
            if (isset($data['translator'][0])) {
                $translators[$data['translator'][0]] = true;
            }
        }
        return array_keys($translators);
    }

    /**
     * @inheritdoc
     */
    protected function getLanguageItem($buffer) {
        $index = -1;
        $languageItems = [];
        foreach ($buffer as $key => $data) {
            if (isset($data[0], $data[1]) && $data[0] === T_CONSTANT_ENCAPSED_STRING) {
                $message = stripcslashes($data[1]);
                $message = mb_substr($message, 1, mb_strlen($message) - 2);
                if (isset($buffer[$key - 1][0]) && $buffer[$key - 1][0] === '.') {
                    $languageItems[$index]['message'] .= $message;
                } else {
                    $languageItems[++$index] = [
                        'category' => Scanner::CATEGORY_ARRAY,
                        'message' => $message,
                    ];
                }
            }
        }
        return $languageItems ?: null;
    }

}