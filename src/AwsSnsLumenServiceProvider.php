<?php

namespace Lab123\AwsSns;

/**
 * Kept so existing Lumen `bootstrap/app.php` registrations keep working.
 *
 * AwsSnsServiceProvider detects Lumen-style applications by itself.
 */
class AwsSnsLumenServiceProvider extends AwsSnsServiceProvider
{
}
