<?php
declare(strict_types=1);

/** Page registry. Every public page needs a unique title and description. */
function fg_pages(): array
{
    return [
        'home' => [
            'path' => '/',
            'label' => 'Home',
            'title' => 'FaithGuard | Catholic digital wellbeing through Scripture',
            'description' => 'FaithGuard helps people break free from digital addiction through Scripture, prayer, accountability and community support.',
        ],
        'about' => [
            'path' => '/about.php',
            'label' => 'About',
            'title' => 'About FaithGuard | A quieter way online',
            'description' => 'How FaithGuard approaches digital addiction: Scripture first, personal accountability, prayer and community, and what it does not claim to be.',
        ],
        'contact' => [
            'path' => '/contact.php',
            'label' => 'Contact',
            'title' => 'Contact FaithGuard | Questions and support',
            'description' => 'Contact FaithGuard with questions about a room, your account or our policies.',
        ],
        'resources' => [
            'path' => '/resources.php',
            'label' => 'Resources',
            'title' => 'Resources | FaithGuard',
            'description' => 'Free, vetted articles, videos and prayers to support your recovery from digital addiction.',
        ],
        'privacy' => [
            'path' => '/privacy.php',
            'label' => 'Privacy policy',
            'title' => 'Privacy Policy | FaithGuard',
            'description' => 'What personal data FaithGuard collects, why, who can see it, how long it is kept and how to use your rights.',
        ],
        'cookies' => [
            'path' => '/cookies.php',
            'label' => 'Cookie policy',
            'title' => 'Cookie Policy | FaithGuard',
            'description' => 'Which cookies and browser storage FaithGuard uses, what each is for and how to remove them.',
        ],
        'terms' => [
            'path' => '/terms.php',
            'label' => 'Terms and conditions',
            'title' => 'Terms and Conditions | FaithGuard',
            'description' => 'The terms for using FaithGuard, including accounts, acceptable use and the limits of the service.',
        ],
    ];
}

function fg_page_meta(string $key): array
{
    $meta = fg_pages()[$key];
    $meta['key'] = $key;
    $meta['noindex'] = false;
    $meta['breadcrumbs'] = $key === 'home' ? [] : [['Home', '/'], [$meta['label'], null]];
    return $meta;
}

function fg_room_meta(string $slug): array
{
    $room = rooms()[$slug];
    return [
        'key' => 'room-' . $slug,
        'path' => '/rooms/' . $slug,
        'label' => $room['name'],
        'title' => $room['name'] . ': ' . $room['short'] . ' | FaithGuard',
        'description' => $room['summary'],
        'noindex' => false,
        'breadcrumbs' => [['Home', '/'], ['Eight rooms', '/#rooms'], [$room['name'], null]],
    ];
}

/** Pages that must never be indexed (account areas, errors). */
function fg_private_meta(string $title, string $path): array
{
    return [
        'key' => 'private',
        'path' => $path,
        'label' => $title,
        'title' => $title . ' | FaithGuard',
        'description' => 'Private FaithGuard area.',
        'noindex' => true,
        'breadcrumbs' => [],
    ];
}