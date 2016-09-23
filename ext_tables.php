<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

if (TYPO3_MODE === 'BE') {

	/**
	 * Registers a Backend Module
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'ZerosOnes.' . $_EXTKEY,
		'tools',	 // Make module a submodule of 'web'
		'templavoila2fluid',	// Submodule key
		'',						// Position
		array(
			'Transfer' => 'index,template2fluid,fce2dce,createDCE, mappingContent,remapping',
			
		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_templavoila2fluid.xlf',
		)
	);

}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Templavoila to Fluid');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_templavoila2fluid_domain_model_log', 'EXT:templavoila2fluid/Resources/Private/Language/locallang_csh_tx_templavoila2fluid_domain_model_log.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_templavoila2fluid_domain_model_log');
