<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager;

use Yii;

class Component extends \yii\base\Component {

    /**
     * @inheritdoc
     */
    public function init() {
        $this->_initTranslation();
        parent::init();
    }

    private function _initTranslation() {
        $module = Yii::$app->getModule('translateManager');
        if ($module->checkAccess() && $this->_checkRoles($module->roles)) {
            Yii::$app->session->set(Module::SESSION_KEY_ENABLE_TRANSLATE, true);
        }
    }

    /**
     * @param array $roles The necessary roles for accessing the module.
     * @return bool
     */
    private function _checkRoles($roles) {
        if (!$roles) {
            return true;
        }
        foreach ($roles as $role) {
            if (Yii::$app->user->can($role)) {
                return true;
            }
        }
        return false;
    }

}