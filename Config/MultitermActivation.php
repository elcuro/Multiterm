<?php
/**
 * Plugin activation
 *
 * Description
 *
 * @package  Croogo
 * @author Juraj Jancuska <jjancuska@gmail.com>
 */
class MultitermActivation {

/**
 * Before onActivation
 *
 * @param object $controller
 * @return boolean
 */
    public function beforeActivation(&$controller) {

            return true;
    }

/**
 * Activation of plugin,
 * called only if beforeActivation return true
 *
 * @param object $controller
 * @return void
 */
    public function onActivation(&$controller) {

            $controller->Croogo->addAco('Multiterms'); // ExampleController
            $controller->Croogo->addAco('Multiterms/view', array('registered', 'public')); // ExampleController::index()

            return true;
    }

/**
 * Before onDeactivation
 *
 * @param object $controller
 * @return boolean
 */
    public function beforeDeactivation(&$controller) {

            return true;
    }

/**
 * Deactivation of plugin,
 * called only if beforeActivation return true
 *
 * @param object $controller
 * @return void
 */
    public function onDeactivation(&$controller) {

            $controller->Croogo->removeAco('Multiterms');

            return true;
    }

}
?>