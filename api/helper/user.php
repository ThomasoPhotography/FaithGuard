<?php
// Helper functions to normalize user data for templates

if (! function_exists('normalize_user')) {
    /**
     * Normalize a user array returned from FaithGuardRepository into a predictable shape.
     * Ensures keys: id, display_name, email, role, avatar_url, is_admin
     *
     * @param array|null $user
     * @return array|null
     */
    function normalize_user(?array $user): ?array
    {
        if (empty($user) || ! is_array($user)) {
            return null;
        }

        $normalized = [];

        $normalized['id'] = isset($user['id']) ? (int) $user['id'] : null;

        // Prefer full_name, then first_name + last_name, then email
        if (! empty($user['full_name'])) {
            $display = $user['full_name'];
        } elseif (! empty($user['first_name']) || ! empty($user['last_name'])) {
            $display = trim((string) ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
        } else {
            $display = $user['email'] ?? 'User';
        }
        $normalized['display_name'] = htmlspecialchars($display, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $normalized['email']      = $user['email'] ?? null;
        $normalized['avatar_url'] = $user['avatar_url'] ?? null;

        // Derive role from is_admin or role field if present
        if (isset($user['is_admin'])) {
            $normalized['is_admin'] = (bool) ($user['is_admin'] == 1 || $user['is_admin'] === true);
        } else {
            $normalized['is_admin'] = ! empty($user['role']) && strtolower($user['role']) === 'admin';
        }
        $normalized['role'] = $normalized['is_admin'] ? 'admin' : 'user';

        return $normalized;
    }
}
