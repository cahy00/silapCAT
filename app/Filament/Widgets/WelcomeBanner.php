<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class WelcomeBanner extends Widget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected string $view = 'filament.widgets.welcome-banner';
}
