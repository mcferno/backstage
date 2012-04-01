<?php
App::uses('Helper', 'View');
App::uses('FormHelper', 'View/Helper');
/**
 * Extending the default FormHelper to make minor interface tweaks
 */
class AppFormHelper extends FormHelper {
	
	public function addClass($options = array(), $class = null, $key = 'class') {
		$newClass = parent::addClass($options,$class,$key);
		
		// inject bootstrap friendly class names
		if(isset($newClass['class']) && strpos($newClass['class'],'control-group') === false) {
			$newClass['class'] .= ' control-group';
		}
		return $newClass;
	}
	
	public function error($field, $text = null, $options = array()) {
		// inject bootstrap friendly class names
		if(isset($options['class'])) {
			$options['class'] .= ' help-inline';
		} else {
			$options['class'] = 'error-message help-inline';
		}
		return parent::error($field,$text,$options);
	}
}