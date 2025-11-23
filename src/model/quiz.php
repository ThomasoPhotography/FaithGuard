<?php

class Quiz
{
    public $quiz_id;
    public $user_id;
    public $duration;
    public $accountability;
    public $resources_of_interest;
    public $spiritual_connection;
    public $primary_goal;
    public $created_at;

    public function __construct($parQuizId = -1, $parUserId = -1, $parDuration = "", $parAccountability = "", $parResourcesOfInterest = "", $parSpiritualConnection = "", $parPrimaryGoal = "", $parCreatedAt = "")
    {
        $this->quiz_id = $parQuizId;
        $this->user_id = $parUserId;
        $this->duration = $parDuration;
        $this->accountability = $parAccountability;
        $this->resources_of_interest = $parResourcesOfInterest;
        $this->spiritual_connection = $parSpiritualConnection;
        $this->primary_goal = $parPrimaryGoal;
        $this->created_at = $parCreatedAt;
    }
}
?>