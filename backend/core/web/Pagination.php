<?php
/**
 * Created by PhpStorm.
 * User: Насухов
 * Date: 23.05.2015
 * Time: 20:09
 */

namespace app\core\web;


use app\core\App;

class Pagination {

    private $currentUrl;            //current request url
    public $currentPage;            //current page number
    private $visiblePages = 10;     //count of pages element visible in pagination
    private $pattern;               //page pattern to be displayed in pagination and parsed from url to current page
    private $regExPattern;          // pattern converted to regEx format
    private $perPage;               //Count of elements on each page
    private $totalElementsCount;    //Total elements count
    private $totalPages;            //total pages count based on elements count
    private static $parsedUrl;      //parsed current url



    public function __construct($perPage = 10, $elementsCount = 0, $pagePattern = 'page={{number}}')
    {
        $this->currentUrl = $_SERVER["REQUEST_URI"];
        if(self::$parsedUrl == null) {
            self::$parsedUrl = parse_url($this->currentUrl);
        }
        $this->perPage = intval($perPage);
        $this->totalElementsCount = intval($elementsCount);
        $this->totalPages = ceil($this->totalElementsCount/$this->perPage);
        $this->pattern = $pagePattern;
        $this->regExPattern = $this->patternToRegExPattern();
        $this->currentPage = $this->parseCurrentPage();
    }



    //generate pagination
    public function pagination(){

        if(App::pluginIsActive('bootstrap')) {
            return $this->bootstrapPagination();
        }

        return $this->simplePagination();

    }


    private function bootstrapPagination()
    {

       $pagination =  '
            <div class="paginationBlock"></div>
                <script>
                    $(".paginationBlock").bootpag({
                        total: "'.$this->totalPages.'",
                        page: "'.$this->currentPage.'",
                        maxVisible: "'.$this->visiblePages.'",
                        href: "?'.$this->pattern.'",
                        leaps: true,
                        firstLastUse: true,
                        first: "←",
                        last: "→",
                        wrapClass: "pagination",
                        activeClass: "active",
                        disabledClass: "disabled",
                        nextClass: "next",
                        prevClass: "prev",
                        lastClass: "last",
                        firstClass: "first"
                    }).on("page", function(event, /* page number here */ num){
                        //ajax action here
                    });
                </script>
            </div>';

        return $pagination;

    }



    private function simplePagination()
    {
        $html = '';

        if( $this->totalElementsCount !== 0 ){

            $offset = floor($this->currentPage/$this->visiblePages)*$this->visiblePages;

            if($this->currentPage%$this->visiblePages === 0){
                $offset = $offset === 0 ? $offset  : $offset-$this->visiblePages;
            }

            $fromPage = $offset + 1;
            $toPage = $offset + $this->visiblePages;


            if($toPage>$this->totalPages){
                $toPage = $$this->totalPages;
            }


            if($this->currentPage>$this->visiblePages){
                $html .= '<a href="' . $this->collectUrl($offset) . '"/>←'.($offset-$this->visiblePages).'-'.$offset.'</a>&emsp;';
            }

            for($i=$fromPage; $i<=$toPage; $i++){

                if($i!=$this->currentPage){
                    $html .= '<a href="' .$this->collectUrl($i). '"/>'.$i.'</a>&emsp;';
                } else{
                    $html .= '<span id="current_page" >'.$i.'</span>&emsp;';
                }

            }
            if($this->totalPages > $toPage){
                $max = $offset + ($this->visiblePages*2)>$this->totalPages ? $this->totalPages : $offset+($this->visiblePages*2);
                $upTo = $offset+11==$max ? $max : $offset+11 . '-' . $max;
                $html .= '<a href="' . $this->collectUrl($toPage+1) . '"/>→'.$upTo.'</a>&emsp;';
            }
        }
        return $html;
    }



    //Detect current page number
    private function parseCurrentPage()
    {
        if(!empty(self::$parsedUrl['query'])) {
            preg_match('#'.$this->regExPattern.'#', self::$parsedUrl['query'], $match);
            $page = !empty($match[1]) ? $match[1] : 1;
        } else{
            $page = 1;
        }

        return $page;
    }



    private function patternToRegExPattern()
    {
        return preg_replace('({{.+?}})', '(\d+)', $this->pattern);
    }




    private function collectUrl($pageNumber = 1)
    {

        if (!empty(self::$parsedUrl['query'])) { //if url has query params
            $pageIdentifier = preg_replace('#=\(\\\d\+\)#', '='.$pageNumber, $this->regExPattern);
            if(preg_match('#'.$this->regExPattern.'#', self::$parsedUrl['query'], $match)){ //if page already exists in query params replace it
                $url = self::$parsedUrl['path'] . '?' . preg_replace('#' . $match[0] .'#', $pageIdentifier, self::$parsedUrl['query']);
            } else{ //if page pattern doesn't exist in query params just add it to query
                $url = self::$parsedUrl['path'] . '?' . self::$parsedUrl['query'] . '&' . $pageIdentifier;
            }
        } else { //if there are no query params
            $url = self::$parsedUrl['path'] . '?' . preg_replace('#=\(\\\d\+\)#', '='.$pageNumber, $this->regExPattern);
        }


        return $url;
    }



}