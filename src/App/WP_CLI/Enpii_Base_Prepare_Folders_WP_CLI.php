<?php

declare(strict_types=1);

namespace Enpii_Base\App\WP_CLI;

use Enpii_Base\App\Actions\WP_CLI\Prepare_WP_App_Folders_Action;

class Enpii_Base_Prepare_Folders_WP_CLI {
	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
	public function __invoke( $args ) {
		Prepare_WP_App_Folders_Action::exec();

		// Return 0 to tell that everything is alright
		exit( 0 );
	}
}
