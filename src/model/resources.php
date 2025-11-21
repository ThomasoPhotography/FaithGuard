<?php

class Resources{

    public $id;
    public $title;
    public $description;
    public $category;
    public $type;
    public $difficulty;
    public $tags;
  

    public function __construct($parId=-1, $parTitle ="", $parDescription ="", $parCategory ="", $parType ="", $parDifficulty ="", $parTags=""){
        $this->id = $parId;
        $this->title = $parTitle;
        $this->description = $parDescription;
        $this->category = $parCategory;
        $this->type = $parType;
        $this->difficulty = $parDifficulty;
        $this->tags = $parTags;
    }
}


?>