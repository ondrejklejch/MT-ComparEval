<?php

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

	private $google;

	private $usersModel;

	protected $isInsertAllowed;

	protected $canExperimentBeRemoved;

	protected $canTaskBeRemoved;

	public function startup() {
		parent::startup();

		$parameters = $this->context->getParameters();
		$user = $this->user;
		$this->isInsertAllowed = function() use ($parameters, $user) {
			return $parameters[ "show_administration" ] || $user->isLoggedIn();
		};

		$this->canExperimentBeRemoved = function($experiment) use ($parameters, $user) {
			return $parameters[ "show_administration" ] ||
				$user->isInRole( "admin" ) ||
				$user->isLoggedIn() && $experiment[ "created_by" ] === $user->getId();
		};

		$this->canTaskBeRemoved = function($task) use ($parameters, $user) {
			return $parameters[ "show_administration" ] ||
				$user->isInRole( "admin" ) ||
				$user->isLoggedIn() && $task[ "created_by" ] == $user->getId();
		};
	}


	public function actionLogout() {
		$this->user->logout();
		$this->redirect(':Experiments:list');
	}

	protected function createComponentGoogleLogin() {
		$this->usersModel = $this->context->getService('users');
		$this->google = $this->context->getByType('\Kdyby\Google\Google');

		$dialog = new \Kdyby\Google\Dialog\LoginDialog($this->google);
		$dialog->onResponse[] = function (\Kdyby\Google\Dialog\LoginDialog $dialog) {
			$google = $dialog->getGoogle();

			if (!$google->getUser()) {
				$this->flashMessage("We are sorry, we were unable to authentize you with Google.");
				return;
			}

			try {
				$me = $google->getProfile();

				if (!$existing = $this->usersModel->findByGoogleId($google->getUser())) {
					$existing = $this->usersModel->registerFromGoogle($google->getUser(), $me);
				}

				if ( in_array( $existing->email, $this->context->parameters["administrators"] ) ) {
					$existing->roles = array( "admin" );
				} else {
					$existing->roles = array();
				}

				$this->user->login(new \Nette\Security\Identity($existing->id, $existing->roles, $existing));
			} catch (\Exception $e) {
				\Tracy\Debugger::log($e, 'google');
				$this->flashMessage("We are sorry, we were unable to authentize you with Google.");
			}

			$this->redirect('this');
		};

		return $dialog;
	}

	public function beforeRender() {
		$parameters = $this->context->getParameters();
		$this->template->title = $parameters[ "title" ];
		$this->template->isInsertAllowed = $this->isInsertAllowed;
		$this->template->canExperimentBeRemoved = $this->canExperimentBeRemoved;
		$this->template->canTaskBeRemoved = $this->canTaskBeRemoved;
	}

}
