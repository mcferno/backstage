<?php
/**
 * Wrapper for alert with a specific informative class
 */
$alertClass[] = 'alert-info';

if (!empty($class)) {
	$alertClass[] = $class;
}
echo $this->element('Flash/alert', array('alert_class' => implode(' ', $alertClass), 'message' => $message));
