<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager\services;

use DemonDogSL\translateManager\models\LanguageSource;
use DemonDogSL\translateManager\models\Language;

class Optimizer {

    private $_scanner;
    private $_languageElements = [];

    /**
     * @return int Number of unused language elements detected.
     *
     * @deprecated since version 1.4
     */
    public function optimization() {
        return $this->run();
    }

    /**
     * @return int The number of removed language elements.
     */
    public function run() {
        $this->_scanner = new Scanner();
        $this->_scanner->run();
    }

    /**
     * @return array
     */
    public function getRemovedLanguageElements() {
        return $this->_languageElements;
    }

    /**
     * @param array $languageSourceIds
     */
    private function _initLanguageElements($languageSourceIds) {
        $languageSources = LanguageSource::findAll(['id' => $languageSourceIds]);
        foreach ($languageSources as $languageSource) {
            $this->_languageElements[$languageSource->category][$languageSource->message] = $languageSource->id;
        }
    }

}