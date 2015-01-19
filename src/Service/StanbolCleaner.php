<?php
/**
 *  \details © 2014  Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

namespace Drupal\xowl\Service;


class StanbolCleaner {




    public  $replaceString = null ;
    public  $cleanedString = null ;
    public  $tags = null ;
    public $originalString = null ;
    public $offsets = null ;
    public function __construct( $string ,  $replaceString = '_' ) {
        $this->originalString = $string ;
        $this->replaceString = $replaceString ;
    }

    public function clean( ) {
        $stringCopy = $this->originalString ;

        $foundTags = array();
        $matches = [] ;
        preg_match_all( '#<script.*</script>|<a .*</a>|<.*>#iUs', $stringCopy, $matches, PREG_OFFSET_CAPTURE ) ;
        foreach( $matches[0] as $key =>  $match ) {
            $tag = $match[0];
            $starts = $match[1] ;
            $foundTags[ $starts ] = $tag ;
            $stringCopy = $this->subFromString( $stringCopy, $starts, strlen( $tag ) , str_pad( ' ', strlen( $tag ) ) ) ;
        }
        $this->cleanedString = $stringCopy ;
        $this->tags = $foundTags ;
        return $stringCopy ;


    }
    public function restore( ) {
        $newString =  $this->cleanedString ;
        $offsetChange = 0 ;
        foreach( $this->tags as $offset => $tag  ) {
            $newString = $this->subFromString( $newString, $offset, strlen( $tag ) ,   $tag ) ;
            $offsetChange +=  strlen( $tag ) -  strlen( $this->replaceString ) ;

        }
        return $newString ;

    }


    public function getNewOffset( $from , $limit = 0   ) {


        return mb_strlen( substr( $this->originalString, 0, strlen(  mb_substr( $this->cleanedString, 0, $from )  )  ));





    }

    public function subFromString( $string, $start, $length, $sub = '' ) {
        $result =  substr( $string , 0, $start ) . $sub  . substr( $string , $start + $length ) ;
        return $result ;
    }



}