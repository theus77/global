<?php
App::uses('AppHelper', 'View/Helper');

class I18nHelper extends AppHelper {
	
	public $helpers = array('Html');
	
	public function t($id){		
		$i18n = &$this->_View->viewVars['i18n'];
		
		if(isset($i18n[$id]) && strlen($i18n[$id])>0){
			
			$params = func_get_args();
			$params[0] = $i18n[$id];
			
			return htmlspecialchars(call_user_func_array('sprintf', $params));
		}
		return $id;
	}


	public function w($id){
		$i18n = &$this->_View->viewVars['i18n'];
	
		$out = __('Traduction manquante pour la clé "%s".', $id);
		
		if(isset($i18n[$id]) && strlen($i18n[$id]) > 0){
			$out = $i18n[$id];
		}		
		
		$tagOptions = array(
				'class' => 'wysiwygContent',
		);
				
		if(AuthComponent::user() && AuthComponent::user()['role'] === 'admin'){
			//$tagOptions['contenteditable'] = 'true';
			$tagOptions['data-update-url'] = $this->Html->url(array('controller' => 'wysiwygs', 'action' => 'update', 'slug' => $id, 'ext' => 'json', 'language' => Configure::read('Config.language')));
		}/**/
		
		return $this->Html->tag('div', $out, $tagOptions);
		
	}
}