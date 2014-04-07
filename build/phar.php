<?php
/**
 * Google Maps - Encoded Polyline Algorithm
 * 
 * This library is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @sub-package build/phar.php
 * @author  Eric McConville <emcconville@emcconville.com>
 * @version 1.0.2
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @link    https://github.com/emcconville/polyline-encoder
 * @link    https://bitbucket.org/emcconville/polyline-encoder
 */

define('ROOT_DIR',realpath(__DIR__.DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR);
define('SRC_DIR',ROOT_DIR.'src'.DIRECTORY_SEPARATOR);
define('EXT','.phar');
define('PACKAGE_NAME','polyline-encoder');
define('PACKAGE_VERSION',trim(`git describe`));
define('PACKAGE',PACKAGE_NAME.'-'.PACKAGE_VERSION);
define('PACKAGE_PHAR',PACKAGE.EXT);

@unlink(ROOT_DIR.PACKAGE_PHAR);

$build = new Phar(ROOT_DIR.PACKAGE_PHAR,0,PACKAGE_NAME.EXT);

$directory = new RecursiveDirectoryIterator(SRC_DIR);
$iterator  = new RecursiveIteratorIterator($directory);
$matches   = new RegexIterator($iterator,'!\.php$!');

$build->startBuffering();
foreach($matches as $stub)
{
    $stubFilename = basename($stub); // No need to keep directory structure
    $stubContent  = php_strip_whitespace($stub); // Minify code
    $build->addFromString($stubFilename,$stubContent);
}
// Nothing special, or any need for autoload. Just include the the two 
// traits for immediate use.
$main=<<<END_OF_MAIN
<?php
require_once('phar://polyline-encoder.phar/BingTrait.php');
require_once('phar://polyline-encoder.phar/GoogleTrait.php');
__HALT_COMPILER();
END_OF_MAIN;
$build->setStub($main);
$build->stopBuffering();


