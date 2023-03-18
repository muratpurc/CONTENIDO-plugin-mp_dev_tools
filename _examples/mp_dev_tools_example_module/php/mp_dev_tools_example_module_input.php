?>
<?php

/**
 * Module mp_dev_tools_example_module input.
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
        'i18n' => [
            'LBL_RESULT' => mi18n('LBL_RESULT')
        ],
        'myCustomProperty' => 'Value of my custom property',
    ]);

    // ##############################################

    // Render some styles for this module
    echo $module->renderModuleInputStyles();

    // Table instance
    $table = $module->getGuiTable(['width' => '100%']);

    $table->addContrastRow(
        [mi18n("MSG_TABLE_EXAMPLE")], [], [['colspan' => '2']]
    );

    // Checkbox row example
    $cmsCheckboxToken = $module->getCmsToken(0); // For CMS_VAR[n], CMS_VALUE[n]
    $checkbox = new cHTMLCheckbox($cmsCheckboxToken->getVar(), "true");
    $checkbox->setLabelText(mi18n("LBL_CHECKBOX"))
        ->setChecked($cmsCheckboxToken->getValue());
    $table->addRow([mi18n("LBL_CHECKBOX_ROW"), $checkbox]);

    // Select row example
    $cmsSelectToken = $module->getCmsToken(1);
    $select = new cHTMLSelectElement($cmsSelectToken->getVar());
    $select->autofill(['' => mi18n("OPTION_PLEASE_CHOOSE"), "1" => "Foo", "2" => "Bar"])
        ->setDefault($cmsSelectToken->getValue());
    $table->addRow([mi18n("LBL_SELECT_ROW"), $select]);

    // Radio button row example
    $cmsRadioButtonToken = $module->getCmsToken(2);

    $radio1 = new cHTMLRadiobutton($cmsRadioButtonToken->getVar(), 'radio_value_1', '', $cmsRadioButtonToken->getValue() === 'radio_value_1');
    $radio1->setLabelText(mi18n("LBL_RADIO_1"));

    $radio2 = new cHTMLRadiobutton($cmsRadioButtonToken->getVar(), 'radio_value_2', '', $cmsRadioButtonToken->getValue() === 'radio_value_2');
    $radio2->setLabelText(mi18n("LBL_RADIO_2"));

    $radio3 = new cHTMLRadiobutton($cmsRadioButtonToken->getVar(), 'radio_value_3', '', $cmsRadioButtonToken->getValue() === 'radio_value_3');
    $radio3->setLabelText(mi18n("LBL_RADIO_3"));

    $table->addFullSeparatorRow([mi18n("LBL_RADIO_ROW"), $radio1 . ' ' . $radio2 . ' ' . $radio3]);

    // Textbox row example
    $cmsTextToken = $module->getCmsToken(3);
    $textbox = new cHTMLTextbox($cmsTextToken->getVar(), $cmsTextToken->getValue());
    $table->addRow([mi18n("LBL_TEXTBOX_ROW"), $textbox]);

    // Textarea row example
    $cmsTextareaToken = $module->getCmsToken(4);
    $textarea = new cHTMLTextarea($cmsTextareaToken->getVar(), $cmsTextareaToken->getValue());
    $infoButton = new cGuiBackendHelpbox(mi18n("MSG_TEXTAREA_INFO"));
    $table->addRow([mi18n("LBL_TEXTAREA_ROW"), $textarea->render() . ' ' . $infoButton->render()]);

    // Row submit button
    $table->addSubmitRow(['', mi18n("SAVE_CHANGES")]);

    // Render table
    echo $table->render();

    // ##############################################

    // Fieldset table "More selects"

    // Fieldset table class
    // - "mp_dev_tools_show_more": Enables toggling
    // - "mp_dev_tools_show_all": Disables toggling, fieldset table is always open
    $legendCssClass = "mp_dev_tools_show_more";

    $fieldsetTable = $module->getGuiFieldsetTable();
    $fieldsetTable->setLegend($legendCssClass, mi18n("LEGEND_MORE_SELECTS"));

    // Row category select
    $cmsCategoryToken = $module->getCmsToken(10);
    $categorySelect = $module->getGuiCategorySelect(
        $cmsCategoryToken->getVar(), $module->clientId, $module->languageId
    );
    $fieldsetTable->addFullSeparatorRow([
        mi18n("LBL_CATEGORY_SELECT"), $categorySelect->render($cmsCategoryToken->getValue())
    ]);

    // Row article select
    $selectedCategoryId = cSecurity::toInteger(str_replace('cat_', '', $cmsCategoryToken->getValue()));
    $cmsArticleToken = $module->getCmsToken(11);
    $articleSelect = $module->getGuiArticleSelect(
        $cmsArticleToken->getVar(), $module->clientId, $module->languageId
    );
    $fieldsetTable->addFullSeparatorRow([
        mi18n("LBL_ARTICLE_SELECT"),
        $articleSelect->render($selectedCategoryId, $cmsArticleToken->getValue())
    ]);

    // Row content type select
    $cmsContentTypeToken = $module->getCmsToken(12);
    $contentTypeSelect = $module->getGuiContentTypeSelect(
        $cmsContentTypeToken->getVar(), $module->clientId, $module->languageId
    );
    $contentTypes = $module->getContentTypeIds();
    $contentTypes = implode(',', $contentTypes);
    $contentTypeSelect = $contentTypeSelect->render(
        $cmsArticleToken->getValue(), $cmsContentTypeToken->getValue(), $contentTypes
    );
    $fieldsetTable->addFullSeparatorRow([
        mi18n("LBL_CONTENT_TYPE_SELECT"), $contentTypeSelect
    ]);

    // Multiple select row example
    $cmsToken = $module->getCmsToken(13);
    $infoButton = new cGuiBackendHelpbox(mi18n("MSG_MULTIPLE_SELECT"));
    $categorySelect = $module->getGuiCategorySelect(
        $cmsToken->var, $module->clientId, $module->languageId,
        ['multiple' => 'multiple', 'size' => 5]
    );
    $fieldsetTable->addRow([
        mi18n("LBL_MULTIPLE_SELECT"),
        $categorySelect->render($cmsToken->value) . $infoButton->render()]
    );

    // Row submit button
    $fieldsetTable->addSubmitRow(['', mi18n("SAVE_CHANGES")]);

    // Render table
    echo $fieldsetTable->render();

    // ##############################################

    // Fieldset table "Module"

    $fieldsetTable = $module->getGuiFieldsetTable();
    $fieldsetTable->setLegend('mp_dev_tools_show_all', mi18n("LEGEND_MODULE"));
    $fieldsetTable->addContrastRow(
        [mi18n("MSG_MODULE")], [], [['colspan' => '2']]
    );

    $moduleClassExample = @file_get_contents($module->getPhpPath('class.mp.dev.tools.example.module.php'));
    $module->addCodeRow($fieldsetTable, mi18n("LBL_MODULE_CLASS_EXAMPLE"), conHtmlentities($moduleClassExample));

    $fieldsetTable->addContrastRow(
        [mi18n("MSG_MODULE_2")], [], [['colspan' => '2']]
    );

    $moduleInstanceExample = "
    // Instance of the example module
    \$module = new MpDevToolsExampleModule([
        'debug' => false,
        'db' => cRegistry::getDb(),
        'i18n' => [
            'LBL_RESULT' => mi18n('LBL_RESULT')
        ],
        'myCustomProperty' => 'Value of my custom property',
    ]);
    ";
    $module->addCodeRow($fieldsetTable, '', conHtmlentities($moduleInstanceExample));

    $fieldsetTable->addContrastRow(
        [mi18n("MSG_MODULE_3")], [], [['colspan' => '2']]
    );
    $module->addCodeRow($fieldsetTable, '', $module);

    echo $fieldsetTable->render();


    // ##############################################

    // Fieldset table "Properties"

    // Fieldset table class
    $legendCssClass = "mp_dev_tools_show_more";

    $fieldsetTable = $module->getGuiFieldsetTable();
    $fieldsetTable->setLegend($legendCssClass, mi18n("LEGEND_MODULE_PROPERTIES"));

    // Row debug
    $module->addCodeRow($fieldsetTable, '$module->debug', $module->debug);

    // Row i18n property
    $module->addCodeRow($fieldsetTable, '$module->i18n', $module->i18n);

    // Row custom property
    $module->addCodeRow($fieldsetTable, '$module->myCustomProperty', $module->myCustomProperty);

    // Row moduleId
    $module->addCodeRow($fieldsetTable, '$module->moduleId', $module->moduleId);

    // Row containerNumber
    $module->addCodeRow($fieldsetTable, '$module->containerNumber', $module->containerNumber);

    // Row articleId
    $module->addCodeRow($fieldsetTable, '$module->articleId', $module->articleId);

    // Row languageId
    $module->addCodeRow($fieldsetTable, '$module->languageId', $module->languageId);

    // Row clientId
    $module->addCodeRow($fieldsetTable, '$module->clientId', $module->clientId);

    // Row categoryId
    $module->addCodeRow($fieldsetTable, '$module->categoryId', $module->categoryId);

    // Row cfg
    $module->addCodeRow($fieldsetTable, '$module->cfg', $module->cfg);

    // Row cfgClient
    $module->addCodeRow($fieldsetTable, '$module->cfgClient', $module->cfgClient);

    // Render fieldset table
    echo $fieldsetTable->render();

    // ##############################################

    // Fieldset table "Methods"

    $fieldsetTable = $module->getGuiFieldsetTable();
    $fieldsetTable->setLegend('mp_dev_tools_show_all', mi18n("LEGEND_MODULE_METHODS"));

    // Row getModuleName
    $module->addCodeRow($fieldsetTable, '$module->getModuleName()', $module->getModuleName());

    // Row getModulePath
    $module->addCodeRow($fieldsetTable, '$module->getModulePath()', $module->getModulePath());

    // Row getJsPath
    $module->addCodeRow($fieldsetTable, '$module->getJsPath()', $module->getJsPath());

    // Row getCssPath
    $module->addCodeRow($fieldsetTable, '$module->getCssPath()', $module->getCssPath());

    // Row getTemplatePath
    $module->addCodeRow($fieldsetTable, '$module->getTemplatePath()', $module->getTemplatePath());

    // Row templateExists
    $module->addCodeRow($fieldsetTable, '$module->templateExists("get.tpl")', $module->templateExists('get.tpl'));

    // Row getPhpPath
    $module->addCodeRow($fieldsetTable, '$module->getPhpPath()', $module->getPhpPath());

    // Row getProperty
    $module->addCodeRow($fieldsetTable, '$module->getProperty("myCustomProperty")', $module->getProperty("myCustomProperty"));

    // Row getBackendArea
    $module->addCodeRow($fieldsetTable, '$module->getBackendArea()', $module->getBackendArea());

    // Row getBackendAction
    $module->addCodeRow($fieldsetTable, '$module->getBackendAction()', $module->getBackendAction());

    // Row isBackendEditMode
    $module->addCodeRow($fieldsetTable, '$module->isBackendEditMode()', $module->isBackendEditMode());

    // Row isBackendPreviewMode
    $module->addCodeRow($fieldsetTable, '$module->isBackendPreviewMode()', $module->isBackendPreviewMode());

    // Row isBackendSession
    $module->addCodeRow($fieldsetTable, '$module->isBackendSession()', $module->isBackendSession());

    // Row isBackendVisualEditMode
    $module->addCodeRow($fieldsetTable, '$module->isBackendVisualEditMode()', $module->isBackendVisualEditMode());

    // Render fieldset table
    echo $fieldsetTable->render();

    // ##############################################

    // Fieldset table "Module token"

    $fieldsetTable = $module->getGuiFieldsetTable();
    $fieldsetTable->setLegend('mp_dev_tools_show_all', mi18n("LEGEND_MODULE_TOKEN_INFO"));

    $fieldsetTable->addContrastRow(
        [mi18n("MSG_MODULE_TOKEN")], [], [['colspan' => '2']]
    );

    $cmsToken = $module->getCmsToken(2);
    $module->addCodeRow($fieldsetTable, '$cmsToken = $module->getCmsToken(2)', $cmsToken);

    $module->addCodeRow($fieldsetTable, '$cmsToken->getIndex()', $cmsToken->getIndex());

    $module->addCodeRow($fieldsetTable, '$cmsToken->getVar()', $cmsToken->getVar());

    $module->addCodeRow($fieldsetTable, '$cmsToken->getValue()', $cmsToken->getValue());


    // Render fieldset table
    echo $fieldsetTable->render();


    // ##############################################

    // Fieldset table "Request"

    $fieldsetTable = $module->getGuiFieldsetTable();
    $fieldsetTable->setLegend('mp_dev_tools_show_all', mi18n("LEGEND_REQUEST_INFO"));

    $fieldsetTable->addContrastRow(
        [mi18n("MSG_REQUEST_INFO")], [], [['colspan' => '2']]
    );

    $request = $module->getRequest();
    $module->addCodeRow($fieldsetTable, '$request = $module->getRequest()', $request);

    // Row request GET
    $module->addCodeRow($fieldsetTable, '$request->get()', $request->get());

    // Row request is GET
    $module->addCodeRow($fieldsetTable, '$request->isGet()', $request->isGet());

    // Row request POST
    $module->addCodeRow($fieldsetTable, '$request->post()', $request->post());

    // Row request is POST
    $module->addCodeRow($fieldsetTable, '$request->isPost()', $request->isPost());

    // Row request is Ajax
    $module->addCodeRow($fieldsetTable, '$request->isAjax()', $request->isAjax());

    // Render fieldset table
    echo $fieldsetTable->render();

    // ##############################################

    // Fieldset table "Client info"

    $fieldsetTable = $module->getGuiFieldsetTable();
    $fieldsetTable->setLegend('mp_dev_tools_show_all', mi18n("LEGEND_CLIENT_INFO"));

    $fieldsetTable->addContrastRow(
        [mi18n("MSG_CLIENT_INFO")], [], [['colspan' => '2']]
    );

    // Row client info
    $clientInfo = $module->getClientInfo();
    $module->addCodeRow($fieldsetTable, '$clientInfo = $module->getClientInfo()', $module->getClientInfo());

    $fieldsetTable->addContrastRow(
        [mi18n("COMMON_CLIENT_INFO")], [], [['colspan' => '2']]
    );

    $module->addCodeRow($fieldsetTable, '$clientInfo->getId()', $clientInfo->getId());

    $module->addCodeRow($fieldsetTable, '$clientInfo->getName()', $clientInfo->getName());

    $module->addCodeRow($fieldsetTable, '$clientInfo->getErrorSiteCategoryId()', $clientInfo->getErrorSiteCategoryId());

    $module->addCodeRow($fieldsetTable, '$clientInfo->getErrorSiteArticleId()', $clientInfo->getErrorSiteArticleId());

    $fieldsetTable->addContrastRow(
        [mi18n("CLIENT_PATH_INFO") . '<br><br>' . mi18n("MSG_CLIENT_PATHS")], [], [['colspan' => '2']]
    );

    $module->addCodeRow($fieldsetTable, '$clientInfo->getPath()', $clientInfo->getPath());

    $module->addCodeRow($fieldsetTable, '$clientInfo->getUrl()', $clientInfo->getUrl());

    $module->addCodeRow($fieldsetTable, '$clientInfo->getImagePath()', $clientInfo->getImagePath());

    $module->addCodeRow($fieldsetTable, '$clientInfo->getImageUrl()', $clientInfo->getImageUrl());

    $module->addCodeRow($fieldsetTable, '$clientInfo->getImagePath("calendar.png")', $clientInfo->getImagePath("calendar.png"));

    $module->addCodeRow($fieldsetTable, '$clientInfo->getImageUrl("calendar.png")', $clientInfo->getImageUrl("calendar.png"));

    $module->addCodeRow($fieldsetTable, '$clientInfo->getUploadPath()', $clientInfo->getUploadPath());

    $module->addCodeRow($fieldsetTable, '$clientInfo->getUploadUrl()', $clientInfo->getUploadUrl());

    $module->addCodeRow($fieldsetTable, '$clientInfo->getUploadPath("picture_gallery/galerie_01.jpg")', $clientInfo->getUploadPath("picture_gallery/galerie_01.jpg"));

    $module->addCodeRow($fieldsetTable, '$clientInfo->getUploadUrl("picture_gallery/galerie_01.jpg")', $clientInfo->getUploadUrl("picture_gallery/galerie_01.jpg"));

    $module->addCodeRow($fieldsetTable, '$clientInfo->relativePath($clientInfo->getUploadPath())', $clientInfo->relativePath($clientInfo->getUploadPath()));

    $module->addCodeRow($fieldsetTable, '$clientInfo->relativeUrl($clientInfo->getUploadUrl()', $clientInfo->relativeUrl($clientInfo->getUploadUrl()));

    $module->addCodeRow($fieldsetTable, '$clientInfo->getCssPath()', $clientInfo->getCssPath());

    $module->addCodeRow($fieldsetTable, '$clientInfo->getCssUrl()', $clientInfo->getCssUrl());

    $module->addCodeRow($fieldsetTable, '$clientInfo->getJsPath()', $clientInfo->getJsPath());

    $module->addCodeRow($fieldsetTable, '$clientInfo->getJsUrl()', $clientInfo->getJsUrl());

    $module->addCodeRow($fieldsetTable, '$clientInfo->getTemplatePath()', $clientInfo->getTemplatePath());

    $module->addCodeRow($fieldsetTable, '$clientInfo->getCachePath()', $clientInfo->getCachePath());

    $module->addCodeRow($fieldsetTable, '$clientInfo->getCodePath()', $clientInfo->getCodePath());

    $module->addCodeRow($fieldsetTable, '$clientInfo->getXmlPath()', $clientInfo->getXmlPath());

    $module->addCodeRow($fieldsetTable, '$clientInfo->getDataPath()', $clientInfo->getDataPath());

    $module->addCodeRow($fieldsetTable, '$clientInfo->getModulePath()', $clientInfo->getModulePath());

    $module->addCodeRow($fieldsetTable, '$clientInfo->getConfigPath()', $clientInfo->getConfigPath());

    $module->addCodeRow($fieldsetTable, '$clientInfo->getLayoutPath()', $clientInfo->getLayoutPath());

    $module->addCodeRow($fieldsetTable, '$clientInfo->getLogPath()', $clientInfo->getLogPath());

    $module->addCodeRow($fieldsetTable, '$clientInfo->getVersionPath()', $clientInfo->getVersionPath());

    // Render fieldset table
    echo $fieldsetTable->render();

})();
