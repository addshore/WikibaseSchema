<?php

namespace Wikibase\Schema\MediaWiki;


use FormAction;
use Status;

class SchemaSubmitAction extends SchemaEditAction {


	public function getName() {
		return 'submit';
	}
}
