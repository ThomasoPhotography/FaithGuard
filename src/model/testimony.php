<?php

class Testimony
{
    public $testimony_id;
    public $user_id;
    public $quote_text;
    public $cite_name;
    public $approved;
    public $created_at;

    public function __construct($parTestimonyId = -1, $parUserId = -1, $parQuoteText = "", $parCiteName = "", $parApproved = false, $parCreatedAt = "")
    {
        $this->testimony_id = $parTestimonyId;
        $this->user_id = $parUserId;
        $this->quote_text = $parQuoteText;
        $this->cite_name = $parCiteName;
        $this->approved = $parApproved;
        $this->created_at = $parCreatedAt;
    }
}
?>