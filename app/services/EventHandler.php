<?php

use Cartalyst\Sentry\UserNotFoundException;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserAlreadyActivatedException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\WrongPasswordException;
use Cartalyst\Sentry\Users\UserNotActivatedException;
use Cartalyst\Sentry\Throttling\UserSuspendedException;
use Cartalyst\Sentry\Throttling\UserBannedException;

class EventHandler {

	public function __construct()
	{
		$this->notifier = new EmailNotifierInterface();
		//$this->notifier = new LogNotifierInterface();
	}

	public function handle($user)
  	{
  		$data['mail_template'] = 'emails.commonEmail';
  		$data['subject'] = 'Webshop: Listening to events using classes@handle!';
	    $data['content'] = 'To register a listener using a class instead of a closure, you pass the name of the class as the second parameter, rather than closure:';
//	    Mail::send('emails.commonEmail', $data, function($message)
//		{
//		    $message->to('foo@example.com', 'John Smith')->subject('Webshop: Listening to events using classes@handle!');
//		});
		$this->notifier->notify($user, $data);
  	}

	public function sendActivationCode($user)
	{
		//DB::table('users')->whereRaw('id = ?', array($user->user_id))->update(array('activation_code' => time()));
		$data['mail_template'] = 'emails.auth.userActivation';
		$activation_code = $user->getActivationCode();
		$data['user'] = $user;
		$data['activationUrl'] = URL::to('users/activation/'.$activation_code);
		$data['subject'] = Lang::get('email.userActivation');

		$data['subject'] = Lang::get('emaiTemplates.activation_mail.subject');
		$content = Lang::get('emaiTemplates.activation_mail.content');
		$content = str_replace('VAR_USER_NAME', $user->first_name, $content);
		$content = str_replace('VAR_ACTIVATION_LINK', '<a href="'.URL::to('users/activation/'.$activation_code).'">'.URL::to('users/activation/'.$activation_code).'</a>', $content);
		//$user->first_name, $content);
		$data['content'] = $content;

//		Mail::send('emails.auth.userActivation', $data, function($m) use ($user){
//			$m->to($user->email, $user->first_name);
//			$m->subject(Lang::get('email.userActivation'));
//		});
		$this->notifier->notify($user, $data);
	}

	public function sendUserWelcomeMail($user)
	{
		$data['mail_template'] = "welcomeMailForUser";
		// Data to be used on the email view
		$data['user'] = $user;

		$data['subject'] = Lang::get('emaiTemplates.welcomeMailForUser.subject');
		$content = Lang::get('emaiTemplates.welcomeMailForUser.content');
		$content = str_replace('VAR_USER_NAME', $user->first_name, $content);
		
		$data['content'] = $content;

//		Mail::send($mail_template, $data, function($m) use ($user){
//			$m->to($user->email, $user->first_name);
//			$m->subject(Lang::get('email.welcomeMailForUser'));
//		});
		$this->notifier->notify($user, $data);
	}

	public function sendUserForgetPasswordMail($user)
	{

		//$data['mail_template'] = "forget_password";
		// Data to be used on the email view
		$data['user'] = $user['user'];

		$data['subject'] = Lang::get('emaiTemplates.forget_password.subject');

		$content = Lang::get('emaiTemplates.forget_password.content');
		$content = str_replace('VAR_USER_NAME', $user['user']->first_name, $content);
		$content = str_replace('VAR_RESET_LINK', '<a href="'.URL::to('users/reset-password/'.$user['token']).'">'.URL::to('users/reset-password/'.$user['token']).'</a>', $content);
		$data['content'] = $content;

		$this->notifier->notify($user['user'], $data);
		
	}

	public function sendNewMessagePostedMail($data){

		
		$subject = Lang::get('emaiTemplates.new_message_posted_for_user.subject');
		$subject = str_replace('VAR_FROM_USER_NAME', $data['from_user_details']['display_name'], $subject);
		$data['subject'] =$subject;
		
		$user = $data['to_user_details'];
		$content = Lang::get('emaiTemplates.new_message_posted_for_user.content');
		$content = str_replace('VAR_USER_NAME', $data['to_user_details']['display_name'], $content);
		$content = str_replace('VAR_FROM_USER_NAME', $data['from_user_details']['display_name'], $content);
		$content = str_replace('VAR_MESSAGE_LINK', '<a href="'.$data['message_view_link'] .'">Click Here</a>', $content);

		$data['content'] = $content;

		$this->notifier->notify($user, $data);

	}

	//update teh colleciion view cont
	public function getUpdateCollectionViewCount($collection){

		if(is_object($collection) && isset($collection->id) && $collection->id > 0)
		{
			$collection_count = Cookie::get('collection_view_count');
			if($collection_count != '')
			{
				$viewed_collections = explode(',',$collection_count);
				if(!in_array($collection->id,$viewed_collections))
				{
					Collection::where('id',$collection->id)->increment('total_views');

					array_push($viewed_collections, $collection->id);
					$new_cookie_value = implode(',',$viewed_collections);
					Cookie::queue('collection_view_count', $new_cookie_value, 24*60);
				}
			}
			else
			{
				Collection::where('id',$collection->id)->increment('total_views');
				Cookie::queue('collection_view_count', $collection->id, 24*60);
			}
		}
	}

	//increase collection comment count
	public function getUpdateCollectionCommentCount($comment_info, $action = 'increment'){
		if(is_array($comment_info) && isset($comment_info['collection_id']) && $comment_info['collection_id'] > 0) {
			if($action == 'increment')
				Collection::whereRaw('id = ? AND user_id = ?', array($comment_info['collection_id'], $comment_info['user_id']))->increment('total_comments');
			else
				Collection::whereRaw('id = ? AND user_id = ?', array($comment_info['collection_id'], $comment_info['user_id']))->decrement('total_comments');
		}
	}

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        $events->listen('auth.login', 'EventHandler');

        $events->listen('send.activation.code', 'EventHandler@sendActivationCode');

        $events->listen('send.welcome.mail', 'EventHandler@sendUserWelcomeMail');

        $events->listen('send.forgetpassword.mail', 'EventHandler@sendUserForgetPasswordMail');

        $events->listen('send.newmessageposted.mail', 'EventHandler@sendNewMessagePostedMail');

        $events->listen('collection.viewcount', 'EventHandler@getUpdateCollectionViewCount');

		$events->listen('collectioncomment.updatecount', 'EventHandler@getUpdateCollectionCommentCount');

    }
}
?>