<?php

// Can be called by "Console/cake index"

class HelloShell extends AppShell {
	public function main() {
		$this->out('Hello world.');
	}
}