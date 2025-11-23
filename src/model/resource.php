<?php

class Resource
{
    public $resource_id;
    public $user_id;
    public $title;
    public $description;
    public $category;
    public $type;
    public $difficulty;
    public $tags;

    public function __construct($parResourceId = -1, $parUserId = -1, $parTitle = "", $parDescription = "", $parCategory = "", $parType = "", $parDifficulty = "", $parTags = "")
    {
        $this->resource_id = $parResourceId;
        $this->user_id = $parUserId;
        $this->title = $parTitle;
        $this->description = $parDescription;
        $this->category = $parCategory;
        $this->type = $parType;
        $this->difficulty = $parDifficulty;
        $this->tags = $parTags;
    }
}
?>