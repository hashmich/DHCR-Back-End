<?php
/**
 * Copyright 2014 Hendrik Schmeer on behalf of DARIAH-EU, VCC2 and DARIAH-DE,
 * Credits to Erasmus University Rotterdam, University of Cologne, PIREH / University Paris 1
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
class CronShell extends AppShell {
    
	public $tasks = array(
		'CheckUrls',
		'SendReminders',
		'Test'
	);
	
	private $to = null;
	private $sendMails = null;
	
	public function main() {
        $this->out("Available tasks: \n\t ");
		foreach($this->tasks as $task) {
			$this->out($task . " (" . strtoupper($task[0]) . ")\n\t");
		}
		$this->out("Please note: \nperforming these tasks will send out emails to recipients, \nif the application is not in debug-mode.\nYou can enter an alternative debug-mail recipient.");
		$this->hr();
		
		$task = $this->in('Choose an action', array('C','S','T','Q'), 'Q');
		
		if(strtolower($task) != 'q') {
			
			
			if(strtolower($task) == 'c') {
				$this->__emailSettings();
				$this->CheckUrls->execute($this->sendMails, $this->to);
			}
			elseif(strtolower($task) == 's') {
				$this->__emailSettings();			
				$this->SendReminders->execute($this->sendMails, $this->to);
			}
			elseif(strtolower($task) == 't') {
				$this->to = $this->in('Send an Email to: ', null, 'mail@hendrikschmeer.de');
				$this->Test->execute($this->to);
			}
		}else{
			$this->out("Exited on user request.");
		}
    }
    
    
    private function __emailSettings() {
    	$this->out("Send emails to the collected recipients or an alternative debug email address? \n
Hit Enter or type \"recipients\" to start mass emailing. Type in an alternative address for debugging.");
    	$this->to = $this->in('Send all Emails to...: ', null, 'recipients');
    	if($this->to == 'recipients') $this->to = null;
    	$this->sendMails = $this->in('Send Emails?', array('Y','N'), 'N');
    	if(strtolower($this->sendMails) == 'y') $this->sendMails = true;
    	elseif(strtolower($this->sendMails) == 'n') $this->sendMails = false;
    }
    
    
}


?>