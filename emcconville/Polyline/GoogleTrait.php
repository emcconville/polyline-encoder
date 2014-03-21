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
 * @package BingTrait
 * @author  E. McConville <emcconville@emcconville.com>
 * @version 1.0
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @link    https://github.com/emcconville/polyline-encoder
 */   
namespace emcconville\Polyline
{
    trait GoogleTrait
    {
        /**
         * Convert array of points into compressed ANSI string
         *
         * Points should be given to this method in lists of two.
         * array(
         *    array({latitude1},{longitude1}),
         *    array({latitude2},{longitude2}),
         *    ...
         *    array({latitudeN},{longitudeN}),
         * )
         *
         * @link https://developers.google.com/maps/documentation/utilities/polylinealgorithm
         * @param array $points;
         * @return string
         */
        public function encodePoints($points)
        {
            $prescision = 5;
            if(method_exists(get_class(),'polylinePrescision'))
                $prescision = (int)$this->polylinePrescision();
            $tuple = 2;
            if(method_exists(get_class(),'polylineTupleSize'))
                $tuple = (int)$this->polylineTupleSize();
    		$encoded_string = '';
    		$index = 0;
    		$previous = array_fill(0,$tuple,0);
    		foreach($points as $number) {
    			$number = (float)($number);
    			$number = floor($number * pow(10, $prescision));
    			$diff = $number - $previous[$index % $tuple];
    			$previous[$index % $tuple] = $number;
    			$number = $diff;
    			$index++;
    			$number = ($number < 0) ? ~($number << 1) : ($number << 1);
    			$chunk = '';
    			while($number >= 0x20) {
    				$chunk .= chr((0x20 | ($number & 0x1f)) + 63);
    				$number >>= 5;
    			}
    			$chunk .= chr($number + 63);
    			$encoded_string .= $chunk;
    		}
    		return $encoded_string;
        }
        
        /**
         * Convert ANSI string into array of points
         *
         * @link https://developers.google.com/maps/documentation/utilities/polylinealgorithm
         * @param string $encoded
         * @return array or FALSE on failure
         */
        public function decodeString($str)
        {
            $prescision = 5;
            if(method_exists(get_class(),'polylinePrescision'))
                $prescision = (int)$this->polylinePrescision();
            $tuple = 2;
            if(method_exists(get_class(),'polylineTupleSize'))
                $tuple = (int)$this->polylineTupleSize();
    		$points = array();
    		$index = $i = 0;
    		$previous = array_fill(0,$tuple,0);
    		while( $i < strlen($string)  ) {
    			$shift = $result = 0x00;
    			do {
    				$bit = ord(substr($string,$i++)) - 63;
    				$result |= ($bit & 0x1f) << $shift;
    				$shift += 5;
    			} while( $bit >= 0x20 ) ;

    			$diff = ($result & 1) ? ~($result >> 1) : ($result >> 1);
    			$number = $previous[$index % $tuple] + $diff;
    			$previous[$index % $tuple] = $number;
    			$index++;
    			$points[] = $number * 1 / pow(10, $prescision);
    		}
            if($tuple > 1)
            {
                $points = array_chunk($points,$tuple);
                
            }
    		return $points;
        }
    }
}