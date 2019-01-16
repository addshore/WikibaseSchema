<?php

namespace Wikibase\Schema\MediaWiki\Content;

use Html;
use ParserOptions;
use ParserOutput;
use Title;

class WikibaseSchemaContent extends \JsonContent {

	const CONTENT_MODEL_ID = 'WikibaseSchema';

	public function __construct( $text, $modelId = self::CONTENT_MODEL_ID ) {
		parent::__construct( $text, $modelId );
	}

	protected function fillParserOutput(
		Title $title,
		$revId,
		ParserOptions $options,
		$generateHtml,
		/** @noinspection ReferencingObjectsInspection */
		ParserOutput &$output
	) {

		if ( $generateHtml && $this->isValid() ) {
			$output->setText( $this->schemaJsonToHtml(
				json_decode( $this->getNativeData(), true )
			) );
		} else {
			$output->setText( '' );
		}
	}

	private function schemaJsonToHtml( array $schema ) {
		return
			Html::element(
				'h3',
				[],
				$schema['description']['en']
			)
			. Html::element(
			'pre',
			[],
			$schema['schema']
		);
	}

	public function setNativeData( $data ) {
		$this->mText = $data;
	}

}
