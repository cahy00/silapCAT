<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Detail Postingan')
                    ->icon('heroicon-o-document-text')
                    ->columnSpan(12)
                    ->schema([
                        Select::make('category_id')
                            ->relationship(name: 'categories', titleAttribute: 'name')
                            ->label('Kategori')
                            ->required(),
                        TextInput::make('title')
                            ->label('Judul')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(callable $set, ?string $state) => $set('slug', Str::slug($state)))
                            ->required()
                            ->autocapitalize('words'),
                        TextInput::make('slug')
                            ->required()
                            ->readOnly(),
                        FileUpload::make('thumbnail')
                            ->required()
                            ->label('Thumbnail Postingan')
                            ->directory('post-thumbnail')
                            ->disk('public_uploads')
                            ->maxSize(2048)
                            ->image()
                            ->helperText('Hanya file gambar (JPG, PNG). Maksimal ukuran 2 MB.')
                            ->acceptedFileTypes(['image/jpg', 'image/jpeg', 'image/png']),
                        RichEditor::make('content')
                            ->label('Konten')
                            ->required()
                            ->columnSpanFull(),
                        Select::make('status')
                            ->label('Status')
                            ->options([0 => 'Draft', 1 => 'Published'])
                            ->default(0)
                            ->required(),
                        Select::make('is_headline')
                            ->label('Postingan Headline')
                            ->options([0 => 'Bukan Headline', 1 => 'Headline'])
                            ->default(0)
                            ->required(),
                    ])->columns(2),
            ]);
    }
}
