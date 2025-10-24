<?php

namespace Bytexr\QueueableBulkActions;

use Bytexr\QueueableBulkActions\Livewire\BulkActionNotification;
use Bytexr\QueueableBulkActions\Livewire\BulkActionNotifications;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class QueueableBulkActionsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'queueable-bulk-actions';

    public static string $viewNamespace = 'queueable-bulk-actions';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->runsMigrations()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('bytexr/filament-queueable-bulk-actions');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        Livewire::component('queueable-bulk-actions.bulk-action-notifications', BulkActionNotifications::class);
        Livewire::component('queueable-bulk-actions.bulk-action-notification', BulkActionNotification::class);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'bytexr/queueable-bulk-actions';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            Css::make('queueable-bulk-actions-styles', __DIR__ . '/../resources/dist/queueable-bulk-actions.css'),
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_bulk_actions_table',
            'create_bulk_action_records_table',
        ];
    }
}
