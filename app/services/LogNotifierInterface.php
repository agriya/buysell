<?php
class LogNotifierInterface implements NotifierInterface {
	public function notify($user, $data)
	{
		$this->logit('Date: '.date('Y-m-d H:i:s'));
		$this->logit("to('foo@notify.com', 'John notify')");
		$this->logit("subject('Webshop notify: EmailNotifierInterface@handle!')");
		foreach($data as $eachdata){
			$this->logit($eachdata);
		}
		$this->logit("=================================================");
	}

	public function logit($content)
	{
		$fp = fopen('notifier.txt', 'a+');
		fwrite($fp, $content.PHP_EOL);
		fclose($fp);
	}
}
?>