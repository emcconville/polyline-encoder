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
            $prev = array(0,0);
            $return = "";
            foreach($points as $point)
            {
                // Convert to 32bit integer
                $lat = (int)round(array_shift($point) * 1e5);
                $lon = (int)round(array_shift($point) * 1e5);
                
                // Find delta
                $dy = $lat - $prev[0];
                $dx = $lon - $prev[1];
                
                // Save current diff for next calculation
                $prev = array($lat,$lon);
                
                // Record number signing
                $dy = ($dy << 1) ^ ($dy >> 31);
                $dx = ($dx << 1) ^ ($dx >> 31);
                
                // Calculate diagonal line
                $index = (($dy + $dx) * ($dy + $dx + 1) / 2) + $dy;
                
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
            $len = strlen($str);
            $cursor = 0;
            $prev = array(0,0);
            $points = array();
            while($cursor < $len)
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

                // Find diagnoal from index line
                $diagonal = (int)((sqrt(8 * $index + 5) - 1) / 2);
                // Reduce line from to lower point
                $index -= $diagonal * ($diagonal + 1) / 2;
                $dy = (int)$index;
                $dx = $diagonal - $dy;
                
                // Apply signing
                $dy = ($dy >> 1) ^ -($dy & 1);
                $dx = ($dx >> 1) ^ -($dx & 1);
                
                // Apply delta from previous point
                $prev[0] = $lat = $prev[0] + $dy;
                $prev[1] = $lng = $prev[1] + $dx;
                
                // Return precision
                $points[] = array( $lat * 1e-5,$lng * 1e-5 );
            }
            return $points;
        }
    }
}