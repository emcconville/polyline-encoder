<?php
/**
 * Bing Maps - Point Compression Algorithm
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
    define('BING_SAFE_CHARACTERS','ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-');
    
    trait BingTrait
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
         * @link http://msdn.microsoft.com/en-us/library/jj158958.aspx
         * @param array $points;
         * @return string
         */
        public function encodePoints($points)
        {
            assert(is_array($points));
            $previous = array(0,0);
            $return   = "";
            foreach($points as $point)
            {
                // Convert to 32bit integer
                $latitude  = (int)round(array_shift($point) * 1e5);
                $longitude = (int)round(array_shift($point) * 1e5);
                
                // Find delta
                $deltaLatitude  = $latitude  - $previous[0];
                $deltaLongitude = $longitude - $previous[1];
                
                // Save current diff for next calculation
                $previous = array($latitude,$longitude);
                
                // Record number signing
                $deltaLatitude  = ($deltaLatitude  << 1) ^ ($deltaLatitude  >> 31);
                $deltaLongitude = ($deltaLongitude << 1) ^ ($deltaLongitude >> 31);
                
                // Calculate diagonal line
                $index = (($deltaLatitude + $deltaLongitude    )
                       *  ($deltaLatitude + $deltaLongitude + 1)
                       / 2 ) + $deltaLatitude;
                
                while($index > 0)
                {
                    // Extract what would be the modulus
                    $remainder = $index & 31;
                    
                    // Reduce line
                    $index = ($index - $remainder) / 32;
                    
                    // Not a terminating bit, so use lowercase characters
                    if ( $index > 0 ) $remainder += 32;
                    
                    // Concatenate character
                    $return .= substr(BING_SAFE_CHARACTERS,$remainder,1);
                }
            }
    
            return $return;
        }

        /**
         * Convert ANSI string into array of points
         *
         * @link http://msdn.microsoft.com/en-us/library/dn306801.aspx
         * @param string $encoded
         * @return array or FALSE on failure
         */
        public function decodeString($str)
        {
            assert(is_string($str));
            $length   = strlen($str);
            $cursor   = 0;
            $previous = array(0,0);
            $points   = array();
            while($cursor < $length)
            {
                // Rest line & counter
                $index = $bytes = 0;
                while(true)
                {
                    // Read input
                    $remainder = strpos(BING_SAFE_CHARACTERS,$str[$cursor++]);
                    if( $remainder === FALSE )
                        return FALSE;
                    // Mask top bit and shift by bytes read
                    $index |= ( $remainder & 31 ) << $bytes;
                    $bytes += 5; // Increment accumulator
                    if($remainder < 32) break; // Last remainder
                }

                // Find diagonal from index line
                $diagonal = (int)((sqrt(8 * $index + 5) - 1) / 2);
                // Reduce line from to lower point
                $index -= $diagonal * ($diagonal + 1) / 2;
                $deltaLatitude  = (int)$index;
                $deltaLongitude = $diagonal - $deltaLatitude;
                
                // Apply signing
                $deltaLatitude  = ($deltaLatitude  >> 1) ^ -($deltaLatitude  & 1);
                $deltaLongitude = ($deltaLongitude >> 1) ^ -($deltaLongitude & 1);
                
                // Apply delta from previous point
                $previous[0] = $latitude  = $previous[0] + $deltaLatitude;
                $previous[1] = $longitude = $previous[1] + $deltaLongitude;
                
                // Return precision
                $points[] = array( $latitude * 1e-5,$longitude * 1e-5 );
            }
            return $points;
        }
    }
}