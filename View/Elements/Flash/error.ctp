<?php
/**
 * Wrapper for alert with a specific error class
 */
$alertClass[] = 'alert-danger';

if (!empty($class)) {
	$alertClass[] = $class;
}
echo $this->element('Flash/alert', array('alert_class' => implode(' ', $alertClass), 'message' => $message));
