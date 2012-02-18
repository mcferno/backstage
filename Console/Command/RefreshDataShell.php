<?php
// periodically refresh the API driven content
class RefreshDataShell extends AppShell {
	public $uses = array('Tumblr','Twitter');
	
    public function main() {
        $this->Tumblr->refresh();
        $this->Twitter->refresh();
    }
}