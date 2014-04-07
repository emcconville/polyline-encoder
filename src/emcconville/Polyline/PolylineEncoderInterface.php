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
 * @package PolylineEncoderInterface
 * @author  E. McConville <emcconville@emcconville.com>
 * @version 1.1
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @link    https://github.com/emcconville/polyline-encoder
 */   
namespace emcconville\Polyline
{
    /**
     * Inform a delegate that a given class uses polyline encoding
     */
    interface PolylineEncoderInterface
    {
        /**
         * Convert array of points into compressed ANSI string
         *
         * Points should be given to this method in lists of two.
         * array(
         *   array({latitude1},{longitude1}),
         *   array({latitude2},{longitude2}),
         *   ...
         *   array({latitudeN},{longitudeN}),
         * )
         *
         * @param array $points
         * @return string
         */
        public function encodePoints($points);

        /**
         * Convert ANSI string into array of points
         *
         * @param string $string
         * @return array or FALSE on error
         */
        public function decodeString($string);
    }
}
