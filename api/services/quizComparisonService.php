<?php

require_once __DIR__ . '/../db/FaithGuardRepository.php';

    /* =========================================================
        QUIZ COMPARISON SERVICE
    ========================================================= */
    class QuizComparisonService{
    /* =====================================================
        PUBLIC ENTRY POINT
    ===================================================== */
    public static function getAttemptComparisons($userId, $currentAttemptId): void{
        $previousAttemptId = FaithGuardRepository::getPreviousAttemptId($userId, $currentAttemptId);
        $currentAddictions  = FaithGuardRepository::getAttemptAddictions($currentAttemptId);
        $previousAddictions = $previousAttemptId ? FaithGuardRepository::getAttemptAddictions($previousAttemptId) : [];
        foreach ($currentAddictions as $addiction => $currentScore) {
            /* ---------------------------------------------
                NEW ADDICTION DETECTED
            --------------------------------------------- */
            if (!isset($previousAddictions[$addiction])) {
                self::storeComparison(
                    $currentAttemptId,
                    $addiction,
                    null,
                    $currentScore * 5,
                    $currentScore * 5,
                    'new'
                );
                continue;
            }
            /* ---------------------------------------------
                EXISTING ADDICTION COMPARISON
            --------------------------------------------- */
            $previousScore = (int) $previousAddictions[$addiction];
            $delta         = $currentScore - $previousScore;
            $trend         = self::determineTrend($delta);
            self::storeComparison($currentAttemptId, $addiction, $previousScore, $currentScore, $delta, $trend);
        }
    }

    /* =====================================================
        TREND ANALYSIS
    ===================================================== */
    private static function determineTrend(int $delta): string{
        if ($delta < 0) {
            return 'improved';
        }
        if ($delta > 0) {
            return 'worsened';
        }
        return 'unchanged';
    }

    /* =====================================================
        PERSISTENCE LAYER
    ===================================================== */
    private static function storeComparison(int $attemptId, string $addiction, ?int $previous, int $current, int $delta, string $trend ): void {
        FaithGuardRepository::insertAttemptComparison([
            'attempt_id'     => $attemptId,
            'addiction_type' => $addiction,
            'previous_score' => $previous,
            'current_score'  => $current,
            'delta'          => $delta,
            'trend'          => $trend
        ]);
    }

    /* =====================================================
        USER-FACING SUMMARY BUILDERS
    ===================================================== */
    public static function buildUserSummary(array $comparisons): array{
        $summary = [];
        foreach ($comparisons as $row) {
            $summary[] = [
                'addiction' => ucfirst($row['addiction_type']),
                'previous'  => $row['previous_score'],
                'current'   => $row['current_score'],
                'delta'     => $row['delta'],
                'trend'     => $row['trend'],
                'message'   => self::trendMessage($row),
                'tone'      => self::trendTone($row['trend']),
            ];
        }
        return $summary;
    }

    /* =====================================================
        LANGUAGE & TONE MAPPING
    ===================================================== */
    private static function trendMessage(array $row): string{
        return match ($row['trend']) {
            'improved'  => 'There has been measurable progress since your last assessment.',
            'worsened'  => 'This area has become more difficult recently.',
            'unchanged' => 'This area remains steady since last time.',
            'new'       => 'This struggle has newly appeared in your journey.',
            default     => 'No change detected.',
        };
    }
    private static function trendTone(string $trend): string{
        return match ($trend) {
            'improved'  => 'positive',
            'unchanged' => 'neutral',
            'new'       => 'gentle',
            'worsened'  => 'supportive',
            default     => 'neutral',
        };
    }
}
