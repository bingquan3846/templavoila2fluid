<?php

namespace ZerosOnes\Templavoila2fluid\Controller;

use GeorgRinger\News\Domain\Model\Dto\Search;
use GeorgRinger\News\Utility\Page;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\FormProtection\FormProtectionFactory;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Lang\LanguageService;

    /***************************************************************
     *
     *  Copyright notice
     *
     *  (c) 2016 Bingquan bao
     *
     *  All rights reserved
     *
     *  This script is part of the TYPO3 project. The TYPO3 project is
     *  free software; you can redistribute it and/or modify
     *  it under the terms of the GNU General Public License as published by
     *  the Free Software Foundation; either version 3 of the License, or
     *  (at your option) any later version.
     *
     *  The GNU General Public License can be found at
     *  http://www.gnu.org/copyleft/gpl.html.
     *
     *  This script is distributed in the hope that it will be useful,
     *  but WITHOUT ANY WARRANTY; without even the implied warranty of
     *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *  GNU General Public License for more details.
     *
     *  This copyright notice MUST APPEAR in all copies of the script!
     ***************************************************************/

/**
 * LogController
 */
class TransferController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

    /**
     * @var
     */
    protected $templavoilaHelper;

    protected $dceHelper;


    /**
     * @param \ZerosOnes\Templavoila2fluid\Service\TemplavoilaHelper $templavoilaHelper
     */
    public function injectTemplavoilaHelper(\ZerosOnes\Templavoila2fluid\Service\TemplavoilaHelper $templavoilaHelper){
        $this->templavoilaHelper = $templavoilaHelper;
    }

    public function injectDceHelper(\ZerosOnes\Templavoila2fluid\Service\DCEHelper $dceHelper){
        $this->dceHelper = $dceHelper;

    }

    /**
     * action index
     *
     * @return void
     */
    public function indexAction() {
        $templates = $this->templavoilaHelper->getAllTemplates();

        $this->view->assign('templates', $templates);
    }

    public function template2fluidAction(){
        $templatesId = GeneralUtility::_POST('template');

        if(!empty($templatesId)){
            $this->templavoilaHelper->transfer2Fluid($templatesId);
        }

        $this->redirect('index', 'Transfer', 'Templavoila2Fluid', array('transfer' => 1));
    }

    public function fce2dceAction(){

        $templates = $this->templavoilaHelper->getAllTemplates();
        $dces = $this->dceHelper->getAllDCE();
        $this->view->assign('templates', $templates);
        $this->view->assign('dces', $dces);

    }

    public function createDCEAction(){

        $templatesId = GeneralUtility::_POST('template');
        $templates =  $this->templavoilaHelper->getAllTemplates($templatesId);
        $this->dceHelper->createDCE($templates);
        $this->redirect('fce2dce', 'Transfer');

    }

    public function mappingContentAction(){

        $templates = $this->templavoilaHelper->getAllTemplates();
        $dces = $this->dceHelper->getAllDCE();
        $this->view->assign('templates', $templates);
        $this->view->assign('dces', $dces);

    }

    public function remappingAction(){

        $templatesId = GeneralUtility::_POST('template');
        $dceId = GeneralUtility::_POST('dce');

        $template = $this->templavoilaHelper->getAllTemplates(array($templatesId))[0];
        $contents = $this->templavoilaHelper->getContentsByTemplate($templatesId);

        if(!empty($contents)){
            foreach($contents as $content){

                $data = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($content['tx_templavoila_flex']);

                if ($GLOBALS['TSFE']->sys_language_isocode) {
                    $DS = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($template['dataprot']);
                    if (is_array($DS)) {
                        $langChildren = $DS['meta']['langChildren'] ? 1 : 0;
                        $langDisabled = $DS['meta']['langDisable'] ? 1 : 0;
                        $lKey = (!$langDisabled && !$langChildren) ? 'l' . $GLOBALS['TSFE']->sys_language_isocode : 'lDEF';
                        $vKey = (!$langDisabled && $langChildren) ? 'v' . $GLOBALS['TSFE']->sys_language_isocode : 'vDEF';
                    }

                } else {
                    $lKey = 'lDEF';
                    $vKey = 'vDEF';
                }

                if (is_array($data) && isset($data['data']['sDEF'][$lKey]) && is_array($data['data']['sDEF'][$lKey])) {
                    $dataValues = $data['data']['sDEF'][$lKey];

                    foreach($dataValues as $key => $value){
                        $field = $this->dceHelper->getCorrectKey($key);
                        $this->dceHelper->updateFileReference($content, $value[$vKey], $field);
                    }


                    $fields = array_keys($dataValues);

                    $flexform = $this->dceHelper->createFlexform($content['tx_templavoila_flex'], $fields);
                    $this->dceHelper->updateCtype($dceId, $content['uid']);
                    $this->dceHelper->updateFlexform($flexform, $content['uid']);
                }
            }
        }
        $this->redirect('mappingContent', 'Transfer');

    }

}