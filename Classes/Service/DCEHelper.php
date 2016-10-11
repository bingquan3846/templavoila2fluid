<?php


namespace ZerosOnes\Templavoila2fluid\Service;


/**
 * Class DCEHelper
 * @package ZerosOnes\Templavoila2fluid\Service
 */
class DCEHelper implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @param $templates
     */
    public function createDCE($templates){
        if(!empty($templates)){
            foreach($templates as $template){
                $dataStructure = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($template['dataprot']);

                $dce = array();
                $template['title'] = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToLowerCamelCase($template['title']);
                $templates['title'] = str_replace('/', '', $template['title']);
                $dce['title'] = $template['title'];
                $dce['fields'] = count($dataStructure['ROOT']['el']);
                $dce['template_type'] = 'file';
                $dce['identifier'] = 'dce_';
                $dce['use_simple_backend_view'] = 1;
                $dce['template_layout_root_path'] = 'EXT:dce/Resources/Private/Layouts/';
                $dce['template_partial_root_path'] = 'EXT:dce/Resources/Private/Partials/';
                $dce['template_file'] = 'fileadmin/templates/fluid/dce/' . $template['title'] . '.html';

                $dceFields = array();
                $backendView = array();

                foreach($dataStructure['ROOT']['el'] as $key =>$el){
                    $field = array();
                    $field['parent_dce'] = '';
                    $field['new_tca_field_type'] = 'auto';
                    $field['title'] = $el['tx_templavoila']['title'];
                    $field['variable'] = $this->getCorrectKey($key);
                    $backendView[] =  $field['variable'];
                    switch($el['tx_templavoila']['eType']){
                        case 'input' : $field['configuration'] = '
<config>
	<type>input</type>
	<size>30</size>
	<eval>trim,required</eval>
</config>';
                                        break;
                        case 'check' : $field['configuration'] ='
<config>
	<type>check</type>
	<default>0</default>
</config>';

                        case 'text'  :
                        case 'rte'   : $field['configuration'] = '
<config>
	<type>text</type>
	<rows>5</rows>
	<cols>30</cols>
	<eval>trim,required</eval>
</config>
<defaultExtras>richtext[*]:rte_transform[mode=ts_css]</defaultExtras>';
                                        break;
                        case 'image'      :
                        case 'imagefixed' : $field['configuration'] = '
<config>
	<type>inline</type>
	<foreign_table>sys_file_reference</foreign_table>
	<foreign_field>uid_foreign</foreign_field>
	<foreign_sortby>sorting_foreign</foreign_sortby>
	<foreign_table_field>tablenames</foreign_table_field>
	<foreign_match_fields>
		<fieldname>'.$field['variable'].'</fieldname> <!-- CAUTION!! Replace "fal" with the variable name of this field! -->
	</foreign_match_fields>
	<foreign_label>uid_local</foreign_label>
	<foreign_selector>uid_local</foreign_selector>
	<foreign_selector_fieldTcaOverride>
		<config>
			<appearance>
				<elementBrowserType>file</elementBrowserType>
				<elementBrowserAllowed>gif,jpg,jpeg,tif,tiff,bmp,pcx,tga,png,pdf,ai,svg</elementBrowserAllowed>
			</appearance>
		</config>
	</foreign_selector_fieldTcaOverride>
	<foreign_types type="array">
		<numIndex index="2">
			<showitem>--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,--palette--;;filePalette</showitem>
		</numIndex>
	</foreign_types>

	<minitems>0</minitems>
	<maxitems>99</maxitems>

	<appearance>
		<useSortable>1</useSortable>
		<headerThumbnail>
			<field>uid_local</field>
			<width>45c</width>
			<height>45</height>
		</headerThumbnail>

		<showPossibleLocalizationRecords>0</showPossibleLocalizationRecords>
		<showRemovedLocalizationRecords>0</showRemovedLocalizationRecords>
		<showSynchronizationLink>0</showSynchronizationLink>
		<useSortable>1</useSortable>
		<enabledControls>
			<info>1</info>
			<new>0</new>
			<dragdrop>0</dragdrop>
			<sort>1</sort>
			<hide>1</hide>
			<delete>1</delete>
			<localize>1</localize>
		</enabledControls>

		<createNewRelationLinkTitle>LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference</createNewRelationLinkTitle>
	</appearance>

	<behaviour>
		<localizationMode>select</localizationMode>
		<localizeChildrenAtParentLocalization>1</localizeChildrenAtParentLocalization>
	</behaviour>
</config>
';
                                            break;
                        case 'link'       : $field['configuration'] = '
<config>
    <type>input</type>
    <size>30</size>
    <eval>trim</eval>
    <softref>typolink,typolink_tag,images,url</softref>
    <wizards>
        <_PADDING>2</_PADDING>
        <link>
            <type>popup</type>
            <title>Link</title>
            <module>
                <name>wizard_element_browser</name>
                <urlParameters>
                    <mode>wizard</mode>
                </urlParameters>
            </module>
            <icon>link_popup.gif</icon>
            <script>browse_links.php?mode=wizard</script>
            <params>
                <!--<blindLinkOptions>page,file,folder,url,spec</blindLinkOptions>-->
            </params>
            <JSopenParams>height=500,width=500,status=0,menubar=0,scrollbars=1</JSopenParams>
        </link>
    </wizards>
</config>';
                                            break;
                        default: $field['configuration'] = '
<config>
	<type>input</type>
	<size>30</size>
	<eval>trim,required</eval>
</config>';

                    }

                    $dceFields[] = $field;

                }
                $dce['backend_view_bodytext'] = implode(',', $backendView);
                $this->deleteDCEByTitle($dce['title']);
                $dceUid = $this->insertDCE($dce);

                foreach($dceFields as $fields){
                    $fields['parent_dce'] = $dceUid;
                    $this->insertDCEFields($fields);
                }

            }
        }
    }

    /**
     * @param string $dceId
     * @return mixed
     */
    public function getAllDCE($dceId = '' ){
        if( !empty($templateId) ){
            $where = 'uid in (' . implode(',', $templateId) . ')';
        }else{
            $where = '';
        }

        $dces = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'tx_dce_domain_model_dce', $where);
        return $dces;
    }

    /**
     * @param $title
     * @return bool
     */
    public function deleteDCEByTitle($title){

        $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_dce_domain_model_dcefield ', ' parent_dce in (select uid from  tx_dce_domain_model_dce b  where b.title  like "%' . $title . '%")');

        $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_dce_domain_model_dce', 'title like "%' . $title . '%"');

        return true;
    }

    /**
     * @param $fields
     * @return mixed
     */
    public function insertDCE($fields){

        $result = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_dce_domain_model_dce', $fields);

        $dceUid = $GLOBALS['TYPO3_DB']->sql_insert_id();

        return $dceUid;

    }

    /**
     * @param $fields
     * @return mixed
     */
    public function insertDCEFields($fields){
        return  $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_dce_domain_model_dcefield', $fields);
    }


    /**
     * @param $key
     * @return mixed
     */
    public function getCorrectKey($key){
        $keysArray = explode('_', $key);

        if(!empty($keysArray)){
            $key = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToLowerCamelCase($key);
        }

        return $key;
    }

    /**
     * @param $oldFlexform
     * @param $fields
     * @return mixed
     */
    public function createFlexform($oldFlexform, $fields){
        $dceFields = array();

        foreach ($fields as $key => $item) {
            $dceFields[] = 'settings.' . $this->getCorrectKey($item);
        }
        array_push($fields, 'sDEF');
        $dceFields[] = 'sheet.tabGeneral';

        return str_replace($fields, $dceFields, $oldFlexform);
    }

    /**
     * @param $flexform
     * @param $uid
     */
    public function updateFlexform($flexform, $uid ){
        $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tt_content', 'uid='.intval($uid), array('pi_flexform' => $flexform));
    }

    /**
     * @param $dceId
     * @param $uid
     */
    public function updateCtype($dceId, $uid){
        $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tt_content', 'uid='.intval($uid), array('CType' => 'dce_dceuid'.$dceId));
    }

    /**
     * @param $content
     * @param $file
     * @param $fieldName
     */
    public function updateFileReference($content, $file, $fieldName){

        $data['pid'] = $content['pid'];
        $data['uid_foreign'] = $content['uid'];
        $data['tablenames'] = 'tt_content';
        $data['fieldname'] = $fieldName;

        $fileData = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid', 'sys_file', ' identifier = "/tx_templavoila/' . $file . '"');
        //var_dump($file);die;
        if(!empty($fileData)){
            $data['uid_local'] = $fileData[0]['uid'];
            $GLOBALS['TYPO3_DB']->exec_DELETEquery('sys_file_reference', 'uid_foreign='.  $content['uid'] . ' and uid_local = '. $data['uid_local'] );
            $GLOBALS['TYPO3_DB']->exec_INSERTquery('sys_file_reference', $data);
        }

    }

}
