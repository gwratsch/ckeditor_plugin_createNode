<?php

/**
 *
 * @author Gerd Ratsch
 */
class ckeditor_plugin_createNode_ckeditorPlugin extends ckeditor_plugin_ckeditorPlugin{
    public function __construct() {
        parent::__construct();
    }
}
class nt extends ckeditor_plugin_ckeditorPlugin{
    
    public function __construct() {
        parent::__construct();
        $this->startString = '<strong>NTB</strong>.';
        $this->endString = '<strong>NTE</strong>.';
        $this->grouprelationname = array('nb');  
        $this->label = 'Node Title';
        $this->command = 'NT';
        $this->icon = 'images/NT.png';
    }
    public function validatePlugin(&$contentList,&$node){
        $body = $this->getBodyContent($node);
        $list = $this->buildPositionList($body);
        $this->createContentList($list,$body);
        $this->getContent($body );
        $this->addNID();
    }
    public function pluginAction(&$contentList,&$node){
        $body = $this->getBodyContent($node);
        $this->addNID();
        $this->saveNodeSelection($node, $contentList);
    }
    public function addNID(){
        foreach ($this->contentList as $rowkey => $itemInfo) {
             if(array_key_exists('content', $itemInfo)){
                $content = $itemInfo['content'];
                $this->contentList[$rowkey]['nid'] = $this->findNodeId($content);
             } 
        }
 
    }
    public function findNodeId($content){
        $query = db_select('node', 'n') ;            
        $query->fields('n', array('nid','title'));
        $query->condition('status', 1);
        $query->condition('type', 'article');
        $query->condition('title', '%'.db_like($content).'%' , 'like');
        $result = $query->execute();
        $nid_list =  $result->fetchAll();
        $nid=0;
        if(count($nid_list) > 1){
            $message= 'The title <strong>'.$title.'</strong> is found in more than 1 excisting nodes. Change the title to get the correct node selected.';
            drupal_set_message($message);
            $this->errorMessage($nid_list,$content);
        }elseif(array_key_exists('0', $nid_list)){
            $nid = $nid_list[0]->nid;
        }
        return $nid;
    }
    public function errorMessage($nid_list, $content){
        $message = '<table><tr><th>Name</th><th>node id</th><th>node name</th></tr>';
        foreach ($nid_list as $row => $nodeInfo) {
            $message .= '<tr><th>'.$content.'</th><th>'.$nodeInfo->nid.'</th><th>'.$nodeInfo->title.'</th></tr>';
        }
        $message .= '</table>';
        form_set_error($name = 'check noderelation',$message , $limit_validation_errors = NULL);
    }
    public function saveNodeSelection(&$node, &$contentList){
        $language = (key($node->body) !== '') ? key($node->body) : 'und';
        $nodeType = $node->type;
        //dpm($contentList);
        foreach ($this->contentList as $rowkey => $itenInfo) {
            $nid = $node_id = $itenInfo['nid'];
            $title = $itenInfo['content'];
            $bodyContent = $contentList->contentList['nb']->contentList[$rowkey]['content'];
            if($node_id == 0){
                $languages_node = key($node->body);
                $new_node['title'] = $title;
                $new_node['type']= $nodeType;
                $new_node['fields']['body'][$languages_node][]=array(
                            'value'=>$bodyContent,
                            'format'=>'full_html',
                        );
                $nid = $this->create_node($new_node);
            }
            if($node_id > 0){
                $update_node = node_load($nid);
                $newContentMessage = '<h1> New content from '.$node->title.'</h1>';
                $languages = key($update_node->body);
                if($languages ==''){
                    $languages = 'und';
                    $update_node->body[$languages][0]['value']='';
                    $newContentMessage='';
                }
                $update_node->body[$languages][0]['value'].= $newContentMessage.$bodyContent;
                $update_node->field_document_status['und']['0']['tid']=2;

                node_save($update_node);
            }
            if($nid > 0){
                $title_replaceContent = '<a href="/node/'.$nid.'">'.$title.'</a>';    
                $this->contentList[$rowkey]['replace'] = $title_replaceContent;
            }else{
                $message= 'Creating or updating the node with title <strong>'.$title.'</strong> wend, for unknown reason, wrong.';
                drupal_set_message($message);
            }
        }
    }
    function create_node($new_node){
        global $user;

        $node = new stdClass();
        $node->title = $new_node['title'];
        $node->type = $new_node['type'];
        node_object_prepare($node); // Sets some defaults. Invokes hook_prepare() and hook_node_prepare().
        $node->language = LANGUAGE_NONE; // Or e.g. 'en' if locale is enabled
        $node->uid = $user->uid; 
        $node->field_document_status['und']['0']['tid']=2;
        $node->status = 1; //(1 or 0): published or not
        $node->promote = 0; //(1 or 0): promoted to front page
        $node->comment = 0; // 0 = comments disabled, 1 = read only, 2 = read/write
        if(array_key_exists('fields', $new_node) && count($new_node['fields']> 0 )){
            foreach ($new_node['fields'] as $fieldname => $value_array) {
                $node->$fieldname=$value_array;
            }
        }

        $node = node_submit($node); // Prepare node for saving
        node_save($node);
        return $node->nid;
    }
}
class nb extends ckeditor_plugin_ckeditorPlugin{

    public function __construct() {
        parent::__construct();
        $this->startString = '<strong>NBB</strong>.';
        $this->endString = '<strong>NBE</strong>.';  
        $this->grouprelationname = array('nt');  
        $this->label = 'Node body';
        $this->command = 'NB';
        $this->icon = 'images/NB.png';
    }

}

