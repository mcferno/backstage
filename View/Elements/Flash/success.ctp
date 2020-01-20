<?php
/**
 * Wrapper for alert with a specific success class
 */
$alertClass[] = 'alert-success';

if (!empty($class)) {
	$alertClass[] = $class;
}
echo $this->element('Flash/alert', array('alert_class' => implode(' ', $alertClass), 'message' => $message));
