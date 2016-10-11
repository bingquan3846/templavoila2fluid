<?php
namespace ZerosOnes\Templavoila2fluid\Service;

/**
 * Class TemplavoilaHelper
 * @package ZerosOnes\Templavoila2fluid\Service
 */
class TemplavoilaHelper implements \TYPO3\CMS\Core\SingletonInterface{

    /**
     * @param string $templateId
     * @return mixed
     */
    public function getAllTemplates($templateId = ''){
        if( !empty($templateId) ){
            $where = 'a.uid in (' . implode(',', $templateId) . ')';
        }else{
            $where = '';
        }

        $tempaltes = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('a.*, b.scope, b.dataprot', '`tx_templavoila_tmplobj` a inner join tx_templavoila_datastructure  b on a.datastructure = b.uid', $where, '', 'scope desc');
        return $tempaltes;
    }

    /**
     * @param $templateId
     */
    public function transfer2Fluid($templateId){
        $templates = $this->getAllTemplates($templateId);
        if(!is_dir(PATH_site.'/fileadmin/templates/fluid')){
            mkdir(PATH_site.'/fileadmin/templates/fluid');
        }
        if(!is_dir(PATH_site.'/fileadmin/templates/fluid/dce')){
            mkdir(PATH_site.'/fileadmin/templates/fluid/dce');
        }

        foreach($templates as $template){
            $TO = unserialize($template['templatemapping']);
            $out = array();


            foreach($TO['MappingData_cached']['cArray'] as $key => $item){

                if(!is_int($key) && intval($template['scope']) != 1){
                    $keysArray = explode('_', $key);

                    if(!empty($keysArray)){
                       $key = DCEHelper::getCorrectKey($key);
                    }

                }
                $out[] =  (is_numeric($key)) ? $item : (( intval($template['scope']) === 1)? '<f:cObject typoscriptObjectPath="lib.' .$key. '" />' : '{field.' .$key. '}') ;
            }
            $template['title'] = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToLowerCamelCase($template['title']);
            $templates['title'] = str_replace('/', '', $template['title']);
            //var_dump($template['title']);exit;

            if( intval($template['scope']) === 1){
                \TYPO3\CMS\Core\Utility\GeneralUtility::writeFile(PATH_site.'/fileadmin/templates/fluid/'.$template['title'].'.html', implode('', $out));
            }else{
                $content = implode('', $out);
                $content = '{namespace dce=ArminVieweg\Dce\ViewHelpers}<f:layout name="Default" /><f:section name="main">' . $content . '</f:section>';
                \TYPO3\CMS\Core\Utility\GeneralUtility::writeFile(PATH_site.'/fileadmin/templates/fluid/dce/'.$template['title'].'.html', $content );
            }

        }

    }

    /**
     * @param $templateId
     * @return mixed
     */
    public function getContentsByTemplate($templateId){
        $contents = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'tt_content', 'hidden = 0 and deleted = 0 and tx_templavoila_to =' . $templateId);
        return $contents;
    }

}