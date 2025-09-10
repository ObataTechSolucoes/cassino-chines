<?php

namespace App\Filament\Admin\Resources\WithdrawalResource\Pages;

use App\Filament\Admin\Resources\WithdrawalResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Models\Withdrawal;

class ListWithdrawals extends ListRecords
{
    protected static string $resource = WithdrawalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'pendentes' => Tab::make('Pendentes')
                ->modifyQueryUsing(fn($query) => $query->where('status', Withdrawal::STATUS_PENDING)),
            'revisao' => Tab::make('Em Revisão')
                ->modifyQueryUsing(fn($query) => $query->where('status', Withdrawal::STATUS_REVIEW)),
            'aprovados' => Tab::make('Aprovados')
                ->modifyQueryUsing(fn($query) => $query->where('status', Withdrawal::STATUS_APPROVED)),
            'negados' => Tab::make('Negados')
                ->modifyQueryUsing(fn($query) => $query->where('status', Withdrawal::STATUS_DENIED)),
        ];
    }
}
