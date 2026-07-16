<?php

namespace App\Filament\Resources\ShortLinks\Schemas;

use App\Models\ShortLink;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ShortLinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        Section::make('Informasi Utama')
                            ->description('Tentukan URL tujuan dan kode pendek yang ingin Anda gunakan.')
                            ->icon('heroicon-o-link')
                            ->schema([
                                TextInput::make('destination_url')
                                    ->label('URL Tujuan')
                                    ->url()
                                    ->required()
                                    ->placeholder('https://contoh.com/halaman-sangat-panjang')
                                    ->columnSpanFull()
                                    ->prefixIcon('heroicon-m-globe-alt'),
                                TextInput::make('short_code')
                                    ->label('Kode Pendek')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->alphaDash()
                                    ->maxLength(50)
                                    ->placeholder('kode-custom')
                                    ->helperText('Hanya huruf, angka, dash, atau underscore. Kosongkan lalu klik tombol generate untuk membuat kode otomatis.')
                                    ->prefix(fn () => url('/') . '/')
                                    ->suffixAction(
                                        \Filament\Actions\Action::make('generate')
                                            ->icon('heroicon-o-arrow-path')
                                            ->color('primary')
                                            ->action(function (\Filament\Forms\Set $set) {
                                                $set('short_code', ShortLink::generateUniqueCode());
                                            })
                                    ),
                                Textarea::make('description')
                                    ->label('Deskripsi (Opsional)')
                                    ->rows(3)
                                    ->placeholder('Tuliskan catatan singkat mengenai kegunaan link ini...')
                                    ->columnSpanFull(),
                            ])->columns(1),
                    ])
                    ->columnSpan(['sm' => 12, 'md' => 8]),

                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        Section::make('Status & Statistik')
                            ->description('Pengaturan status dan info klik.')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Status Aktif')
                                    ->default(true)
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->helperText('Matikan toggle ini jika ingin menonaktifkan link tanpa menghapusnya.'),
                                Placeholder::make('click_count')
                                    ->label('Total Pengunjung')
                                    ->content(fn (?ShortLink $record): string => $record ? number_format($record->click_count) . ' Kali Diklik' : 'Belum ada data')
                                    ->visibleOn('edit'),
                                Placeholder::make('created_at')
                                    ->label('Dibuat Pada')
                                    ->content(fn (?ShortLink $record): string => $record ? $record->created_at->translatedFormat('d F Y, H:i') : '-')
                                    ->visibleOn('edit'),
                                Placeholder::make('short_url_preview')
                                    ->label('URL Pendek')
                                    ->content(function (?ShortLink $record): HtmlString {
                                        if (! $record) {
                                            return new HtmlString('<span class="text-sm text-gray-400 italic">Tersedia setelah disimpan</span>');
                                        }

                                        $url = url($record->short_code);

                                        return new HtmlString(
                                            '<div class="flex items-center gap-2 mt-1">' .
                                            '<a href="' . $url . '" target="_blank" class="text-sm text-primary-600 hover:text-primary-500 hover:underline font-medium transition">' .
                                            $url . '</a>' .
                                            '<span class="text-gray-400">↗</span>' .
                                            '</div>'
                                        );
                                    })
                                    ->visibleOn('edit'),
                            ]),
                    ])
                    ->columnSpan(['sm' => 12, 'md' => 4]),
            ])->columns(12);
    }
}
