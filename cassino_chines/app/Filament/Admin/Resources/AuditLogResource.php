<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AuditLogResource\Pages\ListAuditLogs;
use App\Models\AuditLog;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Auditoria';
    protected static ?string $modelLabel = 'Logs de Auditoria';
    protected static ?string $navigationLabel = 'Logs em Tempo Real';
    protected static ?int $navigationSort = 9999;

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->poll('5s')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Data')->since()->sortable(),
                TextColumn::make('user.name')->label('Usuário')->default('sistema')->searchable(),
                BadgeColumn::make('event')->colors([
                    'success' => 'created',
                    'warning' => 'updated',
                    'danger' => 'deleted',
                    'info' => 'action',
                ])->label('Evento'),
                TextColumn::make('module')->label('Módulo')->searchable(),
                TextColumn::make('target_type')->label('Alvo')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('target_id')->label('ID')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('message')->label('Mensagem')->wrap(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')->options([
                    'created' => 'Criado',
                    'updated' => 'Atualizado',
                    'deleted' => 'Excluído',
                    'action' => 'Ação',
                ]),
                Tables\Filters\SelectFilter::make('module')->options(fn () => AuditLog::query()
                    ->select('module')->distinct()->pluck('module', 'module')->filter()->all()),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('De'),
                        Forms\Components\DatePicker::make('until')->label('Até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('ver')
                    ->label('Detalhes')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detalhes do Log')
                    ->modalSubmitAction(false)
                    ->modalContent(fn (AuditLog $record) => view('filament.admin.audit-log-detail', [
                        'record' => $record,
                    ])),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuditLogs::route('/'),
        ];
    }
}
