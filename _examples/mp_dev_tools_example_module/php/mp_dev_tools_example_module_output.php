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

    // Anonymous function for formats
    $formatFn = function (array $examples) {
        $examples = array_map(function ($item) {
            return '<span style="background-color:#eaeaea;">' . conHtmlentities($item) . '</span>';
        }, $examples);

        return implode(' ' . mi18n("LBL_OR") . ' ', $examples);
    };

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
        'key' => mi18n("LBL_CHECKBOX_ROW") . ' (' . $cmsCheckboxToken->var . '):',
        'value' => $cmsCheckboxToken->value,
    ];

    // Select
    $cmsSelectToken = $module->getCmsToken(1);
    $tplData[] = [
        'key' => mi18n("LBL_SELECT_ROW") . ' (' . $cmsSelectToken->var . '):',
        'value' => $cmsSelectToken->value,
    ];

    // Radio
    $cmsRadioButtonToken = $module->getCmsToken(2);
    $tplData[] = [
        'key' => mi18n("LBL_RADIO_ROW") . ' (' . $cmsRadioButtonToken->var . '):',
        'value' => $cmsRadioButtonToken->value,
    ];

    // Textbox
    $cmsTextToken = $module->getCmsToken(3);
    $tplData[] = [
        'key' => mi18n("LBL_TEXTBOX_ROW") . ' (' . $cmsTextToken->var . '):',
        'value' => $cmsTextToken->value,
    ];

    // Textarea
    $cmsTextareaToken = $module->getCmsToken(4);
    $tplData[] = [
        'key' => mi18n("LBL_TEXTAREA_ROW") . ' (' . $cmsTextareaToken->var . '):',
        'value' => $cmsTextareaToken->value,
    ];

    // Category select
    $cmsCategoryToken = $module->getCmsToken(10);
    $tplData[] = [
        'key' => mi18n("LBL_CATEGORY_SELECT") . ' (' . $cmsCategoryToken->var . '):',
        'value' => $cmsCategoryToken->value,
        'format' => $formatFn(['idcat:<idcat>', 'idcatart:<idcatart>', 'idcat:<idcat>,idcatart:<idcatart>,...']),
        'values' => $module->getGuiCategorySelectValues($cmsCategoryToken),
    ];

    // Article select
    $cmsArticleToken = $module->getCmsToken(11);
    $tplData[] = [
        'key' => mi18n("LBL_ARTICLE_SELECT") . ' (' . $cmsArticleToken->var . '):',
        'value' => $cmsArticleToken->value,
        'values' => $module->getGuiArticleSelectValues($cmsArticleToken),
    ];

    // Content type select
    $cmsContentTypeToken = $module->getCmsToken(12);
    $tplData[] = [
        'key' => mi18n("LBL_CONTENT_TYPE_SELECT") . ' (' . $cmsContentTypeToken->var . '):',
        'value' => $cmsContentTypeToken->value,
        'format' => $formatFn(['<idtype>:<typeid>', '<idtype>:<typeid>,<idtype>:<typeid>,...']),
        'values' => $module->getGuiContentTypeSelectValues($cmsContentTypeToken),
    ];

    // Multiple category select
    $cmsMultipleCategoryToken = $module->getCmsToken(13);
    $tplData[] = [
        'key' => mi18n("LBL_MULTIPLE_CATEGORY_SELECT") . ' (' . $cmsMultipleCategoryToken->var . '):',
        'value' => $cmsMultipleCategoryToken->value,
        'format' => $formatFn(['idcat:<idcat>', 'idcatart:<idcatart>', 'idcat:<idcat>,idcatart:<idcatart>,...']),
        'values' => $module->getGuiCategorySelectValues($cmsMultipleCategoryToken),
    ];

    // Multiple upload select
    $cmsUploadToken = $module->getCmsToken(14);
    $tplData[] = [
        'key' => mi18n("LBL_UPLOAD_SELECT") . ' (' . $cmsUploadToken->var . '):',
        'value' => $cmsUploadToken->value,
        'format' => $formatFn(['idupl:<idupl>', 'iddbfs:<iddbfs>', 'idupl:<idupl>,iddbfs:<iddbfs>,...']),
        'values' => $module->getGuiUploadSelectValues($cmsUploadToken),
    ];

    $tpl->assign('lblValues', mi18n("LBL_VALUES"));
    $tpl->assign('lblFormatIs', mi18n("LBL_FORMAT_IS"));
    $tpl->assign('data', $tplData);
    $tpl->display('get.tpl');

})();

?>