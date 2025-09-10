<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Resources\GameResource\Pages;
use App\Filament\Resources\GameResource\RelationManagers;
use App\Models\Game;
use App\Models\Provider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use App\Models\AproveSaveSetting;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\TextInput;


class GameResource extends Resource
{
    protected static ?string $model = Game::class;

 //   protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationLabel = 'Todos os Jogos';

    protected static ?string $modelLabel = 'Todos os Jogos';

    /**
     * @dev @anonymous
     * @return bool
     */
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Forms\Components\Section::make('')
                ->schema([
                    Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Select::make('provider_id')
                            ->label('PROVEDOR DO JOGO')
                            ->placeholder('Selecione um provedor')
                            ->relationship(name: 'provider', titleAttribute: 'name')
                            ->options(
                                fn($get) => Provider::query()
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->columnSpanFull(),
                        Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\TextInput::make('game_name')
                                ->label('NOME DO JOGO')
                                ->placeholder('Digite o nome do jogo')
                                ->required()
                                ->maxLength(191),
                            Forms\Components\TextInput::make('views')
                                ->label('VEZES JOGADO')
                                ->required()
                                ->numeric()
                                ->default(0),
                        ])->columns(2),
                        Forms\Components\Section::make('CONFIGURAÇÕES DO JOGO')
                            ->description('O ID do jogo e o código do jogo devem ser iguais para que funcione!')
                            ->schema([
                            Forms\Components\TextInput::make('game_id')
                                ->label('ID DO JOGO')
                                ->placeholder('Digite o ID do jogo')
                                ->required()
                                ->maxLength(191),
                            Forms\Components\TextInput::make('game_code')
                                ->placeholder('Digite o código do jogo')
                                ->label('CÓDIGO DO JOGO')
                                ->required()
                                ->maxLength(191),
                            Forms\Components\Select::make('categories')
                                ->label('CATEGORIA DO JOGO')
                                ->placeholder('Selecione categorias para seu jogo')
                                ->multiple()
                                ->relationship('categories', 'name')
                                ->searchable()
                                ->preload()
                                ->live()
                                ->columnSpanFull()
                            ,
                        ])->columns(3),

                        Forms\Components\Section::make('CONFIGURAÇÕES DE EXIBIÇÃO')
                        ->description('Configurações de exibição do jogo na plataforma.')
                        ->schema([
                            Forms\Components\Toggle::make('show_home')
                                ->label('MOSTRAR NA HOME'),
                            Forms\Components\Toggle::make('is_featured')
                                ->label('DESTAQUE NA HOME'),
                            Forms\Components\Toggle::make('status')
                                ->label('STATUS DO JOGO')
                                ->default(true)
                                ->required(),
                           Forms\Components\Toggle::make('original')
                                ->label('GAME ORIGINAL')
                                ->default(false)
                                ->required(),
                        ])->columns(3),
                        Forms\Components\FileUpload::make('cover')
                            ->label('Capa')
                            ->placeholder('Carregue a capa do jogo')
                            ->image()
                            ->columnSpanFull()
                            ->helperText('Tamanho recomendado para a capa é de 322x322')
                            ->required(),
                    ]),
 


                ])->columns(1)
            ]);
    }

    /**
     * @param Table $table
     * @return Table
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('cover')
                ->label('IMAGEM DO JOGO')
                ,
                Tables\Columns\TextColumn::make('provider.name')
                    ->label('PROVEDOR DO JOGO')
                    ->numeric()
                    ->sortable()
                ,
                Tables\Columns\TextColumn::make('game_name')
                    ->label('NOME DO JOGO')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('show_home')
                    ->afterStateUpdated(function ($record, $state) {
                        if($state == 1) {
                            $record->update(['status' => 1]);
                        }
                    })
                    ->label('MOSTRAR NA HOME'),
                Tables\Columns\ToggleColumn::make('is_featured')
                    ->label('DESTAQUE NA HOME'),
                Tables\Columns\ToggleColumn::make('original')
                    ->label('JOGO ORIGINAL'),
                Tables\Columns\TextColumn::make('views')
                    ->label('VEZES JOGADO')
                    ->icon('heroicon-o-eye')
                    ->numeric()
                    ->formatStateUsing(fn (Game $record): string => \Helper::formatNumber($record->views))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('Provedor')
                    ->relationship('provider', 'name')
                    ->label('Provedor')
                    ->indicator('Provedor'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('Ativar Jogos')
                    ->icon('heroicon-m-check')
                    ->requiresConfirmation()
                    ->action(function($records) {
                        return $records->each->update(['status' => 1]);
                    }),
                Tables\Actions\BulkAction::make('Desativar Jogos')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function($records) {
                        return $records->each(function($record) {
                            $record->update(['status' => 0]);
                        });
                    }),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\GameResource\Pages\ListGames::route('/'),
            'create' => \App\Filament\Admin\Resources\GameResource\Pages\CreateGame::route('/create'),
            'edit' => \App\Filament\Admin\Resources\GameResource\Pages\EditGame::route('/{record}/edit'),
        ];
    }
}
