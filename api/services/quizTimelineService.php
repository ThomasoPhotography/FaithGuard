<?php
declare(strict_types=1);

class QuizTimelineService {
    public static function buildUserTimelines(int $userId): array {
        $results = FaithGuardRepository::getQuizResultsByUserId($userId);
        if (empty($results)) {
            return [];
        }
        $timeline = [];
        foreach ($results as $res) {
            $score = (float)$res['total_score'];
            
            $timeline[] = [
                'date' => $res['created_at'],
                'severity' => self::getSeverityLabel($score),
                'score' => $score,
                'addictions' => json_decode($res['addiction_type'], true) ?? []
            ];
        }
        return $timeline;
    }
    private static function getSeverityLabel(float $score): string {
        if ($score < 25) return 'Low';
        if ($score < 50) return 'Moderate';
        if ($score < 75) return 'High';
        return 'Critical';
    }
}