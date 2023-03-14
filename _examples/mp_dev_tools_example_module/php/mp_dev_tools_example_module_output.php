<?php

/**
 * Module mp_dev_tools_example_module output.
 *
 * @package     Module
 * @subpackage  MpDevToolsExampleModule
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

// Wrap module code with a IIFE (Immediately Invoke Function Expression, a.k.a.
// Self-Executing Anonymous Function) syntax, so that the module code stays in
// its own namespace/scope.
(function() {

    // ##############################################

    // Some initial checks

    if (!class_exists(\CONTENIDO\Plugin\MpDevTools\Module\AbstractBase::class)) {
        new cException('This module requires the plugin "Mp Dev Tools", please download, install and activate it!');
    }

    if (!class_exists(MpDevToolsExampleModule::class)) {
        cInclude('module', 'class.mp.dev.tools.example.module.php');
    }

    // ##############################################

    // Instance of the example module class
    $module = new MpDevToolsExampleModule([
        'debug' => false,
        'db' => cRegistry::getDb(),
        'myCustomProperty' => 'Value of my custom property',
    ]);

    // ##############################################

    $tpl = cSmartyFrontend::getInstance();

    $tplData = [];

    // Checkbox
    $cmsCheckboxToken = $module->getCmsToken(0);
    $tplData[] = [
        'key' => 'Checkbox: ' . $cmsCheckboxToken->getVar(),
        'value' => $cmsCheckboxToken->getValue(),
    ];

    // Select
    $cmsSelectToken = $module->getCmsToken(1);
    $tplData[] = [
        'key' => 'Selectbox: ' . $cmsSelectToken->getVar(),
        'value' => $cmsSelectToken->getValue(),
    ];

    // Radio
    $cmsRadioButtonToken = $module->getCmsToken(2);
    $tplData[] = [
        'key' => 'Radio button: ' . $cmsRadioButtonToken->getVar(),
        'value' => $cmsRadioButtonToken->getValue(),
    ];

    // Textbox
    $cmsTextToken = $module->getCmsToken(3);
    $tplData[] = [
        'key' => 'Textbox: ' . $cmsTextToken->getVar(),
        'value' => $cmsTextToken->getValue(),
    ];

    // Textarea
    $cmsTextareaToken = $module->getCmsToken(4);
    $tplData[] = [
        'key' => 'Textarea: ' . $cmsTextareaToken->getVar(),
        'value' => $cmsTextareaToken->getValue(),
    ];

    // Category select
    $cmsCategoryToken = $module->getCmsToken(10);
    $tplData[] = [
        'key' => 'Category select: ' . $cmsCategoryToken->getVar(),
        'value' => $cmsCategoryToken->getValue() . conHtmlentities(' (format is: cat_<idcat> or art_<idcatart>)'),
    ];

    // Article select
    $cmsArticleToken = $module->getCmsToken(11);
    $tplData[] = [
        'key' => 'Article select: ' . $cmsArticleToken->getVar(),
        'value' => $cmsArticleToken->getValue(),
    ];

    // Content type select
    $cmsContentTypeToken = $module->getCmsToken(12);
    $tplData[] = [
        'key' => 'Content type select: ' . $cmsContentTypeToken->getVar(),
        'value' => $cmsContentTypeToken->getValue() . conHtmlentities(' (format is: <idtype>:<typeid>)'),
    ];

    $tpl->assign('data', $tplData);
    $tpl->display('get.tpl');

})();

?>