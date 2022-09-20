<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager\services\scanners;

class ScannerPhpFunction extends ScannerFile {

    const EXTENSION = '*.php';

    /**
     * @param string $route
     * @param array $params
     * @inheritdoc
     */
    public function run($route, $params = []) {
        foreach (self::$files[static::EXTENSION] as $file) {
            if ($this->containsTranslator($this->module->phpTranslators, $file)) {
                $this->extractMessages($file, [
                    'translator' => (array) $this->module->phpTranslators,
                    'begin' => '(',
                    'end' => ')',
                ]);
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function getLanguageItem($buffer) {
        if (isset($buffer[0][0], $buffer[1], $buffer[2][0]) && $buffer[0][0] === T_CONSTANT_ENCAPSED_STRING && $buffer[1] === ',' && $buffer[2][0] === T_CONSTANT_ENCAPSED_STRING) {
            // is valid call we can extract
            $category = stripcslashes($buffer[0][1]);
            $category = mb_substr($category, 1, mb_strlen($category) - 2);
            if (!$this->isValidCategory($category)) {
                return null;
            }
            $message = implode('', $this->concatMessage($buffer));
            return [
                [
                    'category' => $category,
                    'message' => $message,
                ],
            ];
        }
        return null;
    }

    /**
     * @param array $buffer Array to store language element pieces.
     * @return array Sorted list of language element pieces.
     */
    protected function concatMessage($buffer) {
        $messages = [];
        $buffer = array_slice($buffer, 2);
        $message = stripcslashes($buffer[0][1]);
        $messages[] = mb_substr($message, 1, mb_strlen($message) - 2);
        if (isset($buffer[1], $buffer[2][0]) && $buffer[1] === '.' && $buffer[2][0] == T_CONSTANT_ENCAPSED_STRING) {
            $messages = array_merge_recursive($messages, $this->concatMessage($buffer));
        }
        return $messages;
    }

}