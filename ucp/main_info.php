<?php
/**
 *
 * Delete My Account. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017 BrokenCrust
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace pgreca\pg_social\ucp;

// Define the new UCP tab and single page
class main_info {
	function module() {
		return array(
			'filename' => '\pgreca\pg_social\ucp\main_module',
			'title' => 'UCP_PG_SOCIAL_MAIN',
			'modes' => array(
				'chat' => array(
					'title'	=> 'UCP_PG_SOCIAL_CHAT',
					'auth' => 'ext_pgreca/pg_social', 
					'cat' => array('UCP_PG_SOCIAL_MAIN'),
				),
			),
		);
	}
}
