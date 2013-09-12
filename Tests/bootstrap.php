<?php
/**
 * File containing the unit tests bootstrap file.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

$file = __DIR__ . '/../vendor/autoload.php';
if ( !file_exists( $file ) )
{
    throw new RuntimeException( 'Install dependencies to run test suite.' );
}

require_once $file;
