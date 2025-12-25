<?php
require_once __DIR__ . '/../db/FaithGuardRepository.php';
class PastoralJourneyService
{
    public static function buildJourney(
        int $userId,
        string $addiction,
        int $severityScore,
        array $answers
    ): array {
        $stage = self::determineStage($severityScore, $answers);
        $tags  = self::buildTagProfile($addiction, $stage, $answers);
        $resources = FaithGuardRepository::getResourcesByTags($tags);
        $rankedResources = self::rankResources(
            $resources,
            $tags,
            $addiction,
            $severityScore
        );
        return [
            'addiction' => $addiction,
            'severity'  => $severityScore,
            'stage'     => $stage,
            'journey'   => self::formatJourney($stage, $rankedResources)
        ];
    }
    /* -----------------------------
       STAGE DETERMINATION
    ----------------------------- */
    private static function determineStage(int $score, array $answers): string
    {
        if ($score >= 80) {
            return 'grace';
        }
        if ($score >= 60) {
            return 'repentance';
        }
        if ($score >= 40) {
            return 'discipline';
        }
        return 'hope';
    }

    /* -----------------------------
       TAG PROFILE
    ----------------------------- */
    private static function buildTagProfile(
        string $addiction,
        string $stage,
        array $answers
    ): array {
        $tags = [$addiction, $stage];
        // Soft pastoral inference
        foreach ($answers as $value) {
            if ($value >= 4) {
                $tags[] = 'struggle';
            }
            if ($value <= 2) {
                $tags[] = 'hope';
            }
        }
        return array_unique($tags);
    }

    /* -----------------------------
       RESOURCE RANKING
    ----------------------------- */
    private static function rankResources(
        array $resources,
        array $tags,
        string $addiction,
        int $severity
    ): array {
        foreach ($resources as &$resource) {
            $score = 0;
            $resourceTags = array_map('trim', explode(',', $resource['tags']));
            // Tag matches
            foreach ($tags as $tag) {
                if (in_array($tag, $resourceTags, true)) {
                    $score += 2;
                }
            }
            // Addiction priority
            if (in_array($addiction, $resourceTags, true)) {
                $score += 5;
            }
            // Severity influence
            if ($severity >= 80) {
                $score += 3;
            } elseif ($severity >= 60) {
                $score += 2;
            }
            $resource['_relevance'] = $score;
        }
        usort($resources, fn($a, $b) => $b['_relevance'] <=> $a['_relevance']);
        return $resources;
    }

    /* -----------------------------
       JOURNEY FORMATTING
    ----------------------------- */
    private static function formatJourney(string $stage, array $resources): array
    {
        $journey = [];
        foreach ($resources as $resource) {
            $journey[] = [
                'title'   => $resource['title'],
                'slug'    => $resource['slug'],
                'stage'   => $stage,
                'excerpt' => mb_substr(strip_tags($resource['content_text']), 0, 160) . '…'
            ];
        }
        return $journey;
    }
}
