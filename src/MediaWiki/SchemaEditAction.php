<?php

namespace Wikibase\Schema\MediaWiki;

use CommentStoreComment;
use MediaWiki\MediaWikiServices;
use MediaWiki\Storage\PageUpdater;
use Wikibase\Schema\MediaWiki\Content\WikibaseSchemaContent;

class SchemaEditAction extends \FormAction {
//	public function show() {
//		$this->useTransactionalTimeLimit();
//
//		$out = $this->getOutput();
//		$out->setRobotPolicy( 'noindex,nofollow' );
//		if ( $this->getContext()->getConfig()->get( 'UseMediaWikiUIEverywhere' ) ) {
//			$out->addModuleStyles( [
//				'mediawiki.ui.input',
//				'mediawiki.ui.checkbox',
//			] );
//		}
//		$page = $this->page;
//		$user = $this->getUser();
//
//		$editPage = new SchemaEditPage( $page );
//		$editPage->setContextTitle( $this->getTitle() );
//		$editPage->edit();
//	}


	protected function getFormFields() {

		$content = $this->getContext()->getWikiPage()->getContent();
		if ( $content ) { //FIXME: handle this better

			$schema = json_decode( $content->getNativeData(), true );
		} else {
			$schema = [
				'description' => [
					'en' => '',
				],
				'schema' => '',
			];
		}
//		var_dump($schema);
//		exit;

		return [
			'description' => [
				'type' => 'text',
				'default' => $schema[ 'description' ][ 'en' ],
			],
			'schema' => [
				'type' => 'textarea',
				'default' => $schema[ 'schema' ],
			],
		];
	}


	/**
	 * Process the form on POST submission.
	 *
	 * If you don't want to do anything with the form, just return false here.
	 *
	 * This method will be passed to the HTMLForm as a submit callback (see
	 * HTMLForm::setSubmitCallback) and must return as documented for HTMLForm::trySubmit.
	 *
	 * @see HTMLForm::setSubmitCallback()
	 * @see HTMLForm::trySubmit()
	 *
	 * @param array $data
	 *
	 * @return bool|string|array|Status Must return as documented for HTMLForm::trySubmit
	 */
	public function onSubmit( $data ) {
		/**
		 * @var $content WikibaseSchemaContent
		 */
		$content = $this->getContext()->getWikiPage()->getContent();
		$dataToSave = json_encode( $this->formDataToSchemaArray( $data ) );
		if ( $content ) {
			$content->setNativeData( $dataToSave );
		} else {
			$content = new WikibaseSchemaContent( $dataToSave );
		}

		$updater = $this->page->getPage()->newPageUpdater( $this->context->getUser() );
		$updater->setContent( 'main', $content );
		$updater->saveRevision(
			CommentStoreComment::newUnsavedComment( 'abc' )
		);

		return true;
	}

	private function formDataToSchemaArray( array $formData ) {
		return [
			'schema' => $formData[ 'schema' ],
			'description' => [
				'en' => $formData[ 'description' ],
			],
		];
	}

	/**
	 * Do something exciting on successful processing of the form.  This might be to show
	 * a confirmation message (watch, rollback, etc) or to redirect somewhere else (edit,
	 * protect, etc).
	 */
	public function onSuccess() {
		$redirectParams = $this->getRequest()->getVal( 'redirectparams', '' );
		$this->getOutput()->redirect( $this->getTitle()->getFullURL( $redirectParams ) );
	}

	/**
	 * Return the name of the action this object responds to
	 *
	 * @since 1.17
	 *
	 * @return string Lowercase name
	 */
	public function getName() {
		return 'edit';
	}
}
