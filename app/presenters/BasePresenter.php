<?php

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

	public function beforeRender() {
		$parameters = $this->context->getParameters();
		$this->template->title = $parameters[ "title" ];
		$this->template->showAdministration = $parameters[ "show_administration" ];
	}

}
