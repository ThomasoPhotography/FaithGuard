<?php

class Testimonies {

    public $id;
    public $quote;
    public $cite;
    public $approved;
    public $created;

    public function __construct($parId = -1, $parQuote = "", $parCite = "", $parApproved = 0,$parCreated = "",) {
        $this->id = $parId;
        $this->quote = $parQuote;
        $this->cite = $parCite;
        $this->approved = $parApproved;
        $this->created = $parCreated;
    }
}

?>