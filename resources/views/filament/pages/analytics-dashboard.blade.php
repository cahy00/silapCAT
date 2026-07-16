<x-filament-panels::page>
    <x-filament-widgets::widgets
        :columns="[
            'default' => 1,
            'sm' => 2,
            'xl' => 3,
        ]"
        :widgets="$this->getVisibleWidgets()"
    />
</x-filament-panels::page>
