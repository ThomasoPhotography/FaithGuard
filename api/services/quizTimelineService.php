<?php

require_once __DIR__ . '/../db/FaithGuardRepository.php';

    /*=========================================================
        QUIZ TIMELINE SERVICE
    ========================================================= */
    class QuizTimelineService
    {
    /* =====================================================
        PUBLIC API
    ===================================================== */
    public static function buildUserTimelines(int $userId): array{
        $timelines  = [];
        $addictions = FaithGuardRepository::getUserAddictionHistory($userId);
        foreach ($addictions as $row) {
            $addiction             = $row['addiction_type'];
            $timelines[$addiction] = self::buildAddictionTimeline(
                $userId,
                $addiction
            );
        }
        return $timelines;
    }
    public static function buildAddictionTimeline(int $userId, string $addiction): array{
        $entries = FaithGuardRepository::getAddictionTimeline($userId, $addiction);
        return [
            'addiction' => $addiction,
            'history'   => self::formatTimeline($entries),
            'summary'   => self::summarizeTrend($entries),
        ];
    }

    /* =====================================================
        FORMATTERS
    ===================================================== */
    private static function formatTimeline(array $rows): array{
        $timeline = [];
        foreach ($rows as $row) {
            $timeline[] = [
                'date'  => date('Y-m-d', strtotime($row['taken_at'])),
                'score' => (int) $row['score'],
            ];
        }
        return $timeline;
    }

    /* =====================================================
        INTERPRETATION
    ===================================================== */
    private static function summarizeTrend(array $rows): array{
        if (count($rows) < 2) {
            return [
                'trend'   => 'emerging',
                'message' => 'This journey has only recently begun.',
            ];
        }
        $first = (int) $rows[0]['score'];
        $last  = (int) end($rows)['score'];
        $delta = $last - $first;
        if ($delta < 0) {
            return [
                'trend'   => 'improving',
                'message' => 'There has been meaningful progress over time.',
            ];
        }
        if ($delta > 0) {
            return [
                'trend'   => 'worsening',
                'message' => 'This struggle has grown heavier and deserves care.',
            ];
        }
        return [
            'trend'   => 'steady',
            'message' => 'This area has remained stable over time.',
        ];
    }
}
