<?php

namespace Viviniko\Sale;

use Viviniko\Sale\Console\Commands\SaleTableCommand;
use Viviniko\Sale\Console\Commands\SendOrderRemainderEmails;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class SaleServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
            __DIR__.'/../config/sale.php' => config_path('sale.php'),
        ]);

        // Register commands
        $this->commands('command.sale.table');
        $this->commands(SendOrderRemainderEmails::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/sale.php', 'sale');

        $this->registerRepositories();

        $this->registerOrderService();

        $this->registerCommands();
    }

    /**
     * Register the artisan commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        $this->app->singleton('command.sale.table', function ($app) {
            return new SaleTableCommand($app['files'], $app['composer']);
        });
    }

    protected function registerRepositories()
    {
        $this->app->singleton(
            \Viviniko\Sale\Repositories\Order\OrderRepository::class,
            \Viviniko\Sale\Repositories\Order\EloquentOrder::class
        );
        $this->app->singleton(
            \Viviniko\Sale\Repositories\OrderItem\OrderItemRepository::class,
            \Viviniko\Sale\Repositories\OrderItem\EloquentOrderItem::class
        );
        $this->app->singleton(
            \Viviniko\Sale\Repositories\OrderAddress\OrderAddressRepository::class,
            \Viviniko\Sale\Repositories\OrderAddress\EloquentOrderAddress::class
        );
        $this->app->singleton(
            \Viviniko\Sale\Repositories\OrderShipping\OrderShippingRepository::class,
            \Viviniko\Sale\Repositories\OrderShipping\EloquentOrderShipping::class
        );
        $this->app->singleton(
            \Viviniko\Sale\Repositories\OrderVisitor\OrderVisitorRepository::class,
            \Viviniko\Sale\Repositories\OrderVisitor\EloquentOrderVisitor::class
        );
        $this->app->singleton(
            \Viviniko\Sale\Repositories\OrderStatusHistory\OrderStatusHistoryRepository::class,
            \Viviniko\Sale\Repositories\OrderStatusHistory\EloquentOrderStatusHistory::class
        );
    }

    /**
     * Register the cart service provider.
     *
     * @return void
     */
    protected function registerOrderService()
    {
        $this->app->singleton(\Viviniko\Sale\Services\OrderSNGenerator::class, function ($app) {
            return (new \Viviniko\Sale\Services\Impl\TimeOrderSNGenerator())
                ->setOrderRepository($app[\Viviniko\Sale\Repositories\Order\OrderRepository::class])
                ->prefix($app['config']->get('order.prefix'));
        });
        $this->app->singleton(
            \Viviniko\Sale\Services\OrderService::class,
            \Viviniko\Sale\Services\Impl\OrderServiceImpl::class
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            \Viviniko\Sale\Services\OrderService::class,
        ];
    }
}