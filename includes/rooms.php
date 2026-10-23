<?php
declare (strict_types = 1);

/** The eight rooms. Scripture is the front door; the others follow in the order shown on the home page. */
function rooms(): array
{
    static $rooms = null;

    if ($rooms === null) {
        $data = [
            'scripture'  => [
                'name'    => 'Scripture',
                'short'   => 'The front door',
                'summary' => 'Scripture is where FaithGuard begins: a passage to read slowly, in English or Dutch, before anything else on your screen.',
                'body'    => [
                    'Each day starts with the Word instead of the feed. You choose a Bible version, a book and a chapter, and read at your own pace.',
                    'Scripture is the thread through every other room. Formation draws on it, prayer returns to it, and small groups study it together.',
                ],
            ],
            'formation'  => [
                'name'    => 'Formation',
                'short'   => 'Catechesis and apologetics',
                'summary' => 'Learn what the Church teaches and why, through catechesis and apologetics, so your choices rest on understanding and not only willpower.',
                'body'    => [
                    'Catechesis explains the faith and apologetics gives reasons for it. Together they help you see why attention, rest and self-control matter in Catholic life.',
                    'Formation is for the person who wants to understand the struggle, not only manage it.',
                ],
            ],
            'prayer'     => [
                'name'    => 'Prayer',
                'short'   => 'Guided prayer and devotion',
                'summary' => 'Guided prayer and devotion for the moments when the urge to reach for your phone is strongest.',
                'body'    => [
                    'Prayer gives you something to do with the pause between the urge and the scroll. Short guides fit into a spare minute, and devotions are there for longer quiet.',
                    'Prayer requests and support from others belong here too, with the care that private requests deserve.',
                ],
            ],
            'community'  => [
                'name'    => 'Community',
                'short'   => 'Small groups and study',
                'summary' => 'Small groups and shared study, so accountability is personal and not anonymous.',
                'body'    => [
                    'Breaking a digital habit alone is hard. Small groups give you people to study with, pray with and answer to.',
                    'Groups stay small on purpose, so that everyone is known by name.',
                ],
            ],
            'service'    => [
                'name'    => 'Service',
                'short'   => 'Charitable works',
                'summary' => 'Charitable works that turn your attention from the screen to the people who need it.',
                'body'    => [
                    'Time given to others is time taken back from the feed. Service points your attention outward, to neighbours, parishes and those in need.',
                    'This room collects practical ways to give time and help, near to where you live.',
                ],
            ],
            'publishing' => [
                'name'    => 'Publishing',
                'short'   => 'A small imprint',
                'summary' => 'A small imprint for study guides, devotionals and reflections that support the other rooms.',
                'body'    => [
                    'Publishing is kept small on purpose. The aim is a few well-edited titles that serve the other rooms, not a catalogue.',
                    'Writing here follows the same rule as the rest of FaithGuard: it should help you put the screen down.',
                ],
            ],
            'family'     => [
                'name'    => 'Family',
                'short'   => 'Marriage and home',
                'summary' => 'Support for marriage and home, where screens quietly compete with the people closest to you.',
                'body'    => [
                    'Screens often take attention from spouses and children first. Family looks at marriage and home life, and at habits a household can build together.',
                    'It is written for couples and parents who want fewer devices at the table and more time with each other.',
                ],
            ],
            'pilgrimage' => [
                'name'    => 'Pilgrimage',
                'short'   => 'Walking somewhere holy',
                'summary' => 'Leave the screen behind and walk somewhere holy, alone or with others.',
                'body'    => [
                    'A pilgrimage is a long answer to a small question: where does your attention go? Walking to a holy place, even a nearby one, gives a day without a screen a purpose.',
                    'This room is for planning that walk, whether it is an afternoon or a week.',
                ],
            ],
        ];

        foreach ($data as $slug => $room) {
            $data[$slug]['slug'] = $slug;
        }
        $rooms = $data;
    }

    return $rooms;
}
